<?php

namespace App\Filament\AdministratorPanel\Resources\Administrators\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdministratorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Administrator Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user'),

                TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->fontFamily('mono'),

                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->copyable(),

                TextColumn::make('role')
                    ->label('Access Level')
                    ->badge()
                    ->color(fn($state) => (int)$state === 1 ? 'primary' : 'info')
                    ->icon(fn($state) => (int)$state === 1 ? 'heroicon-o-shield-check' : 'heroicon-o-user')
                    ->formatStateUsing(fn($state) => (int)$state === 1 ? 'Super Administrator' : 'Administrator')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Account Created')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading('No Administrators Found')
            ->emptyStateDescription('Create new administrator accounts using the "New Administrator" button.')
            ->emptyStateIcon('heroicon-o-shield-check')
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil-square'),
                DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ]);
    }
}
