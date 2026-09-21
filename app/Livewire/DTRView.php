<?php

namespace App\Livewire;

use App\Models\DeviceLogs;
use App\Models\DTR as DailyTimeRecord;
use DB;
use Illuminate\Support\Facades\Http;
use Filament\Actions\BulkActionGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache as CacheFacade;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\HeaderActionsPosition;
use App\Models\CustomSchedule;
use App\Models\ExternalEmployeeSchedule;
use App\Models\PortalSetting;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\IconColumn;
use Filament\Support\Enums\IconSize;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
use App\Helpers\DtrToken;
use Livewire\Attributes\Computed;


class DTRView extends TableWidget
{


    protected $listeners = ['applyFilter' => 'ApplyFilter', 'refresh' => '$refresh'];

    public int $month;
    public int $year;


    public function getTableHeading(): string|Htmlable|null
    {
        return "";
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }


    #[\Livewire\Attributes\On('applyFilter')]
    public function ApplyFilter($month, $year)
    {
        $this->month = (int) $month;
        $this->year = (int) $year;
        $this->resetTable();
        unset($this->dtrRecords); // bust the computed cache on filter change
    }

    public function refreshDtr()
    {
        $biometric_id = Auth::user()?->biometric_id;

        if (!$biometric_id) {
            Notification::make()
                ->title('No Biometric ID')
                ->body('Your account is not linked to an attendance biometric PIN.')
                ->warning()
                ->send();
            return;
        }

        try {
            $apiUrl = config('app.dtr_api_url');
            if (empty($apiUrl)) {
                throw new \Exception('DTR API URL is not configured.');
            }

            $response = Http::timeout(4)->connectTimeout(2)->get(
                "{$apiUrl}/api/dtr/json/{$biometric_id}/{$this->year}/{$this->month}?refresh=1&token=" . DtrToken::generate()
            );

            if ($response->successful()) {
                Notification::make()
                    ->title('Attendance Punches Synced')
                    ->body('Latest biometric attendance records retrieved.')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Attendance Server Notice')
                    ->body('Could not sync latest punches from the attendance server.')
                    ->warning()
                    ->send();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("DTR API refresh error for PIN {$biometric_id}: " . $e->getMessage());
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
        $externalEmployeeId = Auth::guard('external')->id();

        $schedule = ExternalEmployeeSchedule::updateOrCreate(
            [
                'external_employee_id' => $externalEmployeeId,
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

        // No need to hit the external DTR API on a local schedule save —
        // just refresh the table.
        unset($this->dtrRecords);
        $this->dispatch('refresh');
    }

    /**
     * Fetch DTR records from the external API.
     * The #[Computed] attribute caches the result for the lifetime of this
     * render cycle, so multiple callers (->records(), ->hidden()) share one
     * HTTP request instead of each firing their own.
     */
    #[Computed]
    public function getDtrRecords()
    {
        $biometric_id = Auth::user()?->biometric_id;

        if (!$biometric_id) {
            return collect([]);
        }

        $dailyRecords = [];

        try {
            $apiUrl = config('app.dtr_api_url');
            if (!empty($apiUrl)) {
                $response = Http::timeout(4)->connectTimeout(2)->get(
                    "{$apiUrl}/api/dtr/json/{$biometric_id}/{$this->year}/{$this->month}?token=" . DtrToken::generate()
                );

                if ($response->successful()) {
                    $data = $response->json();
                    $dailyRecords = $data['daily_records'] ?? [];
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("DTR API connection error for PIN {$biometric_id}: " . $e->getMessage());
            return collect([]);
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
                    ->tooltip(fn($state): ?string => $state ? 'Schedule is plotted.' : 'No schedule plotted for this day; punches will not be shown on printed DTR.')
                    ->icon(fn($state): ?string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn($state): ?string => $state ? 'success' : 'danger')
                    ->size(IconSize::Small),
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
                            ->label("Filter Specific Date")
                            ->live(),
                    ])
            ])
            ->emptyStateHeading('No Attendance Records Found')
            ->emptyStateDescription('No punches have been recorded for this period yet. If you have punched on a terminal, click "Refresh Punches".')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->headerActions([
                Action::make("Refresh_DTR")
                    ->label("Refresh Punches")
                    ->color("gray")
                    ->icon(Heroicon::ArrowPath)
                    ->action(fn() => $this->refreshDtr()),
                Action::make("Print_DTR")
                    ->label("Print Official DTR")
                    ->color("primary")
                    ->icon(Heroicon::Printer)
                    ->hidden(fn() => $this->getDtrRecords()->count() == 0)
                    ->action(function () {
                        $token = Str::random(16);

                        CacheFacade::put('dtr_download_' . $token, [
                            'biometric_id' => Auth::user()->biometric_id,
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
                    ->label('Manage Schedule')
                    ->icon(Heroicon::Calendar)
                    ->color('primary')
                    ->modalHeading('Manage Schedule')
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
