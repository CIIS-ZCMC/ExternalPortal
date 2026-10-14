<?php

namespace App\Filament\AdministratorPanel\Resources\ShiftPresets\Pages;

use App\Filament\AdministratorPanel\Resources\ShiftPresets\ShiftPresetsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShiftPresets extends ListRecords
{
    protected static string $resource = ShiftPresetsResource::class;

    public function mount(): void
    {
        static::authorizeResourceAccess();
        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Shift Preset')
                ->icon('heroicon-o-plus')
                ->visible(fn () => (int) auth('administrator')->user()?->role === 1),
        ];
    }
}
