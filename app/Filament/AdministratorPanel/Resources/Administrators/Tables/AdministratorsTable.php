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

                TextColumn::make('assigned_agencies')
                    ->label('Assigned Agencies')
                    ->badge()
                    ->color(fn($state) => empty($state) ? 'gray' : 'primary')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'All Agencies';
                        }
                        return is_array($state) ? implode(', ', $state) : $state;
                    })
                    ->wrap(),

                TextColumn::make('permissions')
                    ->label('Assigned Permissions')
                    ->badge()
                    ->color(fn($record) => (int)$record->role === 1 ? 'success' : 'info')
                    ->formatStateUsing(function ($state, $record) {
                        if ((int) $record->role === 1) {
                            return 'All Permissions (Super Admin)';
                        }

                        $permissions = $state;
                        if (is_string($permissions)) {
                            $permissions = json_decode($permissions, true);
                        }

                        if (empty($permissions) || !is_array($permissions)) {
                            return 'None (View Only)';
                        }

                        $labels = [
                            'time_adjustments'     => 'Time Adjustments',
                            'deactivate'           => 'Deactivate',
                            'reset_password'       => 'Reset Password',
                            'update_email'         => 'Update Email',
                            'edit_profile'         => 'Edit Profile',
                            'register_device'      => 'Upload to Device',
                            'manage_devices'       => 'Devices',
                            'manage_shift_presets' => 'Shift Presets',
                        ];

                        $named = array_map(fn($p) => $labels[$p] ?? $p, $permissions);
                        return $named;
                    })
                    ->wrap(),

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
