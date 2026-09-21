<?php

namespace App\Filament\AdministratorPanel\Resources\Devices\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DevicesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Device Identity & Network')
                    ->description('Specify hardware name and local network address settings.')
                    ->icon(Heroicon::OutlinedServer)
                    ->schema([
                        TextInput::make('device_name')
                            ->label('Device Name')
                            ->required()
                            ->placeholder('e.g. Main Lobby Attendance, HR Biometrics')
                            ->maxLength(191)
                            ->prefixIcon('heroicon-o-cpu-chip'),

                        TextInput::make('ip_address')
                            ->label('Static IP Address')
                            ->required()
                            ->placeholder('192.168.1.100')
                            ->maxLength(191)
                            ->prefixIcon('heroicon-o-globe-alt'),

                        TextInput::make('soap_port')
                            ->label('SOAP Port')
                            ->default('4370')
                            ->placeholder('4370')
                            ->maxLength(10)
                            ->prefixIcon('heroicon-o-hashtag'),

                        TextInput::make('udp_port')
                            ->label('UDP Port')
                            ->default('4370')
                            ->placeholder('4370')
                            ->maxLength(10)
                            ->prefixIcon('heroicon-o-hashtag'),

                        TextInput::make('com_key')
                            ->label('Communication Key')
                            ->default('0')
                            ->placeholder('0')
                            ->maxLength(50)
                            ->prefixIcon('heroicon-o-key'),

                        TextInput::make('serial_number')
                            ->label('Hardware Serial Number')
                            ->placeholder('Optional hardware serial')
                            ->maxLength(191)
                            ->prefixIcon('heroicon-o-identification'),
                    ])
                    ->columns(2),

                Section::make('Device Roles & Operation')
                    ->description('Control whether this terminal syncs attendance logs or enrolls fingerprints.')
                    ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                    ->schema([
                        Checkbox::make('for_attendance')
                            ->label('Enabled for Employee Attendance Tracking')
                            ->helperText('Collects daily punch logs (IN / OUT).')
                            ->default(1),

                        Checkbox::make('is_registration')
                            ->label('Designate as Fingerprint Enrollment Station')
                            ->helperText('Allows uploading and downloading fingerprint templates.')
                            ->default(0),

                        Checkbox::make('is_active')
                            ->label('Device is Active')
                            ->helperText('Inactive devices are excluded from automated polling and synchronization.')
                            ->default(1),
                    ])
                    ->columns(3),
            ]);
    }
}
