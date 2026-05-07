<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use App\Livewire\FilterWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;
    protected $listeners = ['applyFilter' => 'ApplyFilter'];
    public int $month;
    public int $year;

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }


    public function ApplyFilter($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
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
