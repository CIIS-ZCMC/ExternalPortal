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


    public function getSubheading(): string|Htmlable|null
    {
        $hour = now()->hour;
        $name = Auth::user()->name;



        $greeting = $hour < 12
            ? 'Good Morning'
            : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');

        return "$greeting, $name! 👋";
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
