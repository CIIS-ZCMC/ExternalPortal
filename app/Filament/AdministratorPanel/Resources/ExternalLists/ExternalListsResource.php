<?php

namespace App\Filament\AdministratorPanel\Resources\ExternalLists;

use App\Filament\AdministratorPanel\Resources\ExternalLists\Pages\CreateExternalLists;
use App\Filament\AdministratorPanel\Resources\ExternalLists\Pages\EditExternalLists;
use App\Filament\AdministratorPanel\Resources\ExternalLists\Pages\ListExternalLists;
use App\Filament\AdministratorPanel\Resources\ExternalLists\Schemas\ExternalListsForm;
use App\Filament\AdministratorPanel\Resources\ExternalLists\Tables\ExternalListsTable;
use App\Models\ExternalEmployees;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExternalListsResource extends Resource
{
    protected static ?string $model = ExternalEmployees::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'ExternalEmployees';

    public static function form(Schema $schema): Schema
    {
        return ExternalListsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExternalListsTable::configure($table);
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
            'index' => ListExternalLists::route('/'),
            'create' => CreateExternalLists::route('/create'),
            'edit' => EditExternalLists::route('/{record}/edit'),
        ];
    }
}
