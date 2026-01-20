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
use Filament\Forms\Components\Checkbox;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;



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
                // Action::make('Printdtr')
                //     ->label('Print DTR')
                //     ->hidden(fn() => $this->getTableQuery()->count() == 0)
                //     ->icon(Heroicon::Printer)
                //     ->action(function () {
                //         $url = "https://umis.zcmc.online/generateDtr?" .
                //             "biometric_id=[" . Auth::user()->biometric_id .
                //             "]&monthof=" . $this->month .
                //             "&yearof=" . $this->year .
                //             "&view=2&frontview=0&whole_month=1";

                //         // Trigger download in the browser
                //         $this->dispatch('open-new-tab', ['url' => $url]);
                //     }),
                Action::make("is_shifting_action")
                    ->label("Print DTR")
                    ->modalDescription("Since this schedule is not pre-defined, please specify if it follows a shifting pattern or manually designate specific dates for customized scheduling to ensure accurate DTR generation.")
                    ->color("info")
                    ->icon(Heroicon::CalendarDays)
                    ->modalWidth("md")
                    ->schema([

                        Section::make()
                            ->components([
                                Checkbox::make("all_shifting")
                                    ->label("Mark all as w/shifting schedule")
                                    ->live()
                                    ->default(false),
                            ]),

                        Repeater::make('monthly_schedules')
                            ->hidden(fn($get) => $get('all_shifting'))
                            ->label('Days of the Month')
                            ->schema([
                                ComponentsGrid::make(4)
                                    ->schema([
                                        // The specific date
                                        DatePicker::make('dtr_date')
                                            ->label('Date')
                                            ->required(),

                                        // The Shifting Toggle
                                        Checkbox::make("is_shifting")
                                            ->label("Shifting?")
                                            ->live() // Essential for reactivity
                                            ->default(false),


                                    ])
                                    ->columns(2),

                                // You can add second_in/out here as well using the same visible() logic
                            ])
                            ->addable(true)    // Prevents adding random rows
                            ->deletable(true)  // Prevents deleting dates
                            ->reorderable(false)
                            ->columns(1),

                    ])
                    ->modalSubmitActionLabel("Save changes")
                    ->modalCancelActionLabel("Cancel")
                    ->action(function ($data) {
                        dd($data);
                        //   $this->dispatch('open-new-tab', ['url' => $url]);
                    }),

            ])
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
