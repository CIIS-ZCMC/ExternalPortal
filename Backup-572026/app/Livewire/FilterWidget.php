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

        $this->years = DTR::selectRaw('YEAR(dtr_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->mapWithKeys(fn($y) => [$y => $y])
            ->toArray();
    }


    public function getFormSchema(): array
    {

        return [
            Group::make()
                ->schema([
                    Group::make()
                        ->schema([
                            Select::make('selectedMonth')
                                ->label('Month')
                                ->options($this->months)

                                ->reactive()
                                ->afterStateUpdated(function ($state) {

                                    $this->updatedSelectedMonth($state);
                                }),
                            Select::make('selectedYear')
                                ->label('Year')
                                ->options($this->years)

                                ->reactive()
                                ->afterStateUpdated(function ($state) {
                                    $this->updatedSelectedYear($state);
                                }),

                        ])->columns(2)->columnSpanFull(),



                ])->columns(2)
        ];
    }

    public function updatedSelectedMonth($month)
    {
        $this->selectedMonth = $month;
        $this->applyFilter($month, $this->selectedYear);
    }

    public function updatedSelectedYear($year)
    {
        $this->selectedYear = $year;
        $this->applyFilter($this->selectedMonth, $year);
    }


    public function applyFilter($month, $year)
    {
        $this->dispatch('applyFilter', $month, $year);
    }
}
