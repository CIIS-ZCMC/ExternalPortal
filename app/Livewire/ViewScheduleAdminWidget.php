<?php

namespace App\Livewire;

use App\Models\ExternalEmployees;
use App\Models\ExternalEmployeeSchedule;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ViewScheduleAdminWidget extends TableWidget
{
    public $biometric_id;

    /**
     * Resolved once in mount() and reused by all callers within the component.
     * Prevents re-querying ExternalEmployees on every table re-render / button click.
     */
    public ?int $external_employee_id = null;

    protected $listeners = ['applyFilter' => 'ApplyFilter'];

    public int $month;
    public int $year;

    public function mount()
    {
        $this->month = now()->month;
        $this->year  = now()->year;

        // Resolve the external employee's PK once so table() and action closures
        // never need to run ExternalEmployees::where(...) again per render.
        if ($this->biometric_id) {
            $this->external_employee_id = ExternalEmployees::where('biometric_id', $this->biometric_id)
                ->value('id');
        }
    }

    public function ApplyFilter($month, $year)
    {
        $this->month = $month;
        $this->year  = $year;
    }

    public function getColumnSpan(): int|string|array
    {
        return "full";
    }

    public function table(Table $table): Table
    {
        // Use the cached external_employee_id instead of re-querying the DB
        // on every render / pagination / button-click.
        $externalEmployeeId = $this->external_employee_id;

        return $table
            ->query(
                fn(): Builder => ExternalEmployeeSchedule::query()
                    ->where('external_employee_id', $externalEmployeeId)
                    ->whereMonth("dtr_date", $this->month)
                    ->whereYear("dtr_date", $this->year)
            )
            ->columns([
                TextColumn::make('dtr_date')
                    ->label('Date & Day')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(function ($state) {
                        $day = \Carbon\Carbon::parse($state)->dayOfWeek;
                        return in_array($day, [0, 6]) ? 'warning' : 'gray';
                    })
                    ->formatStateUsing(function ($state) {
                        return \Carbon\Carbon::parse($state)->format('d M') . ' (' . \Carbon\Carbon::parse($state)->format('D') . ')';
                    }),

                TextColumn::make('is_shifting')
                    ->label('Shift Type')
                    ->badge()
                    ->color(fn($state) => $state ? 'info' : 'gray')
                    ->icon(fn($state) => $state ? 'heroicon-o-arrow-path' : 'heroicon-o-building-office')
                    ->formatStateUsing(fn($state) => $state ? 'Shifting' : 'Office Hours'),

                TextColumn::make('first_in')
                    ->label('AM In')
                    ->fontFamily('mono')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '--:--';
                        try {
                            return \Carbon\Carbon::parse($state)->format('h:i A');
                        } catch (\Throwable $e) {
                            return $state;
                        }
                    }),

                TextColumn::make('first_out')
                    ->label('AM Out')
                    ->fontFamily('mono')
                    ->default('--:--')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '--:--';
                        try {
                            return \Carbon\Carbon::parse($state)->format('h:i A');
                        } catch (\Throwable $e) {
                            return $state;
                        }
                    }),

                TextColumn::make('second_in')
                    ->label('PM In')
                    ->fontFamily('mono')
                    ->default('--:--')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '--:--';
                        try {
                            return \Carbon\Carbon::parse($state)->format('h:i A');
                        } catch (\Throwable $e) {
                            return $state;
                        }
                    }),

                TextColumn::make('second_out')
                    ->label('PM Out')
                    ->fontFamily('mono')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '--:--';
                        try {
                            return \Carbon\Carbon::parse($state)->format('h:i A');
                        } catch (\Throwable $e) {
                            return $state;
                        }
                    }),
            ])
            ->emptyStateHeading('No Duty Schedules Plotted')
            ->emptyStateDescription('Plot this employee\'s schedule for this month using the "Create Schedule" button.')
            ->emptyStateIcon('heroicon-o-calendar')
            ->filters([
                //
            ])
            ->headerActions([

                Action::make('create_schedule')
                    ->label('Create Schedule')
                    ->icon(Heroicon::Plus)
                    ->modalWidth('full')
                    ->schema([
                        Section::make("Plot Schedule")
                            ->description("Define the shift hours to accurately align with Daily Time Records (DTR).")
                            ->icon(Heroicon::Calendar)
                            ->components([
                                Repeater::make('schedule_4entries')
                                    ->label('Time Management')
                                    ->table([
                                        TableColumn::make('Is Shifting'),
                                        TableColumn::make('Time Shifts'),
                                        TableColumn::make('Is Office hrs'),
                                        TableColumn::make('Schedule Date'),
                                        TableColumn::make('First In'),
                                        TableColumn::make('First Out'),
                                        TableColumn::make('Second In'),
                                        TableColumn::make('Second Out'),
                                    ])
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        // reserved for future pre-fill logic
                                    })
                                    ->schema([
                                        Checkbox::make('is_shifting')
                                            ->label("Is Shifting")
                                            ->live()
                                            ->columnSpan(1),
                                        Select::make('time_shift')
                                            ->label("Time Shift")
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
                                                    $set("first_in", $times[0]);
                                                    $set("second_out", $times[1]);
                                                    $set("first_out", null);
                                                    $set("second_in", null);
                                                }
                                            })
                                            ->columnSpan(1),
                                        Checkbox::make('is_normal')
                                            ->label("Is Normal")
                                            ->disabled(fn($get) => $get('is_shifting'))
                                            ->live()
                                            ->afterStateUpdated(function ($set, $get) {
                                                if ($get('is_normal')) {
                                                    $set("first_in", "08:00:00");
                                                    $set("first_out", "12:00:00");
                                                    $set("second_in", "13:00:00");
                                                    $set("second_out", "17:00:00");
                                                } else {
                                                    $set("first_in", null);
                                                    $set("first_out", null);
                                                    $set("second_in", null);
                                                    $set("second_out", null);
                                                }
                                            })
                                            ->columnSpan(1),
                                        DatePicker::make("dtr_date")
                                            ->label("Schedule Date")
                                            ->required(),
                                        TimePicker::make("first_in")
                                            ->label("First In")
                                            ->required(),
                                        TimePicker::make("first_out")
                                            ->label("First Out")
                                            ->hidden(fn($get) => $get('is_shifting'))
                                            ->required(),
                                        TimePicker::make("second_in")
                                            ->label("Second In")
                                            ->hidden(fn($get) => $get('is_shifting'))
                                            ->required(),
                                        TimePicker::make("second_out")
                                            ->label("Second Out")
                                            ->required(),

                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Add Schedule entry')
                                    ->required(),

                            ])
                            ->columnSpanFull()
                    ])->action(function (array $data) {
                        // Use the cached PK — no extra DB query on button click.
                        $employeeExternalId = $this->external_employee_id;

                        if (isset($data['schedule_4entries']) && is_array($data['schedule_4entries'])) {
                            $data['schedule_4entries'] = array_map(function ($item) use ($employeeExternalId) {
                                $item['external_employee_id'] = $employeeExternalId;
                                if (isset($item['is_shifting']) && $item['is_shifting']) {
                                    $item['first_out'] = null;
                                    $item['second_in'] = null;
                                }
                                return $item;
                            }, $data['schedule_4entries']);
                        }

                        foreach ($data['schedule_4entries'] as $entry) {
                            $dbRecord = ExternalEmployeeSchedule::where('external_employee_id', $employeeExternalId)
                                ->where('dtr_date', $entry['dtr_date']);

                            if ($dbRecord->exists()) {
                                Notification::make()
                                    ->title('Duplicate Schedule')
                                    ->body("A schedule for " . $entry['dtr_date'] . " already exists.")
                                    ->danger()
                                    ->send();
                                return $dbRecord->first();
                            }

                            ExternalEmployeeSchedule::create($entry);
                        }
                    })
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->modalHeading('Edit Schedule')
                    ->modalDescription('Are you sure you want to edit this schedule?')
                    ->modalSubmitActionLabel('Save')
                    ->modalCancelActionLabel('Cancel')
                    ->modalWidth('2xl')
                    ->modalIcon('heroicon-o-pencil')
                    ->modalIconColor('primary')
                    ->mountUsing(function ($form, $record) {
                        $form->fill([
                            'is_shifting' => $record->is_shifting,
                            'dtr_date'    => $record->dtr_date,
                            'first_in'    => $record->first_in,
                            'first_out'   => $record->first_out,
                            'second_in'   => $record->second_in,
                            'second_out'  => $record->second_out,
                        ]);
                    })
                    ->schema(function ($record) {
                        return [
                            Checkbox::make('is_shifting')
                                ->label("Is Shifting")
                                ->live()
                                ->columnSpan(1),
                            Select::make('time_shift')
                                ->label("Time Shift")
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
                                        $set("first_in", $times[0]);
                                        $set("second_out", $times[1]);
                                        $set("first_out", null);
                                        $set("second_in", null);
                                    }
                                })
                                ->columnSpan(1),
                            DatePicker::make("dtr_date")
                                ->label("Schedule Date")
                                ->required(),
                            TimePicker::make("first_in")
                                ->label("First In")
                                ->required(),
                            TimePicker::make("first_out")
                                ->label("First Out")
                                ->hidden(fn($get) => $get('is_shifting'))
                                ->required(),
                            TimePicker::make("second_in")
                                ->label("Second In")
                                ->hidden(fn($get) => $get('is_shifting'))
                                ->required(),
                            TimePicker::make("second_out")
                                ->label("Second Out")
                                ->required(),
                        ];
                    })
                    ->action(function ($record, $data) {
                        ExternalEmployeeSchedule::find($record->id)->update([
                            'is_shifting' => $data['is_shifting'],
                            'dtr_date'    => $data['dtr_date'],
                            'first_in'    => $data['first_in'],
                            'first_out'   => $data['first_out'] ?? null,
                            'second_in'   => $data['second_in'] ?? null,
                            'second_out'  => $data['second_out'],
                        ]);
                        Notification::make()
                            ->title('Schedule updated successfully')
                            ->success()
                            ->send();
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
