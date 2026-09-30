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
use Filament\Support\Enums\IconSize;
use Filament\Notifications\Notification;
use App\Helpers\DtrToken;
use Livewire\Attributes\Computed;


class AdministratorDTRView extends TableWidget
{

    protected $listeners = ['applyFilter' => 'ApplyFilter', 'refresh' => '$refresh'];

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
        if (!$this->employee_name) {
            return "";
        }

        return new HtmlString("
            <div class='flex items-center gap-3 py-0.5'>
                <span class='font-bold text-base text-slate-900 dark:text-white'>{$this->employee_name}</span>
                <span class='inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700'>
                    Biometric ID: {$this->biometric_id}
                </span>
            </div>
        ");
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    public function ApplyFilter($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
        unset($this->dtrRecords); // bust the computed cache on filter change
    }

    public function refreshDtr()
    {
        if (!$this->biometric_id) {
            Notification::make()
                ->title('No Biometric ID')
                ->body('This personnel record has no biometric PIN assigned.')
                ->warning()
                ->send();
            return;
        }

        try {
            $apiUrl = config('app.dtr_api_url');
            if (empty($apiUrl)) {
                throw new \Exception('DTR API URL is not configured.');
            }

            $response = Http::timeout(10)->connectTimeout(6)->retry(2, 200)->get(
                "{$apiUrl}/api/dtr/json/{$this->biometric_id}/{$this->year}/{$this->month}?refresh=1&token=" . DtrToken::generate()
            );

            if ($response->successful()) {
                $data = $response->json();
                $cacheKey = "dtr_records_{$this->biometric_id}_{$this->year}_{$this->month}";
                CacheFacade::put($cacheKey, $data['daily_records'] ?? [], now()->addMinutes(5));

                Notification::make()
                    ->title('Attendance Punches Synced')
                    ->body('Latest biometric records successfully retrieved.')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Attendance Server Notice')
                    ->body('Server returned HTTP status ' . $response->status() . '.')
                    ->warning()
                    ->send();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Administrator DTR API refresh error: " . $e->getMessage());
            Notification::make()
                ->title('Attendance Server Offline')
                ->body('Unable to connect to the attendance service (host unreachable). Please try again later.')
                ->warning()
                ->send();
        }

        unset($this->dtrRecords); // bust computed cache so re-render fetches fresh data
        $this->dispatch('refresh');
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

        // Invalidate cached records so the freshly saved schedule is reflected
        if ($this->biometric_id) {
            CacheFacade::forget("dtr_records_{$this->biometric_id}_{$this->year}_{$this->month}");
        }

        unset($this->dtrRecords);
        $this->dispatch('refresh');
    }

    /**
     * Fetch DTR records from the external API or cache.
     * The #[Computed] attribute caches the result for the lifetime of this
     * render cycle, while CacheFacade caches for 5 minutes across Livewire requests.
     */
    #[Computed]
    public function getDtrRecords()
    {
        if (!$this->biometric_id) {
            return collect([]);
        }

        $cacheKey = "dtr_records_{$this->biometric_id}_{$this->year}_{$this->month}";
        $dailyRecords = CacheFacade::get($cacheKey);

        if ($dailyRecords === null) {
            $dailyRecords = [];

            try {
                $apiUrl = config('app.dtr_api_url');
                if (!empty($apiUrl)) {
                    $url = "{$apiUrl}/api/dtr/json/{$this->biometric_id}/{$this->year}/{$this->month}?token=" . DtrToken::generate();
                    $response = Http::timeout(10)->connectTimeout(6)->retry(2, 200)->get($url);

                    if ($response->successful()) {
                        $data = $response->json();
                        $dailyRecords = $data['daily_records'] ?? [];
                        CacheFacade::put($cacheKey, $dailyRecords, now()->addMinutes(5));
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Administrator DTR API connection error for PIN {$this->biometric_id}: " . $e->getMessage());
                return collect([]);
            }
        }

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
                    ->label('Date & Day')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(function ($state) {
                        $day = Carbon::parse($state)->dayOfWeek;
                        return in_array($day, [0, 6]) ? 'warning' : 'gray';
                    })
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->format('d M') . ' (' . Carbon::parse($state)->format('D') . ')';
                    }),

                TextColumn::make('first_in')
                    ->label('AM In')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '--:--';
                        try {
                            return Carbon::parse($state)->format('h:i A');
                        } catch (\Throwable $e) {
                            return $state;
                        }
                    }),

                TextColumn::make('first_out')
                    ->label('AM Out')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '--:--';
                        try {
                            return Carbon::parse($state)->format('h:i A');
                        } catch (\Throwable $e) {
                            return $state;
                        }
                    }),

                TextColumn::make('second_in')
                    ->label('PM In')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '--:--';
                        try {
                            return Carbon::parse($state)->format('h:i A');
                        } catch (\Throwable $e) {
                            return $state;
                        }
                    }),

                TextColumn::make('second_out')
                    ->label('PM Out')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '--:--';
                        try {
                            return Carbon::parse($state)->format('h:i A');
                        } catch (\Throwable $e) {
                            return $state;
                        }
                    }),

                IconColumn::make('has_schedule')
                    ->label('Schedule')
                    ->tooltip(fn($state): ?string => $state ? 'Official duty schedule is plotted.' : 'No schedule plotted for this day; punches will not appear on formal printouts.')
                    ->icon(fn($state): ?string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn($state): ?string => $state ? 'success' : 'danger')
                    ->size(IconSize::Small),
            ])
            ->emptyStateHeading('No Attendance Records Found')
            ->emptyStateDescription('No device punches were found for this period. Click "Refresh" to query the live attendance server.')
            ->emptyStateIcon('heroicon-o-calendar-days')
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
                        // Use a random token (same as DTRView) to avoid cache key
                        // collisions when printing within the same clock-hour.
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
                    ->modalHeading(fn($record) => 'Device Logs' . (!empty($record['dtr_date']) ? ' - ' . Carbon::parse($record['dtr_date'])->format('M d, Y') : ''))
                    ->modalSubmitAction(false)
                    ->modalContent(fn($record) => view('filament.modals.dtr-logs', [
                        'logs' => collect($record['data'] ?? []),
                        'date' => $record['dtr_date'] ?? null,
                    ])),
                Action::make('create_schedule')
                    ->label('Create Schedule')
                    ->icon(Heroicon::Calendar)
                    ->color('primary')
                    ->modalHeading('Create Schedule')
                    ->modalSubmitActionLabel('Save')
                    ->mountUsing(function ($form, $record) {
                        if (!$record) {
                            Notification::make()
                                ->title('Attendance Record Unavailable')
                                ->body('Could not load details for this day. Please refresh punches and try again.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $form->fill([
                            'dtr_date' => $record['dtr_date'] ?? null,
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
