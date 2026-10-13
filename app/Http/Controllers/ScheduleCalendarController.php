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

        $schedules = $query->orderBy('dtr_date', 'asc')->get();

        // Group consecutive days with identical shifts into continuous multi-day spanning event bars
        $groupedEvents = [];
        $currentGroup = null;

        foreach ($schedules as $item) {
            $isShifting = (bool) $item->is_shifting;
            $firstIn = $item->first_in ?: ($isShifting ? '08:00:00' : '08:00:00');
            $secondOut = $item->second_out ?: ($isShifting ? '16:00:00' : '17:00:00');
            $dtrDate = Carbon::parse($item->dtr_date);

            // Check if this item is strictly consecutive day and same shift
            if (
                $currentGroup &&
                $currentGroup['is_shifting'] === $isShifting &&
                $currentGroup['first_in'] === $firstIn &&
                $currentGroup['second_out'] === $secondOut &&
                $currentGroup['last_date']->copy()->addDay()->toDateString() === $dtrDate->toDateString()
            ) {
                // Extend current continuous group
                $currentGroup['last_date'] = $dtrDate;
                $currentGroup['schedule_ids'][] = $item->id;
            } else {
                if ($currentGroup) {
                    $groupedEvents[] = $currentGroup;
                }
                $currentGroup = [
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
        if ($currentGroup) {
            $groupedEvents[] = $currentGroup;
        }

        $events = collect($groupedEvents)->map(function ($group) {
            $isShifting = $group['is_shifting'];
            $firstIn = $group['first_in'];
            $secondOut = $group['second_out'];

            $startStr = $group['start_date']->toDateString();
            // FullCalendar exclusive allDay end date is last_date + 1 day
            $endStr = $group['last_date']->copy()->addDay()->toDateString();
            $inclusiveEndStr = $group['last_date']->toDateString();

            $timeLabel = Carbon::parse($firstIn)->format('h:i A') . ' - ' . Carbon::parse($secondOut)->format('h:i A');
            $title = $isShifting ? "Shift: {$timeLabel}" : "Office: {$timeLabel}";

            // Colors based on shift
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
                ],
            ];
        });

        return response()->json($events);
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

        $curr = $startDate->copy();
        while ($curr->lessThanOrEqualTo($endDate)) {
            ExternalEmployeeSchedule::updateOrCreate(
                [
                    'external_employee_id' => $employee->id,
                    'dtr_date' => $curr->toDateString(),
                ],
                [
                    'is_shifting' => $validated['is_shifting'] ? 1 : 0,
                    'first_in' => $firstIn,
                    'first_out' => $validated['is_shifting'] ? null : $firstOut,
                    'second_in' => $validated['is_shifting'] ? null : $secondIn,
                    'second_out' => $secondOut,
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
     * Update an existing schedule on calendar drag-and-drop or resize.
     */
    public function updateDropResize(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'required|integer',
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

        // Update the starting day's schedule
        $schedule->update([
            'dtr_date' => $validated['dtr_date'],
            'first_in' => $firstIn,
            'second_out' => $secondOut,
        ]);

        // If extended across multiple days (e.g. from 5 to 8, replicate shift across all days in range)
        if (!empty($validated['end_date'])) {
            $endDate = Carbon::parse($validated['end_date']);
            if ($endDate->greaterThan($startDate)) {
                $curr = $startDate->copy()->addDay();
                while ($curr->lessThanOrEqualTo($endDate)) {
                    ExternalEmployeeSchedule::updateOrCreate(
                        [
                            'external_employee_id' => $employee->id,
                            'dtr_date' => $curr->toDateString(),
                        ],
                        [
                            'is_shifting' => $schedule->is_shifting,
                            'first_in' => $firstIn,
                            'first_out' => $schedule->first_out,
                            'second_in' => $schedule->second_in,
                            'second_out' => $secondOut,
                        ]
                    );
                    $curr->addDay();
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated and extended successfully.',
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

        if ($targetDate) {
            // Delete specific date's schedule
            ExternalEmployeeSchedule::where('external_employee_id', $employee->id)
                ->where('dtr_date', $targetDate)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Schedule on {$targetDate} removed.",
            ]);
        }

        $schedule = ExternalEmployeeSchedule::where('id', $id)
            ->where('external_employee_id', $employee->id)
            ->first();

        if (!$schedule) {
            return response()->json(['error' => 'Schedule record not found'], 404);
        }

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully.',
        ]);
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
