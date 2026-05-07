<?php

namespace App\Filament\AdministratorPanel\Resources\ExternalLists\Pages;

use App\Filament\AdministratorPanel\Resources\ExternalLists\ExternalListsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExternalLists extends ListRecords
{
    protected static string $resource = ExternalListsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
