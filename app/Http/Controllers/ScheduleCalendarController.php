<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\ExternalEmployees;
use App\Models\ExternalEmployeeSchedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleCalendarController extends Controller
{
    /**
     * Determine caller permissions and return the target employee model.
     */
    protected function getAuthorizedEmployee(?int $employeeId = null): ?ExternalEmployees
    {
        $admin = Auth::guard('administrator')->user();
        if ($admin) {
            if (!$employeeId) {
                return null;
            }

            $employee = ExternalEmployees::find($employeeId);
            if (!$employee) {
                return null;
            }

            // Check agency authorization if applicable
            if (!empty($admin->assigned_agencies)) {
                $agencies = is_array($admin->assigned_agencies)
                    ? $admin->assigned_agencies
                    : json_decode($admin->assigned_agencies, true);

                if (!empty($agencies) && !in_array($employee->agency, $agencies)) {
                    return null;
                }
            }

            return $employee;
        }

        $user = Auth::guard('external')->user();
        if ($user) {
            // Regular user can only access their own schedules
            if ($employeeId && $employeeId !== $user->id) {
                return null;
            }
            return $user;
        }

        return null;
    }

    /**
     * Fetch calendar schedule events.
     */
    public function getEvents(Request $request): JsonResponse
    {
        $employeeId = $request->query('employee_id');
        $employee = $this->getAuthorizedEmployee($employeeId ? (int) $employeeId : null);

        if (!$employee) {
            return response()->json(['error' => 'Unauthorized or Employee Not Found'], 403);
        }

        $start = $request->query('start');
        $end = $request->query('end');

        $query = ExternalEmployeeSchedule::where('external_employee_id', $employee->id);

        if ($start) {
            $query->where('dtr_date', '>=', Carbon::parse($start)->toDateString());
        }
        if ($end) {
            $query->where('dtr_date', '<=', Carbon::parse($end)->toDateString());
        }

        // Order schedules by date, then by start time so multiple shifts per day stack neatly
        $schedules = $query->orderBy('dtr_date', 'asc')->orderBy('first_in', 'asc')->get();

        // Group consecutive days with identical shifts into continuous multi-day spanning event bars.
        // If there are multiple different shifts on the same day, they remain separate groups and stack cleanly.
        $activeGroups = []; // Array of active continuous groups: key => group
        $completedGroups = [];

        foreach ($schedules as $item) {
            $isShifting = (bool) $item->is_shifting;
            $firstIn = $item->first_in ?: ($isShifting ? '08:00:00' : '08:00:00');
            $secondOut = $item->second_out ?: ($isShifting ? '16:00:00' : '17:00:00');
            $dtrDate = Carbon::parse($item->dtr_date);

            // Shift signature based on shift type and exact duty hours
            $shiftKey = ($isShifting ? 'S' : 'O') . '_' . substr($firstIn, 0, 5) . '_' . substr($secondOut, 0, 5);

            if (
                isset($activeGroups[$shiftKey]) &&
                $activeGroups[$shiftKey]['last_date']->copy()->addDay()->toDateString() === $dtrDate->toDateString()
            ) {
                // Extend active group for this specific shift time across consecutive days
                $activeGroups[$shiftKey]['last_date'] = $dtrDate;
                $activeGroups[$shiftKey]['schedule_ids'][] = $item->id;
            } else {
                // If there was an existing group for this shiftKey that is not consecutive, complete it
                if (isset($activeGroups[$shiftKey])) {
                    $completedGroups[] = $activeGroups[$shiftKey];
                    unset($activeGroups[$shiftKey]);
                }

                $activeGroups[$shiftKey] = [
                    'schedule_id' => $item->id,
                    'schedule_ids' => [$item->id],
                    'start_date' => $dtrDate,
                    'last_date' => $dtrDate,
                    'is_shifting' => $isShifting,
                    'first_in' => $firstIn,
                    'first_out' => $item->first_out,
                    'second_in' => $item->second_in,
                    'second_out' => $secondOut,
                ];
            }
        }

        foreach ($activeGroups as $g) {
            $completedGroups[] = $g;
        }

        $groupedEvents = $completedGroups;

        // Load active shift presets to preserve exact custom preset colors when rendering events
        $presets = \App\Models\ShiftPreset::active()->get();

        $events = collect($groupedEvents)->map(function ($group) use ($presets) {
            $isShifting = $group['is_shifting'];
            $firstIn = $group['first_in'];
            $secondOut = $group['second_out'];

            $startStr = $group['start_date']->toDateString();
            // FullCalendar exclusive allDay end date is last_date + 1 day
            $endStr = $group['last_date']->copy()->addDay()->toDateString();
            $inclusiveEndStr = $group['last_date']->toDateString();

            $timeLabel = Carbon::parse($firstIn)->format('h:i A') . ' - ' . Carbon::parse($secondOut)->format('h:i A');
            $title = $isShifting ? "Shift: {$timeLabel}" : "Office: {$timeLabel}";

            // Check if there is a matching ShiftPreset by time and shifting flag
            $matchedPreset = $presets->first(function ($p) use ($firstIn, $secondOut, $isShifting) {
                return (bool) $p->is_shifting === $isShifting
                    && substr($p->first_in ?? '', 0, 5) === substr($firstIn, 0, 5)
                    && substr($p->second_out ?? '', 0, 5) === substr($secondOut, 0, 5);
            });

            if ($matchedPreset) {
                $bgColor = $matchedPreset->color ?: '#0ea5e9';
                $borderColor = $matchedPreset->border_color ?: $matchedPreset->color ?: '#0284c7';
                if (!empty($matchedPreset->name)) {
                    $title = $matchedPreset->name;
                }
            } else {
                // Fallback default colors based on shift
                $bgColor = '#0ea5e9';
                $borderColor = '#0284c7';

                if ($isShifting) {
                    $startHour = (int) Carbon::parse($firstIn)->format('H');
                    if ($startHour >= 20 || $startHour < 4) {
                        $bgColor = '#6366f1'; // Indigo for Night shift (10pm-6am)
                        $borderColor = '#4f46e5';
                    } elseif ($startHour >= 12) {
                        $bgColor = '#f59e0b'; // Amber for Afternoon / Evening
                        $borderColor = '#d97706';
                    } else {
                        $bgColor = '#0ea5e9'; // Sky for Morning shift
                        $borderColor = '#0284c7';
                    }
                } else {
                    $bgColor = '#059669'; // Emerald for Office hours
                    $borderColor = '#047857';
                }
            }

            return [
                'id' => (string) $group['schedule_id'],
                'title' => $title,
                'start' => $startStr,
                'end' => $endStr,
                'allDay' => true,
                'backgroundColor' => $bgColor,
                'borderColor' => $borderColor,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'schedule_id' => $group['schedule_id'],
                    'schedule_ids' => $group['schedule_ids'],
                    'dtr_date' => $startStr,
                    'end_date' => $inclusiveEndStr,
                    'is_shifting' => $isShifting,
                    'first_in' => $firstIn,
                    'first_out' => $group['first_out'],
                    'second_in' => $group['second_in'],
                    'second_out' => $secondOut,
                    'time_label' => $timeLabel,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Compute [startDateTime, endDateTime] Carbon pair for a shift on a given date.
     * Handles overnight shifts (crosses midnight) and 24-hour shifts.
     */
    protected function getShiftDateTimeRange(string $date, string $firstIn, string $secondOut): array
    {
        $start = Carbon::parse($date . ' ' . $firstIn);
        $end = Carbon::parse($date . ' ' . $secondOut);
        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay(); // Overnight or 24-hr shift
        }
        return [$start, $end];
    }

    /**
     * Check if two datetime intervals [start1, end1] and [start2, end2] overlap.
     * Touching boundaries (e.g. 06:00-14:00 and 14:00-22:00) do NOT overlap.
     */
    protected function intervalsOverlap(Carbon $start1, Carbon $end1, Carbon $start2, Carbon $end2): bool
    {
        return $start1->lessThan($end2) && $start2->lessThan($end1);
    }

    /**
     * Helper to test if two shift time windows on a given date overlap/conflict.
     */
    protected function checkShiftOverlap(string $date, string $in1, string $out1, string $in2, string $out2): bool
    {
        [$start1, $end1] = $this->getShiftDateTimeRange($date, $in1, $out1);
        [$start2, $end2] = $this->getShiftDateTimeRange($date, $in2, $out2);
        return $this->intervalsOverlap($start1, $end1, $start2, $end2);
    }

    /**
     * Find if a proposed shift across [startDate, endDate] conflicts with existing shifts for an employee.
     *
     * @param int $employeeId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string $firstIn
     * @param string $secondOut
     * @param array $excludeIds Schedule IDs to ignore (e.g. schedules currently being moved)
     * @return string|null Formatted error message if conflict exists, or null if clear.
     */
    protected function findConflictingShift(
        int $employeeId,
        Carbon $startDate,
        Carbon $endDate,
        string $firstIn,
        string $secondOut,
        array $excludeIds = []
    ): ?string {
        // Query window [startDate - 1 day, endDate + 1 day] to capture overnight spillover in either direction
        $windowStart = $startDate->copy()->subDay()->toDateString();
        $windowEnd = $endDate->copy()->addDay()->toDateString();

        $existingShifts = ExternalEmployeeSchedule::where('external_employee_id', $employeeId)
            ->whereBetween('dtr_date', [$windowStart, $windowEnd])
            ->when(!empty($excludeIds), fn($q) => $q->whereNotIn('id', $excludeIds))
            ->get();

        if ($existingShifts->isEmpty()) {
            return null;
        }

        $curr = $startDate->copy();
        while ($curr->lessThanOrEqualTo($endDate)) {
            $currDateStr = $curr->toDateString();
            [$candStart, $candEnd] = $this->getShiftDateTimeRange($currDateStr, $firstIn, $secondOut);

            foreach ($existingShifts as $ex) {
                $exIn = $ex->first_in ?: '08:00:00';
                $exOut = $ex->second_out ?: '16:00:00';
                [$exStart, $exEnd] = $this->getShiftDateTimeRange($ex->dtr_date, $exIn, $exOut);

                if ($this->intervalsOverlap($candStart, $candEnd, $exStart, $exEnd)) {
                    $candLabel = Carbon::parse($firstIn)->format('h:i A') . ' - ' . Carbon::parse($secondOut)->format('h:i A');
                    $exLabel = Carbon::parse($exIn)->format('h:i A') . ' - ' . Carbon::parse($exOut)->format('h:i A');
                    return "Conflict on {$currDateStr}: Proposed shift ({$candLabel}) overlaps with existing shift ({$exLabel}) on {$ex->dtr_date}.";
                }
            }

            $curr->addDay();
        }

        return null;
    }

    /**
     * Store a new schedule (e.g., dragged from preset palette or created via date click).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|integer',
            'dtr_date' => 'required|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
            'is_shifting' => 'required|boolean',
            'first_in' => 'required|string',
            'first_out' => 'nullable|string',
            'second_in' => 'nullable|string',
            'second_out' => 'required|string',
            'replace_existing' => 'nullable|boolean',
        ]);

        $employee = $this->getAuthorizedEmployee((int) $validated['employee_id']);
        if (!$employee) {
            return response()->json(['error' => 'Unauthorized or Employee Not Found'], 403);
        }

        // Clean time formats
        $firstIn = Carbon::parse($validated['first_in'])->format('H:i:s');
        $secondOut = Carbon::parse($validated['second_out'])->format('H:i:s');
        $firstOut = !empty($validated['first_out']) ? Carbon::parse($validated['first_out'])->format('H:i:s') : null;
        $secondIn = !empty($validated['second_in']) ? Carbon::parse($validated['second_in'])->format('H:i:s') : null;

        $startDate = Carbon::parse($validated['dtr_date']);
        $endDate = !empty($validated['end_date']) ? Carbon::parse($validated['end_date']) : $startDate;

        $replaceExisting = $request->boolean('replace_existing');

        if ($replaceExisting) {
            // Delete existing schedules for this employee in the date range so new preset takes over
            ExternalEmployeeSchedule::where('external_employee_id', $employee->id)
                ->whereBetween('dtr_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->delete();
        } else {
            // Verify if there are any conflicting overlapping shifts across the requested dates
            $conflict = $this->findConflictingShift($employee->id, $startDate, $endDate, $firstIn, $secondOut);
            if ($conflict) {
                return response()->json(['error' => $conflict], 422);
            }
        }

        $curr = $startDate->copy();
        while ($curr->lessThanOrEqualTo($endDate)) {
            ExternalEmployeeSchedule::updateOrCreate(
                [
                    'external_employee_id' => $employee->id,
                    'dtr_date' => $curr->toDateString(),
                    'first_in' => $firstIn,
                    'second_out' => $secondOut,
                ],
                [
                    'is_shifting' => $validated['is_shifting'] ? 1 : 0,
                    'first_out' => $validated['is_shifting'] ? null : $firstOut,
                    'second_in' => $validated['is_shifting'] ? null : $secondIn,
                ]
            );
            $curr->addDay();
        }

        return response()->json([
            'success' => true,
            'message' => 'Schedule plotted successfully across selected dates.',
        ]);
    }

    /**
     * Update an existing schedule on calendar drag-and-drop or resize with conflict verification.
     */
    public function updateDropResize(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'schedule_ids' => 'nullable|array',
            'schedule_ids.*' => 'integer',
            'employee_id' => 'required|integer',
            'dtr_date' => 'required|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
            'first_in' => 'required|string',
            'second_out' => 'required|string',
        ]);

        $employee = $this->getAuthorizedEmployee((int) $validated['employee_id']);
        if (!$employee) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $schedule = ExternalEmployeeSchedule::where('id', $validated['id'])
            ->where('external_employee_id', $employee->id)
            ->first();

        if (!$schedule) {
            return response()->json(['error' => 'Schedule record not found'], 404);
        }

        $firstIn = Carbon::parse($validated['first_in'])->format('H:i:s');
        $secondOut = Carbon::parse($validated['second_out'])->format('H:i:s');
        $startDate = Carbon::parse($validated['dtr_date']);
        $endDate = !empty($validated['end_date']) ? Carbon::parse($validated['end_date']) : $startDate;

        // Collect all IDs involved if moving or resizing an existing multi-day block
        $idsToUpdate = !empty($validated['schedule_ids']) ? $validated['schedule_ids'] : [$schedule->id];

        // VERIFY CONFLICTS BEFORE COMMITTING MOVE OR RESIZE
        $conflict = $this->findConflictingShift($employee->id, $startDate, $endDate, $firstIn, $secondOut, $idsToUpdate);
        if ($conflict) {
            return response()->json([
                'error' => "Cannot move schedule: " . $conflict,
            ], 422);
        }

        // If dragged across days or resized to a multi-day span
        if (!empty($validated['end_date']) && $endDate->greaterThan($startDate)) {
            // Delete previous schedules in this group so any shortened/trimmed dates or moved dates are cleanly shifted
            ExternalEmployeeSchedule::whereIn('id', $idsToUpdate)
                ->where('external_employee_id', $employee->id)
                ->delete();

            // Re-create / update schedules for the newly moved/resized date range
            $curr = $startDate->copy();
            while ($curr->lessThanOrEqualTo($endDate)) {
                ExternalEmployeeSchedule::create([
                    'external_employee_id' => $employee->id,
                    'dtr_date' => $curr->toDateString(),
                    'is_shifting' => $schedule->is_shifting,
                    'first_in' => $firstIn,
                    'first_out' => $schedule->first_out,
                    'second_in' => $schedule->second_in,
                    'second_out' => $secondOut,
                ]);
                $curr->addDay();
            }

            return response()->json([
                'success' => true,
                'message' => 'Schedule updated successfully.',
            ]);
        }

        // Single day move/resize:
        // If it was previously a multi-day span (multiple schedule_ids), delete all other days in the span
        if (count($idsToUpdate) > 1) {
            ExternalEmployeeSchedule::whereIn('id', $idsToUpdate)
                ->where('id', '!=', $schedule->id)
                ->where('external_employee_id', $employee->id)
                ->delete();
        }

        // Single day move/resize: update the schedule date and times
        $schedule->update([
            'dtr_date' => $validated['dtr_date'],
            'first_in' => $firstIn,
            'second_out' => $secondOut,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully.',
            'schedule' => $schedule,
        ]);
    }

    /**
     * Update schedule details via modal form.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|integer',
            'dtr_date' => 'required|date_format:Y-m-d',
            'is_shifting' => 'required|boolean',
            'first_in' => 'required|string',
            'first_out' => 'nullable|string',
            'second_in' => 'nullable|string',
            'second_out' => 'required|string',
        ]);

        $employee = $this->getAuthorizedEmployee((int) $validated['employee_id']);
        if (!$employee) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $schedule = ExternalEmployeeSchedule::where('id', $id)
            ->where('external_employee_id', $employee->id)
            ->first();

        if (!$schedule) {
            return response()->json(['error' => 'Schedule record not found'], 404);
        }

        $firstIn = Carbon::parse($validated['first_in'])->format('H:i:s');
        $secondOut = Carbon::parse($validated['second_out'])->format('H:i:s');
        $firstOut = !empty($validated['first_out']) ? Carbon::parse($validated['first_out'])->format('H:i:s') : null;
        $secondIn = !empty($validated['second_in']) ? Carbon::parse($validated['second_in'])->format('H:i:s') : null;

        $targetDate = Carbon::parse($validated['dtr_date']);
        $conflict = $this->findConflictingShift($employee->id, $targetDate, $targetDate, $firstIn, $secondOut, [$id]);
        if ($conflict) {
            return response()->json(['error' => $conflict], 422);
        }

        $schedule->update([
            'dtr_date' => $validated['dtr_date'],
            'is_shifting' => $validated['is_shifting'] ? 1 : 0,
            'first_in' => $firstIn,
            'first_out' => $validated['is_shifting'] ? null : $firstOut,
            'second_in' => $validated['is_shifting'] ? null : $secondIn,
            'second_out' => $secondOut,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule details saved.',
            'schedule' => $schedule,
        ]);
    }

    /**
     * Delete a schedule record (or specific date within a schedule span).
     */
    public function destroy(Request $request, ?int $id = null): JsonResponse
    {
        $employeeId = $request->query('employee_id') ?? $request->input('employee_id');
        $employee = $this->getAuthorizedEmployee($employeeId ? (int) $employeeId : null);

        if (!$employee) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $targetDate = $request->query('dtr_date') ?? $request->input('dtr_date');

        // If specific schedule record ID is provided, delete that specific shift
        if ($id) {
            $query = ExternalEmployeeSchedule::where('id', $id)
                ->where('external_employee_id', $employee->id);
            if ($targetDate) {
                $query->where('dtr_date', $targetDate);
            }
            $schedule = $query->first();

            if ($schedule) {
                $schedule->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Schedule shift deleted successfully.',
                ]);
            }
        }

        // Fallback: delete by target date if provided
        if ($targetDate) {
            ExternalEmployeeSchedule::where('external_employee_id', $employee->id)
                ->where('dtr_date', $targetDate)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Schedule on {$targetDate} removed.",
            ]);
        }

        return response()->json(['error' => 'Schedule record not found'], 404);
    }

    /**
     * Clear / remove all schedules for an employee (optionally bounded by month or date range).
     */
    public function clearAll(Request $request): JsonResponse
    {
        $employeeId = $request->input('employee_id');
        $employee = $this->getAuthorizedEmployee($employeeId ? (int) $employeeId : null);

        if (!$employee) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $start = $request->input('start');
        $end = $request->input('end');

        $query = ExternalEmployeeSchedule::where('external_employee_id', $employee->id);

        if ($start) {
            $query->where('dtr_date', '>=', Carbon::parse($start)->toDateString());
        }
        if ($end) {
            $query->where('dtr_date', '<=', Carbon::parse($end)->toDateString());
        }

        $deletedCount = $query->delete();

        $message = ($start && $end)
            ? "Cleared {$deletedCount} scheduled day(s) for the visible period."
            : "All {$deletedCount} duty schedule(s) cleared successfully.";

        return response()->json([
            'success' => true,
            'message' => $message,
            'count' => $deletedCount,
        ]);
    }
}
