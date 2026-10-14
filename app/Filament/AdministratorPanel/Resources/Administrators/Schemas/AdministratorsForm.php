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

                \Filament\Schemas\Components\Section::make('Agency & Personnel Access')
                    ->description('Specify which agencies/affiliations this administrator is allowed to view. Leave empty to allow viewing all agencies.')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        \Filament\Forms\Components\Select::make('assigned_agencies')
                            ->label('Accessible Agencies / Affiliations')
                            ->placeholder('Select agencies or leave empty for full access')
                            ->helperText('Select one or more agencies (e.g. Department of Health (DOH), City Government). If none are selected, this administrator will have access to all agencies.')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(function () {
                                $prefilled = [
                                    'Zamboanga City Medical Center (ZCMC)',
                                    'Department of Health (DOH)',
                                    'PhilHealth',
                                    'Food and Drug Administration (FDA)',
                                    'Other Hospital / Medical Institution',
                                    'Department of Education (DepEd)',
                                    'Department of the Interior and Local Government (DILG)',
                                    'Department of Social Welfare and Development (DSWD)',
                                    'Department of Finance (DOF)',
                                    'Department of Budget and Management (DBM)',
                                    'Department of Science and Technology (DOST)',
                                    'Department of Tourism (DOT)',
                                    'Department of Justice (DOJ)',
                                    'Department of Agriculture (DA)',
                                    'Department of Labor and Employment (DOLE)',
                                    'Department of National Defense (DND)',
                                    'Department of Transportation (DOTr)',
                                    'Department of Public Works and Highways (DPWH)',
                                    'Department of Trade and Industry (DTI)',
                                    'Department of Environment and Natural Resources (DENR)',
                                    'Commission on Elections (COMELEC)',
                                    'Commission on Higher Education (CHED)',
                                    'Technical Education and Skills Development Authority (TESDA)',
                                    'Civil Service Commission (CSC)',
                                    'Professional Regulation Commission (PRC)',
                                    'Commission on Audit (COA)',
                                    'Government Service Insurance System (GSIS)',
                                    'Provincial Government',
                                    'City Government',
                                    'Municipal Government',
                                    'Barangay Government',
                                    'Philippine National Police (PNP)',
                                    'Armed Forces of the Philippines (AFP)',
                                    'Other Government Agency',
                                    'Basilan State College',
                                    'Notre Dame of Jolo College',
                                    'Pilar College of Zamboanga City',
                                    'Western Mindanao State University',
                                    'Universidad de Zamboanga',
                                    'Ateneo De Zamboanga University',
                                    'Brent Hospital and Colleges Inc.',
                                ];

                                $dbAgencies = \App\Models\ExternalEmployees::select('agency')
                                    ->distinct()
                                    ->whereNotNull('agency')
                                    ->where('agency', '!=', '')
                                    ->pluck('agency')
                                    ->map(fn($item) => trim($item))
                                    ->filter()
                                    ->toArray();

                                $all = array_values(array_unique(array_merge($prefilled, $dbAgencies)));
                                sort($all, SORT_NATURAL | SORT_FLAG_CASE);

                                return array_combine($all, $all);
                            })
                            ->columnSpanFull(),
                    ]),

                \Filament\Schemas\Components\Section::make('Role & System Access')
                    ->description('Set whether this user is a Super Administrator (full unrestricted access) or Administrator.')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        \Filament\Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options([
                                1 => 'Super Administrator (Full Access to all buttons & features)',
                                2 => 'Administrator (Restricted by assigned permissions below)',
                            ])
                            ->default(2)
                            ->required()
                            ->live()
                            ->helperText('Super Administrators automatically bypass all permission restrictions and have access to all actions and menus.')
                            ->columnSpanFull(),
                    ]),

                \Filament\Schemas\Components\Section::make('Button & Feature Permissions')
                    ->description('Assign specific rights and button permissions to this administrator. (Applies when Role is Administrator).')
                    ->icon('heroicon-o-key')
                    ->schema([
                        \Filament\Forms\Components\CheckboxList::make('permissions')
                            ->label('Action & Button Rights')
                            ->helperText('Select which buttons and actions this administrator is allowed to execute.')
                            ->options([
                                'time_adjustments' => 'Time Adjustments (Add schedule and biometric device logs)',
                                'deactivate'       => 'Deactivate (Deactivate external employee accounts)',
                                'reset_password'   => 'Reset Password (Reset employee login password)',
                                'update_email'     => 'Update Email (Update employee email address)',
                                'edit_profile'     => 'Edit Profile (Edit employee basic information and photo)',
                                'register_device'  => 'Upload User to Device (Push employee biometric template to biometric device)',
                                'manage_devices'   => 'Manage Devices (Access and manage Device configurations)',
                                'manage_shift_presets' => 'Manage Shift Presets (Access and manage Shift Presets)',
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->visible(fn ($get) => (int) $get('role') !== 1),
                        \Filament\Forms\Components\Placeholder::make('superadmin_note')
                            ->label('Permissions Notice')
                            ->content('Super Administrators have full access to all actions, buttons, and settings by default. No permission checklist needed.')
                            ->visible(fn ($get) => (int) $get('role') === 1)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
