<?php

namespace App\Filament\AdministratorPanel\Pages;

use App\Livewire\FilterWidget;
use App\Models\ExternalEmployees;
use Filament\Actions\Action;
use Filament\Pages\Page;

class ViewSchedule extends Page
{
    protected string $view = 'filament.administrator-panel.pages.view-schedule';

    public static bool $shouldRegisterNavigation = false;

    public $biometric_id;

    public ?ExternalEmployees $employee = null;

    public string $activeTab = 'calendar'; // 'calendar' or 'table'

    public function mount(): void
    {
        $this->biometric_id = request()->query('biometric_id');
        $this->activeTab = request()->query('tab', 'calendar');

        $admin = auth('administrator')->user();
        if ($this->biometric_id) {
            $this->employee = ExternalEmployees::where('biometric_id', $this->biometric_id)->first();
        }

        if ($admin && !empty($admin->assigned_agencies)) {
            $agencies = is_array($admin->assigned_agencies)
                ? $admin->assigned_agencies
                : json_decode($admin->assigned_agencies, true);

            if (!empty($agencies)) {
                if ($this->employee && !in_array($this->employee->agency, $agencies)) {
                    abort(403, 'You are not authorized to view employees from this agency.');
                }
            }
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        if ($this->employee) {
            return "Duty Schedule: {$this->employee->name} (PIN: {$this->employee->biometric_id})";
        }
        return $this->biometric_id ? "Duty Schedule: PIN {$this->biometric_id}" : 'Employee Duty Schedule';
    }

    public function getSubheading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'Interactive calendar shifting schedule management with drag-and-drop & duration adjustments.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Employee Directory')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(route('filament.administratorPanel.resources.external-lists.index')),
        ];
    }

    public function getHeaderWidgets(): array
    {
        // When active tab is table, show existing filter widget and table widget
        if ($this->activeTab === 'table') {
            return [
                FilterWidget::class,
                \App\Livewire\ViewScheduleAdminWidget::make([
                    'biometric_id' => $this->biometric_id,
                ]),
            ];
        }

        return [];
    }
}
