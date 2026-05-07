<?php

namespace App\Filament\AdministratorPanel\Resources\ExternalLists\Tables;

use App\Filament\AdministratorPanel\Pages\ViewSchedule;
use App\Http\Controllers\DeviceController;
use App\Models\Biometrics;
use App\Models\Devices;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\DTR;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Enums\RecordActionsPosition;

class ExternalListsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
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
                                    $biometricRecord =   Biometrics::where('biometric_id', $biometric_id)->first();
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
