<?php

namespace App\Livewire;

use App\Models\DeviceLogs;
use App\Models\DTR as DailyTimeRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache as CacheFacade;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Filament\Actions\BulkActionGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TimePicker;
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
use Filament\Notifications\Notification;


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

    public function refreshDtr()
    {
        Http::get(config('app.dtr_api_url') . "/api/dtr/json/{$this->biometric_id}/{$this->year}/{$this->month}?refresh=1");
    }

    public function createSchedule($data)
    {
        $schedule = ExternalEmployeeSchedule::updateOrCreate(
            [
                'external_employee_id' => $this->external_employee_id,
                'dtr_date' => $data['dtr_date'],
            ],
            [
                'is_shifting' => $data['is_shifting'] ?? false,
                'first_in' => $data['first_in'],
                'first_out' => $data['first_out'] ?? null,
                'second_in' => $data['second_in'] ?? null,
                'second_out' => $data['second_out'],
            ]
        );

        Notification::make()
            ->title($schedule->wasRecentlyCreated ? 'Schedule created successfully' : 'Schedule updated successfully')
            ->success()
            ->send();
    }

    public function getDtrRecords()
    {
        $url = config('app.dtr_api_url') . "/api/dtr/json/{$this->biometric_id}/{$this->year}/{$this->month}";

        $response = Http::get($url);

        if (!$response->successful()) {
            return collect([]);
        }

     //   dd($response->json(),"{$this->biometric_id}/{$this->year}/{$this->month}");

        $data = $response->json();
        $dailyRecords = $data['daily_records'] ?? [];

        $dtRecords = collect($dailyRecords)
            ->map(function ($record) {
                return [
                    'id'          => $record['dtr_date'],
                    'dtr_date'    => $record['dtr_date'],
                    'first_in'    => $record['first_in'],
                    'first_out'   => $record['first_out'],
                    'second_in'   => $record['second_in'],
                    'second_out'  => $record['second_out'],
                    'has_schedule' => count($record['has_schedule'] ?? []),
                    'data'        => $record['data'] ?? [],
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
                    ->formatStateUsing(fn($state) => $state ?: '-')
                    ->sortable(),
                TextColumn::make('first_out')->label('Arrival/Departure')->searchable()
                    ->formatStateUsing(fn($state) => $state ?: '-')
                    ->sortable(),
                TextColumn::make('second_in')->label('Arrival/Departure')->searchable()
                    ->formatStateUsing(fn($state) => $state ?: '-')
                    ->sortable(),
                TextColumn::make('second_out')->label('Arrival/Departure')->searchable()
                    ->formatStateUsing(fn($state) => $state ?: '-')
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
                Action::make("Refresh_DTR")
                    ->label("Refresh")
                    ->color("warning")
                    ->icon(Heroicon::ArrowPath)
                    ->action(fn() => $this->refreshDtr()),
                Action::make("Print_DTR")
                    ->label("Print DTR")
                    ->color("info")
                    ->icon(Heroicon::CalendarDays)
                    ->hidden(fn() => $this->getDtrRecords()->count() == 0)
                    ->action(function () {
                        $token = Str::random(16);

                        CacheFacade::put('dtr_download_' . $token, [
                            'biometric_id' => $this->biometric_id,
                            'year' => $this->year,
                            'month' => $this->month,
                        ], now()->addMinutes(5));

                        $url = route('dtr.download', ['token' => $token]);

                        $this->dispatch('open-new-tab', ['url' => $url]);
                    }),
            ])
            ->recordActions([
                Action::make('view_logs')
                    ->label(function ($record) {
                        $count = count($record['data'] ?? []);
                        return $count > 0 ? "Logs ({$count})" : 'Logs';
                    })
                    ->icon(Heroicon::DocumentText)
                    ->color(function ($record): string {
                        $count = count($record['data'] ?? []);
                        return $count > 0 ? 'success' : 'gray';
                    })
                    ->modalHeading(fn($record) => 'Device Logs - ' . Carbon::parse($record['dtr_date'])->format('M d, Y'))
                    ->modalSubmitAction(false)
                    ->modalContent(fn($record) => view('filament.modals.dtr-logs', ['logs' => collect($record['data'] ?? []), 'date' => $record['dtr_date']])),
                Action::make('create_schedule')
                    ->label('Create Schedule')
                    ->icon(Heroicon::Calendar)
                    ->color('primary')
                    ->modalHeading('Create Schedule')
                    ->modalSubmitActionLabel('Save')
                    ->mountUsing(function ($form, $record) {
                        $form->fill([
                            'dtr_date' => $record['dtr_date'],
                        ]);
                    })
                    ->schema(function () {
                        return [
                            Checkbox::make('is_shifting')
                                ->label('Is Shifting')
                                ->live()
                                ->columnSpan(1),
                            Checkbox::make('is_office_hours')
                                ->label('Office hours')
                                ->live()
                                ->afterStateUpdated(function ($set, $get) {
                                    if ($get('is_office_hours')) {
                                        $set('first_in', '08:00:00');
                                        $set('first_out', '12:00:00');
                                        $set('second_in', '13:00:00');
                                        $set('second_out', '17:00:00');
                                    }
                                })
                                ->columnSpan(1),
                            Select::make('time_shift')
                                ->label('Time Shift')
                                ->disabled(fn($get) => !$get('is_shifting'))
                                ->options([
                                    '1' => '10:00 AM - 06:00 PM',
                                    '2' => '02:00 PM - 10:00 PM',
                                    '3' => '10:00 PM - 06:00 AM',
                                    '4' => '06:00 AM - 02:00 PM',
                                    '5' => '08:00 AM - 04:00 PM',
                                    '6' => '08:00 AM - 08:00 AM',
                                    '7' => '08:00 AM - 12:00 PM',
                                    '8' => '01:00 PM - 05:00 PM',
                                    '9' => '07:00 AM - 07:00 AM',
                                    '10' => '03:00 PM - 07:00 AM',
                                    '11' => '06:00 AM - 10:00 PM',
                                ])
                                ->live()
                                ->afterStateUpdated(function ($set, $get) {
                                    if ($get('time_shift')) {
                                        $shift = $get('time_shift');
                                        $shiftMap = [
                                            '1' => ['10:00:00', '18:00:00'],
                                            '2' => ['14:00:00', '22:00:00'],
                                            '3' => ['22:00:00', '06:00:00'],
                                            '4' => ['06:00:00', '14:00:00'],
                                            '5' => ['08:00:00', '16:00:00'],
                                            '6' => ['08:00:00', '08:00:00'],
                                            '7' => ['08:00:00', '12:00:00'],
                                            '8' => ['13:00:00', '17:00:00'],
                                            '9' => ['07:00:00', '07:00:00'],
                                            '10' => ['15:00:00', '23:00:00'],
                                            '11' => ['06:00:00', '22:00:00'],
                                        ];
                                        $times = $shiftMap[$shift] ?? ['08:00:00', '16:00:00'];
                                        $set('first_in', $times[0]);
                                        $set('second_out', $times[1]);
                                        $set('first_out', null);
                                        $set('second_in', null);
                                    }
                                })
                                ->columnSpan(1),
                            DatePicker::make('dtr_date')
                                ->label('Schedule Date')
                                ->required(),
                            TimePicker::make('first_in')
                                ->label('First In')
                                ->required(),
                            TimePicker::make('first_out')
                                ->label('First Out')
                                ->hidden(fn($get) => $get('is_shifting'))
                                ->required(),
                            TimePicker::make('second_in')
                                ->label('Second In')
                                ->hidden(fn($get) => $get('is_shifting'))
                                ->required(),
                            TimePicker::make('second_out')
                                ->label('Second Out')
                                ->required(),
                        ];
                    })
                    ->action(function ($data) {
                        $this->createSchedule($data);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
