<?php

namespace App\Filament\AdministratorPanel\Pages;

use App\Livewire\FilterWidget;
use Filament\Pages\Page;

class ViewSchedule extends Page
{
    protected string $view = 'filament.administrator-panel.pages.view-schedule';


    public static bool $shouldRegisterNavigation = false;

    public $biometric_id;
    public function mount(): void
    {
        $this->biometric_id = request()->query('biometric_id');
    }



    public function getHeaderWidgets(): array
    {
        return [
            FilterWidget::class,
            \App\Livewire\ViewScheduleAdminWidget::make([
                'biometric_id' => $this->biometric_id,
            ]),
        ];
    }
}
