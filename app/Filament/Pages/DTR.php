<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\DTR as DailyTimeRecord;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Group as ComponentsGroup;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use App\Livewire\FilterWidget;
use App\Livewire\DTRView;

class DTR extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'filament.pages.d-t-r';

    protected static ?string $title = 'My Daily Time Record';

    protected static ?string $navigationLabel = 'My DTR';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?int $navigationSort = 1;


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
        return [];
    }

    public function getHeaderWidgets(): array
    {
        return [
            FilterWidget::class,
            DTRView::class,
        ];
    }
}
