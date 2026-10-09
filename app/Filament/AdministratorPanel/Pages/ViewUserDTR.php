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

        $admin = auth('administrator')->user();
        if ($admin && !empty($admin->assigned_agencies)) {
            $agencies = is_array($admin->assigned_agencies)
                ? $admin->assigned_agencies
                : json_decode($admin->assigned_agencies, true);

            if (!empty($agencies)) {
                $employee = null;
                if ($this->external_employee_id) {
                    $employee = \App\Models\ExternalEmployees::find($this->external_employee_id);
                } elseif ($this->biometric_id) {
                    $employee = \App\Models\ExternalEmployees::where('biometric_id', $this->biometric_id)->first();
                }

                if ($employee && !in_array($employee->agency, $agencies)) {
                    abort(403, 'You are not authorized to view employees from this agency.');
                }
            }
        }
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return $this->employee_name ? "DTR Record: {$this->employee_name}" : 'Employee DTR Record';
    }

    public function getSubheading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'Review biometric punch history and generate official printouts.';
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('back')
                ->label('Back to Employee Directory')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(route('filament.administratorPanel.resources.external-lists.index')),
        ];
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
