<?php

namespace App\Filament\AdministratorPanel\Resources\Administrators;

use App\Filament\AdministratorPanel\Resources\Administrators\Pages\CreateAdministrators;
use App\Filament\AdministratorPanel\Resources\Administrators\Pages\EditAdministrators;
use App\Filament\AdministratorPanel\Resources\Administrators\Pages\ListAdministrators;
use App\Filament\AdministratorPanel\Resources\Administrators\Schemas\AdministratorsForm;
use App\Filament\AdministratorPanel\Resources\Administrators\Tables\AdministratorsTable;
use App\Models\Administrator;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdministratorsResource extends Resource
{
    protected static ?string $model = Administrator::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

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
        return AdministratorsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdministratorsTable::configure($table);
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
            'index' => ListAdministrators::route('/'),
            'create' => CreateAdministrators::route('/create'),
            'edit' => EditAdministrators::route('/{record}/edit'),
        ];
    }
}
