<?php

namespace App\Filament\AdministratorPanel\Resources\ShiftPresets\Pages;

use App\Filament\AdministratorPanel\Resources\ShiftPresets\ShiftPresetsResource;
use Filament\Resources\Pages\EditRecord;

class EditShiftPresets extends EditRecord
{
    protected static string $resource = ShiftPresetsResource::class;

    public function mount(int | string $record): void
    {
        static::authorizeResourceAccess();
        parent::mount($record);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
