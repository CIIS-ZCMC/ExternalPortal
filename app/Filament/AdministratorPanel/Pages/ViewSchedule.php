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

        $admin = auth('administrator')->user();
        if ($admin && !empty($admin->assigned_agencies)) {
            $agencies = is_array($admin->assigned_agencies)
                ? $admin->assigned_agencies
                : json_decode($admin->assigned_agencies, true);

            if (!empty($agencies)) {
                $employee = \App\Models\ExternalEmployees::where('biometric_id', $this->biometric_id)->first();
                if ($employee && !in_array($employee->agency, $agencies)) {
                    abort(403, 'You are not authorized to view employees from this agency.');
                }
            }
        }
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return $this->biometric_id ? "Duty Schedule: PIN {$this->biometric_id}" : 'Employee Duty Schedule';
    }

    public function getSubheading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'Manage and allocate duty shifts and office hours for this employee.';
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
            \App\Livewire\ViewScheduleAdminWidget::make([
                'biometric_id' => $this->biometric_id,
            ]),
        ];
    }
}
