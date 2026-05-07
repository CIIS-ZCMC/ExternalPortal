<?php

namespace App\Filament\AdministratorPanel\Resources\Devices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use App\Http\Controllers\DeviceController;
use App\Models\Biometrics;
use App\Models\Devices;
use App\Models\ExternalEmployee;
use App\Models\ExternalEmployees;
use Filament\Actions\BulkAction;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;


class DevicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('device_name')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('test_connection')
                        ->label('Find Offline devices')
                        ->color('danger')
                        ->icon(Heroicon::SignalSlash)
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $device = Devices::find($record->id);
                                $tad = DeviceController::Connect($device);
                                if (!$tad) {
                                    Notification::make()
                                        ->title($device->device_name . " - " . $device->ip_address)
                                        ->body('Device is offline')
                                        ->danger()
                                        ->send();
                                }
                            }
                        }),

                    BulkAction::make('upload_users')
                        ->label('Upload Users')
                        ->color('success')
                        ->icon(Heroicon::ArrowUpTray)
                        ->modalWidth("2xl")
                        ->schema([

                            Repeater::make('users')
                                ->hiddenLabel()
                                ->schema([
                                    Checkbox::make("is_new_users")->label('New Users Only')
                                        ->columnSpanFull()
                                        ->live(),
                                    TextInput::make("biometric_id")->label('Biometric ID'),
                                    TextInput::make("name")->label('Name')
                                        ->disabled(fn($get) => $get('is_new_users') ? false : true),

                                ])
                                ->columns(2)
                                ->addActionLabel('Add User')
                                ->reorderable()
                                ->collapsible()
                                //->collapsed()
                                ->itemLabel(fn(array $state): ?string => $state['biometric_id'] ?? null)
                                ->columnSpanFull(),
                        ])
                        ->action(function ($records, $data) {

                            $ipAddressPluck = $records->pluck('ip_address')->toArray();
                            $devices = Devices::where("is_active", 1)
                                ->whereIn("ip_address", $ipAddressPluck)
                                ->get();



                            // dd([
                            //     "users" => json_encode($data['users']),
                            //     "device_id" => $device->id
                            // ]);

                            foreach ($devices as $device) {

                                $tad = DeviceController::Connect($device);
                                if ($tad) {

                                    $biometrics = $data['users'];

                                    foreach ($biometrics as $key => $emp) {




                                        if (!isset($emp['name'])) {
                                            $biometric = Biometrics::whereIn('biometric_id', [$emp['biometric_id']])->first();
                                            if ($biometric) {
                                                $emp['name'] = $biometric->name;
                                                $emp['biometric'] = $biometric->biometric;
                                                $emp['biometric_id'] = $biometric->biometric_id;
                                            } else {
                                                $external = ExternalEmployees::where("biometric_id", $emp['biometric_id'])->first();
                                                if ($external) {
                                                    $emp['name'] = $external->name;
                                                    $emp['biometric'] = "EXTERNAL";
                                                    $emp['biometric_id'] = $external->biometric_id;
                                                }
                                            }
                                        }

                                        Biometrics::firstOrCreate([
                                            'biometric_id' => $emp['biometric_id'],
                                        ], [
                                            'name' => $emp['name'],
                                            'privilege' => 0,
                                            'biometric' => "NOT_YET_REGISTERED",
                                            'name_with_biometric' => "External_" . $emp['name']
                                        ]);


                                        $user_temp = $tad->get_user_template(['pin' => $emp['biometric_id']]);
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
                                            'pin' => $emp['biometric_id'],
                                            'name' => $emp['name'],
                                            'privilege' => 0
                                        ]);
                                        $biometric_Data = $BIO_User;
                                        if (isset($emp['biometric'])) {
                                            $biometric_Data = json_decode($emp['biometric'], true);
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
                                                        'pin' => $emp['biometric_id'],
                                                        'finger_id' => $fingerid,
                                                        'size' => $size,
                                                        'valid' => $valid,
                                                        'template' => $template
                                                    ]);
                                                }
                                            }
                                            Notification::make()
                                                ->title('User ' . $emp['biometric_id'] . ' ' . $emp['name'] . ' added successfully')
                                                ->success()
                                                ->send();
                                        } else {
                                            Notification::make()
                                                ->title('Failed to add user ' . $emp['biometric_id'] . ' ' . $emp['name'])
                                                ->danger()
                                                ->send();
                                        }
                                    }
                                }
                            }
                        }),
                    BulkAction::make('download_users')
                        ->label('Download Users')
                        ->color('info')
                        ->icon(Heroicon::ArrowDownTray)
                        ->modalWidth("md")
                        ->schema([
                            Repeater::make('users')
                                ->hiddenLabel()
                                ->schema([
                                    TextInput::make("biometric_id")->label('Biometric ID'),
                                ])
                                ->columns(1)
                                ->addActionLabel('Add User')
                                ->reorderable()
                                ->collapsible()
                                //->collapsed()
                                ->itemLabel(fn(array $state): ?string => $state['biometric_id'] ?? null)
                                ->columnSpanFull(),
                        ])
                        ->action(function ($records, $data) {

                            $ipAddressPluck = $records->pluck('ip_address')->toArray();
                            $devices = Devices::where("is_active", 1)
                                ->whereIn("ip_address", $ipAddressPluck)
                                ->get();

                            $biometrics = Biometrics::whereIn("biometric_id", array_column($data['users'], 'biometric_id'))->get();




                            foreach ($devices as $device) {
                                $tad = DeviceController::Connect($device);
                                if ($tad) {

                                    foreach ($biometrics as $key => $emp) {
                                        $user_temp = $tad->get_user_template(['pin' => $emp->biometric_id]);
                                        $utemp = simplexml_load_string($user_temp);


                                        $info = $utemp !== false && isset($utemp->Row->Information)
                                            ? trim((string) $utemp->Row->Information)
                                            : null;

                                        if (!isset($summary[$device->ip_address])) {
                                            $summary[$device->ip_address] = [
                                                'processed_count' => 0,
                                                'unprocessed_count' => 0,
                                            ];
                                        }
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

                                            if ($emp->biometric !== "NOT_YET_REGISTERED") {
                                                $biometric_Data = json_decode($emp->biometric, true);
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
                                            $emp->update([
                                                'biometric' =>  json_encode($biometric_Data)
                                            ]);
                                        }
                                    }
                                }
                            }
                            Notification::make()
                                ->title('Biometric Data Synced')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
}
