<?php

namespace App\Filament\AdministratorPanel\Resources\ShiftPresets\Pages;

use App\Filament\AdministratorPanel\Resources\ShiftPresets\ShiftPresetsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShiftPresets extends CreateRecord
{
    protected static string $resource = ShiftPresetsResource::class;

    public function mount(): void
    {
        static::authorizeResourceAccess();
        parent::mount();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
