<?php

namespace App\Filament\AdministratorPanel\Resources\ExternalLists\Pages;

use App\Filament\AdministratorPanel\Resources\ExternalLists\ExternalListsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExternalLists extends ListRecords
{
    protected static string $resource = ExternalListsResource::class;

    // Custom view adds the open-new-tab JS listener used by the Print DTR action.
    protected string $view = 'filament.administrator-panel.pages.list-external-lists';

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
