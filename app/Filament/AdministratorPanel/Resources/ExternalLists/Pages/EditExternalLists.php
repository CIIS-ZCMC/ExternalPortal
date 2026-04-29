<?php

namespace App\Filament\AdministratorPanel\Resources\ExternalLists\Pages;

use App\Filament\AdministratorPanel\Resources\ExternalLists\ExternalListsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExternalLists extends EditRecord
{
    protected static string $resource = ExternalListsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
