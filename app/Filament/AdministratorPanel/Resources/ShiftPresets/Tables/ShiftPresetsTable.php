<?php

namespace App\Filament\AdministratorPanel\Resources\ShiftPresets\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShiftPresetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->alignCenter()
                    ->width('50px'),

                TextColumn::make('name')
                    ->label('Shift Preset Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->code ? "Code: {$record->code}" : null),

                TextColumn::make('is_shifting')
                    ->label('Shift Type')
                    ->badge()
                    ->color(fn ($state) => $state ? 'info' : 'success')
                    ->formatStateUsing(fn ($state) => $state ? 'Shifting Duty' : 'Office Hours')
                    ->sortable(),

                TextColumn::make('first_in')
                    ->label('Time In')
                    ->fontFamily('mono')
                    ->formatStateUsing(function ($state) {
                        if (! $state) return '--:--';
                        try {
                            return \Carbon\Carbon::parse($state)->format('h:i A');
                        } catch (\Throwable $e) {
                            return $state;
                        }
                    }),

                TextColumn::make('second_out')
                    ->label('Time Out')
                    ->fontFamily('mono')
                    ->formatStateUsing(function ($state) {
                        if (! $state) return '--:--';
                        try {
                            return \Carbon\Carbon::parse($state)->format('h:i A');
                        } catch (\Throwable $e) {
                            return $state;
                        }
                    }),

                TextColumn::make('total_hours')
                    ->label('Duration')
                    ->suffix(' hrs')
                    ->alignCenter()
                    ->default('8'),

                TextColumn::make('badge_label')
                    ->label('Badge')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                ColorColumn::make('color')
                    ->label('Card Color')
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([]);
    }
}
