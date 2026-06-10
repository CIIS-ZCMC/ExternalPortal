<?php

namespace App\Livewire;

use App\Models\DeviceLogs;
use App\Models\DTR as DailyTimeRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Actions\BulkActionGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\HeaderActionsPosition;
use App\Models\CustomSchedule;
use App\Models\ExternalEmployeeSchedule;
use App\Models\PortalSetting;
use Filament\Tables\Columns\IconColumn;


class AdministratorDTRView extends TableWidget
{

    protected $listeners = ['applyFilter' => 'ApplyFilter'];

    public int $month;
    public int $year;
    public $biometric_id;
    public $external_employee_id;
    public $employee_name;

    public function mount($biometric_id = null, $external_employee_id = null, $employee_name = null)
    {
        $this->biometric_id = $biometric_id;
        $this->external_employee_id = $external_employee_id;
        $this->employee_name = $employee_name;
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function getTableHeading(): string|Htmlable|null
    {
        return $this->employee_name ? new HtmlString("<div style='text-align: right; font-weight: 600; font-size: 1.1rem; color: inherit;'>DTR - {$this->employee_name}</div>") : "";
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    public function ApplyFilter($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function getDtrRecords()
    {
        $devicelogs = DB::select("SELECT * FROM device_logs WHERE biometric_id = ? AND Month(dtr_date) = ? AND YEAR(dtr_date) = ?", [
            $this->biometric_id,
            $this->month,
            $this->year
        ]);

        $schedule = ExternalEmployeeSchedule::where("external_employee_id", $this->external_employee_id)
            ->whereMonth("dtr_date", $this->month)
            ->whereYear("dtr_date", $this->year)
            ->get();

        $dtRecords = collect($devicelogs)
            ->sortBy('date_time')
            ->groupBy('dtr_date')
            ->sortKeys()
            ->map(function ($logs, $date) use ($schedule) {
                $sortedLogs = $logs->sortBy('date_time')
                    ->groupBy(function($log) {
                        return Carbon::parse($log->date_time)->format('H:i');
                    })
                    ->map(function($group) {
                        return $group->first();
                    })
                    ->sortBy('date_time')
                    ->values()
                    ->take(4);

                return [
                    'id'         => $sortedLogs->first()->id ?? null,
                    'dtr_date'   => $date,
                    'first_in'   => $sortedLogs->get(0)->date_time ?? null,
                    'first_out'  => $sortedLogs->get(1)->date_time ?? null,
                    'second_in'  => $sortedLogs->get(2)->date_time ?? null,
                    'second_out' => $sortedLogs->get(3)->date_time ?? null,
                    'has_schedule' => $schedule->where("dtr_date", $date)->count(),
                ];
            })
            ->values();

        $filterData = $this->tableFilters['dtr_date_filter'] ?? [];
        $selectedDate = $filterData['selected_date'] ?? null;

        if ($selectedDate) {
            return $dtRecords->where('dtr_date', $selectedDate);
        }

        return $dtRecords;
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn() => $this->getDtrRecords())
            ->columns([
                TextColumn::make('dtr_date')
                    ->label('Date')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->format('d') . " | " . Carbon::parse($state)->format('D');
                    }),
                TextColumn::make('first_in')->label('Arrival/Departure')->searchable()
                    ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('h:i A') : '-')
                    ->sortable(),
                TextColumn::make('first_out')->label('Arrival/Departure')->searchable()
                    ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('h:i A') : '-')
                    ->sortable(),
                TextColumn::make('second_in')->label('Arrival/Departure')->searchable()
                    ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('h:i A') : '-')
                    ->sortable(),
                TextColumn::make('second_out')->label('Arrival/Departure')->searchable()
                    ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('h:i A') : '-')
                    ->sortable(),
                IconColumn::make('has_schedule')
                    ->label('Has Schedule')
                    ->tooltip(fn($state): ?string => $state ? 'Has schedule' : 'No schedule found, Please process schedule first as this will not be displayed in printouts')
                    ->icon(fn($state): ?string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn($state): ?string => $state ? 'success' : 'danger'),

            ])
            ->filters([
                Filter::make("dtr_date_filter")
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['selected_date'] ?? null) {
                            $indicators[] = 'Date: ' . Carbon::parse($data['selected_date'])->toFormattedDateString();
                        }
                        return $indicators;
                    })
                    ->schema([
                        DatePicker::make("selected_date")
                            ->label("Select Date")
                            ->live(),
                    ])
            ])
            ->emptyStateHeading('No DTR Found')
            ->headerActionsPosition(HeaderActionsPosition::Bottom)
            ->headerActions([
                Action::make("Print_DTR")
                    ->label("Print DTR")
                    ->color("info")
                    ->icon(Heroicon::CalendarDays)
                    ->hidden(fn() => $this->getDtrRecords()->count() == 0)
                    ->action(function () {
                        $url = "https://umis.zcmc.online/generateDtr?" .
                            "biometric_id=[" . $this->biometric_id .
                            "]&monthof=" . $this->month .
                            "&yearof=" . $this->year .
                            "&view=2&frontview=0&whole_month=1&ext=" . $this->external_employee_id;

                        $this->dispatch('open-new-tab', ['url' => $url]);
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
