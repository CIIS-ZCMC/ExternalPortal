<?php

namespace App\Filament\Pages;

use App\Models\ExternalEmployees;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class MyScheduleCalendar extends Page
{
    protected string $view = 'filament.pages.my-schedule-calendar';

    protected static ?string $navigationLabel = 'My Calendar Schedules';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $title = 'My Schedule Calendar';

    protected static ?int $navigationSort = 2;

    public ?ExternalEmployees $employee = null;

    public function mount(): void
    {
        $this->employee = Auth::guard('external')->user();
        if (!$this->employee) {
            abort(403);
        }
    }

    public function getSubheading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'Interactive view of your plotted duty shifts and office hours.';
    }
}
