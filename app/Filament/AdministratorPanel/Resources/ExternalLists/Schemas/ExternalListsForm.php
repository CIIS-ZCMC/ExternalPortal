<?php

namespace App\Filament\AdministratorPanel\Resources\ExternalLists\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ExternalListsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Personal Information')
                    ->description('Primary employee identity and contact records.')
                    ->icon(Heroicon::OutlinedUser)
                    ->schema([
                        TextInput::make('biometric_id')
                            ->label('Biometric ID')
                            ->numeric()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-o-finger-print')
                            ->helperText('Unique biometric ID recognized by attendance devices.'),

                        TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->maxLength(191),

                        TextInput::make('middle_name')
                            ->label('Middle Name')
                            ->maxLength(191),

                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->required()
                            ->maxLength(191),

                        TextInput::make('ext_name')
                            ->label('Suffix / Extension')
                            ->placeholder('e.g. Jr., III, MD')
                            ->maxLength(50),

                        TextInput::make('contact_number')
                            ->label('Contact Number')
                            ->tel()
                            ->prefixIcon('heroicon-o-phone')
                            ->maxLength(191),
                    ])
                    ->columns(3),

                Section::make('Affiliation & Assignment')
                    ->description('Hospital department, sending agency, and institutional designation.')
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->schema([
                        TextInput::make('agency')
                            ->label('Agency / Institution')
                            ->required()
                            ->placeholder('e.g. ZCMC, DOH, PhilHealth, University')
                            ->prefixIcon('heroicon-o-building-office')
                            ->maxLength(191),

                        TextInput::make('position')
                            ->label('Position / Designation')
                            ->required()
                            ->placeholder('e.g. Resident Physician, Nurse Affiliate, IT Consultant')
                            ->prefixIcon('heroicon-o-briefcase')
                            ->maxLength(191),

                        Textarea::make('address')
                            ->label('Address')
                            ->rows(2)
                            ->columnSpanFull()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Portal Account & Security')
                    ->description('Authentication credentials and access verification status.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->schema([
                        TextInput::make('username')
                            ->label('Username')
                            ->prefixIcon('heroicon-o-user')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Managed by system registration.'),

                        TextInput::make('email')
                            ->label('Official Email Address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-o-envelope')
                            ->maxLength(191),

                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At')
                            ->prefixIcon('heroicon-o-calendar')
                            ->helperText('Timestamp when the employee verified their email address.'),
                    ])
                    ->columns(3),
            ]);
    }
}
