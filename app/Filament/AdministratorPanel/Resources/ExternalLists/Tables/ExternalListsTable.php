<?php

namespace App\Filament\AdministratorPanel\Resources\ExternalLists\Tables;

use App\Filament\AdministratorPanel\Pages\ViewSchedule;
use App\Filament\AdministratorPanel\Pages\ViewUserDTR;
use App\Helpers\DtrToken;
use App\Http\Controllers\DeviceController;
use App\Models\Biometrics;
use App\Models\DeviceLogs;
use App\Models\Devices;
use App\Models\DTR;
use App\Models\ExternalEmployeeSchedule;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class ExternalListsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->selectable(fn() => auth('administrator')->user()->role === 1)
            ->columns([
                TextColumn::make("is_registered")
                    ->label("Is Registered")
                   
                    ->badge()
                    ->size("10px")
                    ->color(function ($record) {
                        $biometric = Biometrics::where("biometric_id", $record->biometric_id)->first();
                        return $biometric ? ($biometric->biometric === "NOT_YET_REGISTERED" ? "danger" : "success") : "secondary";
                    })

                    ->state(function ($record) {
                        $biometric = Biometrics::where("biometric_id", $record->biometric_id)->first();
                        return $biometric ? ($biometric->biometric === "NOT_YET_REGISTERED" ? "No Biometric Data" : "Registered") : "Not Registered";
                    }),
                TextColumn::make("status")
                    ->label("Status")
                    ->searchable()
                    ->sortable()
                    ->badge()

                    ->size("10px")
                    ->color(function ($record) {
                        if ($record->deleted_at) {
                            return "danger";
                        }

                        $dtr = DTR::where("biometric_id", $record->biometric_id)->first();
                        return $dtr ? "success" : "danger";
                    })
                    ->state(function ($record) {

                        if ($record->deleted_at) {
                            return "INACTIVE";
                        }

                        $dtr = DTR::where("biometric_id", $record->biometric_id)->first();
                        return $dtr ? "ACTIVE" : "INACTIVE";
                    }),

                TextColumn::make("biometric_id")
                    ->label("Biometric ID")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("first_name")
                    ->label("First Name")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("last_name")
                    ->label("Last Name")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("middle_name")
                    ->label("Middle Name")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("position")
                    ->label("Position")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("email")
                    ->label("Email")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("contact_number")
                    ->label("Contact Number")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("agency")
                    ->label("Agency")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("created_at")
                    ->label("Created At")
                    ->since()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("username")
                    ->label("Username")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActionsPosition(RecordActionsPosition::BeforeColumns)
            ->recordActions([

                ActionGroup::make([
                    Action::make('time_adjustments')
                        ->label('Time Adjustments')
                        ->icon('heroicon-o-clock')
                        ->color('primary')
                        ->modalHeading(fn($record) => 'Time Adjustments - ' . $record->name . ($record->biometric_id ? " (Biometric ID: {$record->biometric_id})" : ''))
                        ->modalDescription('Create a schedule and corresponding device logs for this employee on a specific date.')
                        ->modalWidth('2xl')
                        ->modalSubmitActionLabel('Save Adjustment')
                        ->mountUsing(function ($form, $record) {
                            $form->fill([
                                'dtr_date' => now()->format('Y-m-d'),
                                'is_shifting' => false,
                                'is_office_hours' => false,
                                'log_first_in' => true,
                                'log_first_out' => true,
                                'log_second_in' => true,
                                'log_second_out' => true,
                            ]);
                        })
                        ->schema([
                            Section::make('Date & Schedule Configuration')
                                ->schema([
                                    DatePicker::make('dtr_date')
                                        ->label('Schedule Date')
                                        ->required()
                                        ->default(now()->format('Y-m-d')),
                                    Checkbox::make('is_shifting')
                                        ->label('Is Shifting')
                                        ->live()
                                        ->afterStateUpdated(function ($set, $state) {
                                            if ($state) {
                                                $set('is_office_hours', false);
                                                $set('first_out', null);
                                                $set('second_in', null);
                                            }
                                        })
                                        ->columnSpan(1),
                                    Checkbox::make('is_office_hours')
                                        ->label('Office Hours (8:00 AM - 12:00 PM, 1:00 PM - 5:00 PM)')
                                        ->disabled(fn($get) => (bool)$get('is_shifting'))
                                        ->live()
                                        ->afterStateUpdated(function ($set, $get) {
                                            if ($get('is_office_hours')) {
                                                $set('first_in', '08:00:00');
                                                $set('first_out', '12:00:00');
                                                $set('second_in', '13:00:00');
                                                $set('second_out', '17:00:00');
                                            } else {
                                                $set('first_in', null);
                                                $set('first_out', null);
                                                $set('second_in', null);
                                                $set('second_out', null);
                                            }
                                        })
                                        ->columnSpan(1),
                                    Select::make('time_shift')
                                        ->label('Shift Preset')
                                        ->disabled(fn($get) => !(bool)$get('is_shifting'))
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
                                            '11' => '06:00 AM - 10:00 PM',
                                        ])
                                        ->live()
                                        ->afterStateUpdated(function ($set, $get) {
                                            if ($get('time_shift')) {
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
                                                    '11' => ['06:00:00', '22:00:00'],
                                                ];
                                                $times = $shiftMap[$get('time_shift')] ?? ['08:00:00', '16:00:00'];
                                                $set('first_in', $times[0]);
                                                $set('second_out', $times[1]);
                                                $set('first_out', null);
                                                $set('second_in', null);
                                            }
                                        })
                                        ->columnSpan(2),
                                ])
                                ->columns(2),
                            Section::make('Time Entries')
                                ->schema([
                                    TimePicker::make('first_in')
                                        ->label(fn($get) => $get('is_shifting') ? 'First In (Shift Start)' : 'First In (AM In)')
                                        ->required(),
                                    TimePicker::make('first_out')
                                        ->label('First Out (Lunch Out)')
                                        ->hidden(fn($get) => (bool)$get('is_shifting'))
                                        ->required(fn($get) => !(bool)$get('is_shifting')),
                                    TimePicker::make('second_in')
                                        ->label('Second In (Lunch In)')
                                        ->hidden(fn($get) => (bool)$get('is_shifting'))
                                        ->required(fn($get) => !(bool)$get('is_shifting')),
                                    TimePicker::make('second_out')
                                        ->label(fn($get) => $get('is_shifting') ? 'Second Out (Shift End)' : 'Second Out (PM Out)')
                                        ->required(),
                                ])
                                ->columns(2),
                            Section::make('Device Logs Creation')
                                ->description('Select which punches to record as device logs (Device Name: Time Adjustment)')
                                ->schema([
                                    Checkbox::make('log_first_in')
                                        ->label('Create Device Log for First In')
                                        ->default(true),
                                    Checkbox::make('log_first_out')
                                        ->label('Create Device Log for First Out')
                                        ->default(true)
                                        ->hidden(fn($get) => (bool)$get('is_shifting')),
                                    Checkbox::make('log_second_in')
                                        ->label('Create Device Log for Second In')
                                        ->default(true)
                                        ->hidden(fn($get) => (bool)$get('is_shifting')),
                                    Checkbox::make('log_second_out')
                                        ->label('Create Device Log for Second Out')
                                        ->default(true),
                                ])
                                ->columns(2),
                        ])
                        ->action(function ($record, array $data) {
                            if (!$record->biometric_id) {
                                Notification::make()
                                    ->title('Cannot Create Device Logs')
                                    ->body('This employee does not have a Biometric ID assigned. Please assign a Biometric ID first.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $isShifting = (bool)($data['is_shifting'] ?? false);
                            $dtrDate = $data['dtr_date'];

                            // 1. Create or Update External Employee Schedule
                            ExternalEmployeeSchedule::updateOrCreate(
                                [
                                    'external_employee_id' => $record->id,
                                    'dtr_date' => $dtrDate,
                                ],
                                [
                                    'is_shifting' => $isShifting,
                                    'first_in' => $data['first_in'],
                                    'first_out' => $isShifting ? null : ($data['first_out'] ?? null),
                                    'second_in' => $isShifting ? null : ($data['second_in'] ?? null),
                                    'second_out' => $data['second_out'],
                                ]
                            );

                            // 2. Create Device Logs
                            $logsToCreate = [];

                            if (!empty($data['log_first_in']) && !empty($data['first_in'])) {
                                $logsToCreate[] = [
                                    'time' => $data['first_in'],
                                    'date' => $dtrDate,
                                ];
                            }

                            if (!$isShifting && !empty($data['log_first_out']) && !empty($data['first_out'])) {
                                $logsToCreate[] = [
                                    'time' => $data['first_out'],
                                    'date' => $dtrDate,
                                ];
                            }

                            if (!$isShifting && !empty($data['log_second_in']) && !empty($data['second_in'])) {
                                $logsToCreate[] = [
                                    'time' => $data['second_in'],
                                    'date' => $dtrDate,
                                ];
                            }

                            if (!empty($data['log_second_out']) && !empty($data['second_out'])) {
                                $secondOutDate = $dtrDate;
                                if ($isShifting && !empty($data['first_in']) && $data['second_out'] < $data['first_in']) {
                                    $secondOutDate = Carbon::parse($dtrDate)->addDay()->format('Y-m-d');
                                }
                                $logsToCreate[] = [
                                    'time' => $data['second_out'],
                                    'date' => $secondOutDate,
                                ];
                            }

                            $createdLogsCount = 0;
                            foreach ($logsToCreate as $log) {
                                $timeString = strlen($log['time']) === 5 ? $log['time'] . ':00' : $log['time'];
                                $dateTime = "{$log['date']} {$timeString}";

                                DeviceLogs::firstOrCreate(
                                    [
                                        'biometric_id' => (string)$record->biometric_id,
                                        'date_time' => $dateTime,
                                    ],
                                    [
                                        'name' => $record->name,
                                        'dtr_date' => $log['date'],
                                        'status' => 255,
                                        'is_Shifting' => $isShifting ? 1 : 0,
                                        'schedule' => null,
                                        'active' => 1,
                                        'device_name' => 'Time Adjustment',
                                    ]
                                );
                                $createdLogsCount++;
                            }

                            // 3. Attempt DTR refresh if API configured
                            try {
                                $dateObj = Carbon::parse($dtrDate);
                                if (config('app.dtr_api_url')) {
                                    Http::timeout(3)->get(config('app.dtr_api_url') . "/api/dtr/json/{$record->biometric_id}/{$dateObj->year}/{$dateObj->month}?refresh=1&token=" . DtrToken::generate());
                                }
                            } catch (\Throwable $e) {
                                // Silently continue if DTR API is unreachable
                            }

                            Notification::make()
                                ->title('Time Adjustment Saved')
                                ->body("Schedule and {$createdLogsCount} device log(s) processed for {$record->name}.")
                                ->success()
                                ->send();
                        }),
                    Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-user-minus')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Deactivate Employee')
                    ->modalDescription('Are you sure you want to deactivate this employee? They will no longer be able to log in.')
                    ->action(function ($record) {
                        $record->delete();
                    })
                    ->visible(fn($record) => $record->deleted_at === null),
                Action::make('reset_password')
                    ->label('Reset Password')
                    ->icon(Heroicon::LockClosed)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password')
                    ->modalDescription('Are you sure you want to reset this employee password? , Please enter the new password')
                    ->schema([

                        TextInput::make('new_password')
                            ->password()
                            ->maxLength(255),

                    ])
                    ->action(function ($record, $data) {


                        $record->update([
                            'password' => Hash::make($data['new_password']),
                        ]);

                        Notification::make()
                            ->title('Password Reset')
                            ->body('Password has been reset successfully')
                            ->success()
                            ->send();
                    }),
                Action::make('update_email')
                    ->label('Update Email')
                    ->icon(Heroicon::AtSymbol)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Update Email')
                    ->modalDescription('Are you sure you want to update this employee email? , Please enter the new email')
                    ->schema([

                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),

                    ])
                    ->action(function ($record, $data) {


                        $record->update([
                            'email' => $data['email'],
                        ]);

                        Notification::make()
                            ->title('Email Updated')
                            ->body('Email has been updated successfully')
                            ->success()
                            ->send();
                    })
                ])
                ->visible(fn() => auth('administrator')->user()->role === 1),
                Action::make("Print_DTR")
                    ->label("Print DTR")
                    ->icon("heroicon-o-printer")
                    ->schema([
                        Select::make('month')
                            ->options([
                                '01' => 'January',
                                '02' => 'February',
                                '03' => 'March',
                                '04' => 'April',
                                '05' => 'May',
                                '06' => 'June',
                                '07' => 'July',
                                '08' => 'August',
                                '09' => 'September',
                                '10' => 'October',
                                '11' => 'November',
                                '12' => 'December',
                            ])
                            ->required(),
                        Select::make('year')
                            ->options(function () {
                                $years = range(date('Y') - 3, date('Y') + 3);
                                return array_combine($years, $years);
                            })
                            ->required()
                    ])
                    ->modalWidth('md')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->action(function ($record, array $data) {

                        $url = "https://umis.zcmc.online/generateDtr?" .
                            "biometric_id=[" . $record->biometric_id .
                            "]&monthof=" . $data['month'] .
                            "&yearof=" . $data['year'] .
                            "&view=2&frontview=0&whole_month=1&ext=" . $record->id;

                        // Trigger download in the browser
                        return redirect($url);
                    }),

                Action::make("viewSchedule")
                    ->label("View Schedule")
                    ->icon("heroicon-o-eye")
                    ->color("gray")
                    ->url(fn($record): string => ViewSchedule::getUrl([
                        'biometric_id' => $record->biometric_id
                    ]))
                    ->openUrlInNewTab(),
                Action::make("viewDTR")
                    ->label("View DTR")
                    ->icon("heroicon-o-calendar-days")
                    ->color("info")
                    ->url(fn($record): string => ViewUserDTR::getUrl([
                        'biometric_id' => $record->biometric_id,
                        'external_employee_id' => $record->id,
                        'employee_name' => $record->first_name . ' ' . $record->last_name
                    ]))
                    ->openUrlInNewTab(),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make("register")
                        ->label("Upload User to Device")
                        ->icon("heroicon-o-user-plus")
                        ->color("success")
                        ->modalWidth("md")
                        ->schema([

                            Select::make("device_id")
                                ->label("Device")
                                ->options(Devices::all()->pluck('device_name', 'id')->mapWithKeys(function ($name, $id) {
                                    $device = Devices::find($id);
                                    return [$id => "{$name} ({$device->ip_address})"];
                                }))
                                ->required(),

                        ])
                        ->action(function ($records, array $data) {

                            $deviceID = $data['device_id'];
                            $device = Devices::where("is_active", 1)
                                ->where("id", $deviceID)
                                ->first();
                            $tad = DeviceController::Connect($device);
                            if ($tad) {
                                foreach ($records as $record) {
                                    $biometric_id = $record->biometric_id;


                                    $biometricRecord =   Biometrics::firstOrCreate([
                                        'biometric_id' => $biometric_id,
                                    ], [
                                        'name' => $record->name,
                                        'privilege' => 0,
                                        'biometric' => "NOT_YET_REGISTERED",
                                        'name_with_biometric' => "External_" . $record->name
                                    ]);

                                    $user_temp = $tad->get_user_template(['pin' => $biometric_id]);
                                    $utemp = simplexml_load_string($user_temp);

                                    $info = $utemp !== false && isset($utemp->Row->Information)
                                        ? trim((string) $utemp->Row->Information)
                                        : null;

                                    $BIO_User = [];
                                    if ($info !== "No data!") {
                                        foreach ($utemp->Row as $user_Cred) {
                                            $result = [
                                                'Finger_ID' => (string) $user_Cred->FingerID,
                                                'Size'  => (string) $user_Cred->Size,
                                                'Valid' => (string) $user_Cred->Valid,
                                                'Template' => (string) $user_Cred->Template,
                                            ];
                                            $BIO_User[] = $result;
                                        }
                                    }


                                    $added =  $tad->set_user_info([
                                        'pin' => $biometric_id,
                                        'name' => $record->name,
                                        'privilege' => 0
                                    ]);
                                    $biometric_Data = $BIO_User;
                                    if (isset($biometricRecord->biometric)) {
                                        $biometric_Data = json_decode($biometricRecord->biometric, true);
                                        if (!empty($BIO_User)) {
                                            $merged = array_merge($biometric_Data ?? [], $BIO_User);
                                            $biometric_Data = collect(array_values(array_reduce($merged, function ($carry, $item) {
                                                $carry[$item['Finger_ID']] = $item;
                                                return $carry;
                                            }, [])));
                                        }
                                        $biometric_Data = json_decode(json_encode($biometric_Data));
                                    }

                                    if ($added) {
                                        if ($biometric_Data !== null) {
                                            foreach ($biometric_Data as $row) {
                                                $fingerid = $row->Finger_ID;
                                                $size = $row->Size;
                                                $valid = $row->Valid;
                                                $template = $row->Template;
                                                $tad->set_user_template([
                                                    'pin' => $biometric_id,
                                                    'finger_id' => $fingerid,
                                                    'size' => $size,
                                                    'valid' => $valid,
                                                    'template' => $template
                                                ]);
                                            }
                                        }
                                    }

                                    Notification::make()
                                        ->title('User ' . $biometric_id . ' ' . $record->name . ' added successfully')
                                        ->success()
                                        ->send();
                                }
                            }
                        }),

                    BulkAction::make("download")
                        ->label("Download User from Device")
                        ->icon("heroicon-o-arrow-down-tray")
                        ->color("info")
                        ->modalWidth("md")
                        ->schema([

                            Select::make("device_id")
                                ->label("Device")
                                ->options(Devices::all()->pluck('device_name', 'id')->mapWithKeys(function ($name, $id) {
                                    $device = Devices::find($id);
                                    return [$id => "{$name} ({$device->ip_address})"];
                                }))
                                ->required(),

                        ])
                        ->action(function ($records, array $data) {

                            $deviceID = $data['device_id'];
                            $device = Devices::where("is_active", 1)
                                ->where("id", $deviceID)
                                ->first();
                            $tad = DeviceController::Connect($device);
                            if ($tad) {
                                foreach ($records as $record) {
                                    $biometric_id = $record->biometric_id;


                                    $user_temp = $tad->get_user_template(['pin' => $biometric_id]);
                                    $utemp = simplexml_load_string($user_temp);

                                    $info = $utemp !== false && isset($utemp->Row->Information)
                                        ? trim((string) $utemp->Row->Information)
                                        : null;
                                  
                                    $biometricRecord =   Biometrics::firstOrCreate([
                                        'biometric_id' => $biometric_id,
                                    ], [
                                        'name' => $record->name,
                                        'privilege' => 0,
                                        'biometric' => "NOT_YET_REGISTERED",
                                        'name_with_biometric' => "External_" . $record->name
                                    ]);

                                   
                                    if ($info !== "No data!") {
                                        $BIO_User = [];
                                        foreach ($utemp->Row as $user_Cred) {
                                            $result = [
                                                'Finger_ID' => (string) $user_Cred->FingerID,
                                                'Size'  => (string) $user_Cred->Size,
                                                'Valid' => (string) $user_Cred->Valid,
                                                'Template' => (string) $user_Cred->Template,
                                            ];
                                            $BIO_User[] = $result;
                                        }

                                        if ($biometricRecord->biometric !== "NOT_YET_REGISTERED") {
                                            $biometric_Data = json_decode($biometricRecord->biometric, true);
                                            if (!empty($BIO_User)) {
                                                $merged = array_merge($biometric_Data ?? [], $BIO_User);
                                                $biometric_Data = collect(array_values(array_reduce($merged, function ($carry, $item) {
                                                    $carry[$item['Finger_ID']] = $item;
                                                    return $carry;
                                                }, [])));
                                            }
                                            $biometric_Data = json_decode(json_encode($biometric_Data));
                                        } else {
                                            $biometric_Data = $BIO_User;
                                        }
                                        $biometricRecord->update([
                                            'biometric' =>  json_encode($biometric_Data)
                                        ]);
                                    }

                                    // Notification::make()
                                    //     ->title('User ' . $biometric_id . ' ' . $record->name . ' added successfully')
                                    //     ->success()
                                    //     ->send();
                                }
                                Notification::make()
                                    ->title('Biometric Data Synced')
                                    ->success()
                                    ->send();

                                self::UploadBiometricDataToLive($records);
                            }
                        }),
                ]),
            ]);
    }


    public static function UploadBiometricDataToLive($records)
    {

        $devices = Devices::where("is_registration", 0)
            ->where("for_attendance", 0)
            ->get();


        foreach ($devices as $device) {
            $tad = DeviceController::Connect($device);
            if ($tad) {
                foreach ($records as $record) {
                    $biometric_id = $record->biometric_id;


                    $biometricRecord =   Biometrics::firstOrCreate([
                        'biometric_id' => $biometric_id,
                    ], [
                        'name' => $record->name,
                        'privilege' => 0,
                        'biometric' => "NOT_YET_REGISTERED",
                        'name_with_biometric' => "External_" . $record->name
                    ]);

                    if($biometricRecord->biometric == "NOT_YET_REGISTERED"){
                        Notification::make()
                        ->title("Biometric ID : {$biometric_id} skipped")
                        ->body('No biometric data found for this entry.')
                        ->danger()
                        ->send();
                        continue;
                    }

                    $user_temp = $tad->get_user_template(['pin' => $biometric_id]);
                    $utemp = simplexml_load_string($user_temp);

                    $info = $utemp !== false && isset($utemp->Row->Information)
                        ? trim((string) $utemp->Row->Information)
                        : null;

                    $BIO_User = [];
                    if ($info !== "No data!") {
                        foreach ($utemp->Row as $user_Cred) {
                            $result = [
                                'Finger_ID' => (string) $user_Cred->FingerID,
                                'Size'  => (string) $user_Cred->Size,
                                'Valid' => (string) $user_Cred->Valid,
                                'Template' => (string) $user_Cred->Template,
                            ];
                            $BIO_User[] = $result;
                        }
                    }


                    $added =  $tad->set_user_info([
                        'pin' => $biometric_id,
                        'name' => $record->name,
                        'privilege' => 0
                    ]);
                    $biometric_Data = $BIO_User;
                    if (isset($biometricRecord->biometric)) {
                        $biometric_Data = json_decode($biometricRecord->biometric, true);
                        if (!empty($BIO_User)) {
                            $merged = array_merge($biometric_Data ?? [], $BIO_User);
                            $biometric_Data = collect(array_values(array_reduce($merged, function ($carry, $item) {
                                $carry[$item['Finger_ID']] = $item;
                                return $carry;
                            }, [])));
                        }
                        $biometric_Data = json_decode(json_encode($biometric_Data));
                    }

                    if ($added) {
                        if ($biometric_Data !== null) {
                            foreach ($biometric_Data as $row) {
                                $fingerid = $row->Finger_ID;
                                $size = $row->Size;
                                $valid = $row->Valid;
                                $template = $row->Template;
                                $tad->set_user_template([
                                    'pin' => $biometric_id,
                                    'finger_id' => $fingerid,
                                    'size' => $size,
                                    'valid' => $valid,
                                    'template' => $template
                                ]);
                            }
                        }
                    }

                    Notification::make()
                        ->title('User ' . $biometric_id . ' ' . $record->name . ' added successfully to ' . $device->device_name)
                        ->success()
                        ->send();
                }
            }
        }
    }
}
