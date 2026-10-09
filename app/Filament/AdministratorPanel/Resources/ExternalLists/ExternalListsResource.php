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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'External Employees';

    protected static ?string $modelLabel = 'External Employee';

    protected static ?string $pluralModelLabel = 'External Employees';

    protected static ?string $recordTitleAttribute = 'first_name';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
       
        return true;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $admin = auth('administrator')->user();

        if ($admin && !empty($admin->assigned_agencies)) {
            $agencies = is_array($admin->assigned_agencies)
                ? $admin->assigned_agencies
                : json_decode($admin->assigned_agencies, true);

            if (!empty($agencies)) {
                $query->whereIn('agency', $agencies);
            }
        }

        return $query;
    }
    
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
