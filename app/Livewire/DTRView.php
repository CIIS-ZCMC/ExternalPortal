<?php

namespace App\Livewire;

use App\Models\DTR as DailyTimeRecord;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\HeaderActionsPosition;

class DTRView extends TableWidget
{


    protected $listeners = ['applyFilter' => 'ApplyFilter'];

    public int $month;
    public int $year;


    public function getTableHeading(): string|Htmlable|null
    {
        return "";
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }


    public function ApplyFilter($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    protected function getTableQuery(): Builder
    {
        return DailyTimeRecord::query()
            ->where('biometric_id', Auth::user()->biometric_id)
            ->whereMonth('dtr_date', $this->month)
            ->whereYear('dtr_date', $this->year);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => $this->getTableQuery())
            ->columns([
                TextColumn::make('dtr_date')
                    ->label('Weekday')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->format('d') . " | " . Carbon::parse($state)->format('D');
                    }),
                TextColumn::make('first_in')->label('First In')->searchable()
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('h:i A'))
                    ->sortable(),
                TextColumn::make('first_out')->label('First Out')->searchable()
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('h:i A'))
                    ->sortable(),
                TextColumn::make('second_in')->label('Second In')->searchable()
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('h:i A'))
                    ->sortable(),
                TextColumn::make('second_out')->label('Second Out')->searchable()
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('h:i A'))
                    ->sortable(),

                // TextColumn::make('undertime')->label('Undertime')->searchable()
                //     ->sortable(),
                // TextColumn::make('overall_minutes_rendered')->label('Overall Minutes Rendered')->searchable()
                //     ->sortable(),

            ])
            ->filters([
                Filter::make("dtr_date")
                    ->schema([
                        DatePicker::make("dtr_date")
                            ->label("Select Date")
                            ->reactive(),
                    ])->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['dtr_date'], fn($q) => $q->whereDate('dtr_date', $data['dtr_date']));
                    })
            ])
            ->emptyStateHeading('No DTR Found')
            ->headerActionsPosition(HeaderActionsPosition::Bottom)
            ->headerActions([
                Action::make('Printdtr')
                    ->label('Print DTR')
                    ->hidden(fn() => $this->getTableQuery()->count() == 0)
                    ->icon(Heroicon::Printer)
                    ->url(
                        fn() => "http://192.168.8.95:8000/generateDtr?biometric_id=[" .
                            Auth::user()->biometric_id .
                            "]&monthof=" . $this->month .
                            "&yearof=" . $this->year .
                            "&view=2&frontview=0&whole_month=1"
                    )
                    ->openUrlInNewTab()

            ])
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
