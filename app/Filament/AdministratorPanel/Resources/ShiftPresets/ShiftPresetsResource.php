<?php

namespace App\Filament\AdministratorPanel\Resources\ShiftPresets;

use App\Filament\AdministratorPanel\Resources\ShiftPresets\Pages\CreateShiftPresets;
use App\Filament\AdministratorPanel\Resources\ShiftPresets\Pages\EditShiftPresets;
use App\Filament\AdministratorPanel\Resources\ShiftPresets\Pages\ListShiftPresets;
use App\Filament\AdministratorPanel\Resources\ShiftPresets\Schemas\ShiftPresetsForm;
use App\Filament\AdministratorPanel\Resources\ShiftPresets\Tables\ShiftPresetsTable;
use App\Models\ShiftPreset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ShiftPresetsResource extends Resource
{
    protected static ?string $model = ShiftPreset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Shift Presets';

    protected static ?string $modelLabel = 'Shift Preset';

    protected static ?string $pluralModelLabel = 'Shift Presets';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 4;

    /**
     * Superadmin (role === 1) or Administrator with manage_shift_presets permission
     */
    public static function canViewAny(): bool
    {
        $user = auth('administrator')->user();
        return $user && ($user->isSuperAdmin() || $user->hasPermission('manage_shift_presets'));
    }

    public static function canCreate(): bool
    {
        $user = auth('administrator')->user();
        return $user && ($user->isSuperAdmin() || $user->hasPermission('manage_shift_presets'));
    }

    public static function canEdit($record): bool
    {
        $user = auth('administrator')->user();
        return $user && ($user->isSuperAdmin() || $user->hasPermission('manage_shift_presets'));
    }

    public static function canDelete($record): bool
    {
        $user = auth('administrator')->user();
        return $user && ($user->isSuperAdmin() || $user->hasPermission('manage_shift_presets'));
    }

    public static function form(Schema $schema): Schema
    {
        return ShiftPresetsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShiftPresetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShiftPresets::route('/'),
            'create' => CreateShiftPresets::route('/create'),
            'edit' => EditShiftPresets::route('/{record}/edit'),
        ];
    }
}
