<?php

namespace App\Filament\AdministratorPanel\Pages;

use App\Livewire\FilterWidget;
use Filament\Pages\Page;

class ViewUserDTR extends Page
{
    protected string $view = 'filament.administrator-panel.pages.view-user-d-t-r';

    public static bool $shouldRegisterNavigation = false;

    public $biometric_id;
    public $external_employee_id;
    public $employee_name;

    public function mount(): void
    {
        $this->biometric_id = request()->query('biometric_id');
        $this->external_employee_id = request()->query('external_employee_id');
        $this->employee_name = request()->query('employee_name');
    }

    public function getHeaderWidgets(): array
    {
        return [
            FilterWidget::class,
            \App\Livewire\AdministratorDTRView::make([
                'biometric_id' => $this->biometric_id,
                'external_employee_id' => $this->external_employee_id,
                'employee_name' => $this->employee_name,
            ]),
        ];
    }
}
