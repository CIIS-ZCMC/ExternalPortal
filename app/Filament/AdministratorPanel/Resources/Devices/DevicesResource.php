<?php

namespace App\Filament\AdministratorPanel\Resources\Devices;

use App\Filament\AdministratorPanel\Resources\Devices\Pages\CreateDevices;
use App\Filament\AdministratorPanel\Resources\Devices\Pages\EditDevices;
use App\Filament\AdministratorPanel\Resources\Devices\Pages\ListDevices;
use App\Filament\AdministratorPanel\Resources\Devices\Schemas\DevicesForm;
use App\Filament\AdministratorPanel\Resources\Devices\Tables\DevicesTable;
use App\Models\Devices;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DevicesResource extends Resource
{
    protected static ?string $model = Devices::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static ?string $navigationLabel = 'Biometric Devices';

    protected static ?string $modelLabel = 'Biometric Device';

    protected static ?string $pluralModelLabel = 'Biometric Devices';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'device_name';


    public static function canViewAny(): bool
    {
        $user = auth('administrator')->user();
        if ($user && $user->role === 1) {
            return true;
        }
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return DevicesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DevicesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDevices::route('/'),
            'create' => CreateDevices::route('/create'),
            'edit' => EditDevices::route('/{record}/edit'),
        ];
    }
}
