<?php

namespace App\Filament\AdministratorPanel\Resources\Administrators\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Hash;

class AdministratorsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Personal Information')
                    ->description('Enter the administrator\'s personal details')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter administrator name'),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('admin@example.com'),
                    ])
                    ->columns(2),

                \Filament\Schemas\Components\Section::make('Account Credentials')
                    ->description('Set up login credentials for the administrator')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Enter username')
                            ->alphaNum()
                            ->prefixIcon('heroicon-o-user'),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn ($context) => $context === 'create')
                            ->minLength(5)
                            ->maxLength(255)
                            ->placeholder('Minimum 5 characters')
                            ->helperText(fn ($context) => $context === 'create' ? 'Minimum 5 characters required' : 'Leave blank to keep current password')
                            ->prefixIcon('heroicon-o-key'),
                    ])
                    ->columns(2),

                Hidden::make('role')
                    ->default(2),
            ]);
    }
}
