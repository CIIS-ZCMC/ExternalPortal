<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
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

                        Checkbox::make('is_shifting')
                            ->label("Is Shifting")
                            // ->afterStateHydrated(function ($component, $state, $record) {
                            //     if (! $record) return;
                            //     $component->state($record->plantilla->first()->is_contract);
                            // })
                            ->live()
                            ->columnSpan(1),


                        Repeater::make('pricings')
                            ->label('Time Management')
                            ->table([
                                TableColumn::make('Schedule'),
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
                                DatePicker::make("date")
                                    ->label("Schedule Date")
                                    ->required(),
                                TimePicker::make("first_in")
                                    ->label("First In")
                                    ->required(),
                                TimePicker::make("first_out")
                                    ->label("First Out")
                                    ->required(),
                                TimePicker::make("second_in")
                                    ->label("Second In")
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
