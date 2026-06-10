<?php

namespace App\Filament\AdministratorPanel\Resources\Administrators\Pages;

use App\Filament\AdministratorPanel\Resources\Administrators\AdministratorsResource;
use Filament\Resources\Pages\ListRecords;

class ListAdministrators extends ListRecords
{
    protected static string $resource = AdministratorsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->label('Add Administrator')
                ->icon('heroicon-o-plus'),
        ];
    }
}
