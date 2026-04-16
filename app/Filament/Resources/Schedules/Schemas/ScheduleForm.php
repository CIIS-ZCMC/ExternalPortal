<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->components([

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
                                // if (! $record) return;
                                // $data = $record->unitPricings?->map(function ($item) {
                                //     return [
                                //         'id' => $item->id,
                                //         'unit' => $item->name,
                                //         'required_qty' => $item->qty,
                                //         'price' => $item->prices?->price ?? 0,
                                //     ];
                                // })->toArray() ?? [];

                                // $component->state($data);
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
            ]);
    }
}
