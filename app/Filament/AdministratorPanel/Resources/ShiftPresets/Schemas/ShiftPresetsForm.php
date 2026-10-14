<?php

namespace App\Filament\AdministratorPanel\Resources\ShiftPresets\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;

class ShiftPresetsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Shift Information')
                    ->description('Define the preset label, duration, and shift type.')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        TextInput::make('name')
                            ->label('Shift Name / Title')
                            ->placeholder('e.g. Morning: 06:00 AM - 02:00 PM')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        TextInput::make('code')
                            ->label('Shift Code')
                            ->placeholder('e.g. MORNING_6_2')
                            ->maxLength(100),

                        TextInput::make('badge_label')
                            ->label('Short Badge')
                            ->placeholder('e.g. 8h, Grave, Std, 24h')
                            ->maxLength(50),

                        Checkbox::make('is_shifting')
                            ->label('Is Continuous / Straight Shifting (No lunch break)')
                            ->default(true)
                            ->live()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Working Hours / Duty Times')
                    ->description('Configure scheduled punch in and punch out times.')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        TimePicker::make('first_in')
                            ->label('First In / Shift In')
                            ->required(),

                        TimePicker::make('first_out')
                            ->label('First Out (AM / Lunch Out)')
                            ->hidden(fn ($get) => (bool) $get('is_shifting'))
                            ->required(fn ($get) => ! (bool) $get('is_shifting')),

                        TimePicker::make('second_in')
                            ->label('Second In (PM / Lunch In)')
                            ->hidden(fn ($get) => (bool) $get('is_shifting'))
                            ->required(fn ($get) => ! (bool) $get('is_shifting')),

                        TimePicker::make('second_out')
                            ->label('Second Out / Shift Out')
                            ->required(),

                        TextInput::make('total_hours')
                            ->label('Total Hours')
                            ->placeholder('e.g. 8 or 24')
                            ->numeric()
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Section::make('Display & Appearance')
                    ->description('Customize calendar card badge appearance and sort order.')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        ColorPicker::make('color')
                            ->label('Card Background Color')
                            ->default('#0284c7')
                            ->required(),

                        ColorPicker::make('border_color')
                            ->label('Border / Highlight Color')
                            ->default('#0369a1'),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Active in Preset Palette')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
