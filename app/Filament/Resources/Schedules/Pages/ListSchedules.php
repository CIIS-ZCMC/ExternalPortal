<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use App\Livewire\FilterWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected static ?string $title = 'My Duty Schedules';

    protected $listeners = ['applyFilter' => 'ApplyFilter'];

    public int $month;
    public int $year;

    public function mount(): void
    {
        parent::mount();

        $this->month = (int) now()->month;
        $this->year = (int) now()->year;
    }

    #[On('applyFilter')]
    public function ApplyFilter($month, $year)
    {
        $this->month = (int) $month;
        $this->year = (int) $year;
        $this->resetTable();
    }

    public function getSubheading(): string|Htmlable|null
    {
        $hour = now()->hour;
        $user = Auth::guard('external')->user();
        $name = $user ? $user->name : 'Personnel';
        $agency = $user ? $user->agency : null;
        $position = $user ? $user->position : null;
        $bioId = $user ? $user->biometric_id : null;

        $greeting = $hour < 12
            ? 'Good morning'
            : ($hour < 18 ? 'Good afternoon' : 'Good evening');

        $details = array_filter([$position, $agency]);
        $detailsStr = !empty($details) ? implode(' &bull; ', $details) : 'External Personnel';

        $pinBadge = $bioId ? "<span style='display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-family: ui-monospace, monospace; font-weight: 600; background-color: rgba(16, 185, 129, 0.12); color: rgb(5, 150, 105); border: 1px solid rgba(16, 185, 129, 0.3);'>PIN: {$bioId}</span>" : "";

        return new \Illuminate\Support\HtmlString("
            <div style='display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; margin-top: 0.25rem; font-size: 0.875rem;' class='text-slate-600 dark:text-slate-400'>
                <span>{$greeting}, <strong class='text-slate-900 dark:text-white'>{$name}</strong>!</span>
                <span style='color: #cbd5e1;' class='hidden sm:inline dark:text-slate-600'>|</span>
                <span>{$detailsStr}</span>
                {$pinBadge}
            </div>
        ");
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label("Plot New Schedule")
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            FilterWidget::class,
        ];
    }
}
