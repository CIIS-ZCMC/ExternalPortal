<?php

namespace App\Livewire;

use App\Models\DTR;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Group;
use Filament\Widgets\Widget;

class FilterWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'livewire.filter-widget';

    public array $months = [];
    public array $years = [];
    public ?int $selectedMonth = null;
    public ?int $selectedYear = null;

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;

        $this->months = collect(range(1, 12))
            ->mapWithKeys(fn($m) => [$m => date('F', mktime(0, 0, 0, $m, 1))])
            ->toArray();

        $dbYears = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('daily_time_records')) {
                $dbYears = DTR::selectRaw('YEAR(dtr_date) as year')
                    ->distinct()
                    ->orderByDesc('year')
                    ->pluck('year')
                    ->filter()
                    ->toArray();
            }
        } catch (\Throwable $e) {
            $dbYears = [];
        }

        $defaultYears = range(now()->year - 3, now()->year + 1);
        $allYears = array_unique(array_merge($dbYears, $defaultYears));
        rsort($allYears);

        $this->years = collect($allYears)
            ->mapWithKeys(fn($y) => [$y => (string)$y])
            ->toArray();
    }

    protected int | string | array $columnSpan = 'full';

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    public function getFormSchema(): array
    {
        return [
            Group::make()
                ->schema([
                    Select::make('selectedMonth')
                        ->label('Select Month')
                        ->options($this->months)
                        ->prefixIcon('heroicon-o-calendar')
                        ->native(false)
                        ->selectablePlaceholder(false)
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->updatedSelectedMonth($state);
                        }),

                    Select::make('selectedYear')
                        ->label('Select Year')
                        ->options($this->years)
                        ->prefixIcon('heroicon-o-calendar-days')
                        ->native(false)
                        ->selectablePlaceholder(false)
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->updatedSelectedYear($state);
                        }),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ];
    }

    public function updatedSelectedMonth($month)
    {
        $this->selectedMonth = (int) $month;
        $this->applyFilter($this->selectedMonth, $this->selectedYear);
    }

    public function updatedSelectedYear($year)
    {
        $this->selectedYear = (int) $year;
        $this->applyFilter($this->selectedMonth, $this->selectedYear);
    }

    public function applyFilter($month, $year)
    {
        $this->dispatch('applyFilter', month: (int) $month, year: (int) $year);
    }
}
