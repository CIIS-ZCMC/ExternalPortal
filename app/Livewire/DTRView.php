<?php

namespace App\Livewire;

use App\Models\DTR as DailyTimeRecord;
use Filament\Actions\BulkActionGroup;
use Filament\Infolists\Components\TextEntry;
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
use App\Models\CustomSchedule;
use App\Models\PortalSetting;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
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
        $isUnderMaintenance = false; //Auth::guard("external")->user()->biometric_id !== 8010;
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
                //     ->label(fn()=>$isUnderMaintenance ? "Printing - Under Maintenance":'Print DTR')
                //    // ->hidden(fn() => $this->getTableQuery()->count() == 0)
                //    ->icon(fn()=>$isUnderMaintenance ? Heroicon::ExclamationTriangle : Heroicon::Printer)
                //    ->disabled(fn() => $isUnderMaintenance)

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
                    ->hidden(fn() => $isUnderMaintenance ? true : false || $this->getTableQuery()->count() == 0)
                    ->label("Print DTR")
                    ->modalDescription("Since this schedule is not pre-defined, please specify if it follows a shifting pattern or manually designate specific dates for customized scheduling to ensure accurate DTR generation.")
                    ->color("info")
                    ->icon(Heroicon::CalendarDays)
                    ->modalWidth("lg")
                    ->schema([

                        Section::make()
                            ->components([

                                Radio::make('schedule_type')
                                    ->label('Select Schedule Type')

                                    ->options([
                                        'normal' => 'Normal schedule ( 8 am - 12 pm | 1 pm - 5 pm )',
                                        'shifting' => 'Shifting schedule',
                                        'custom' => 'Select specific dates as shifting/normal',
                                    ])
                                    ->default('normal')
                                    ->live()

                            ]),


                        Repeater::make('monthly_schedules')
                            ->hidden(fn($get) => $get('schedule_type') == 'normal' || $get('schedule_type') == 'shifting')
                            ->label('Add date to be set as shifting schedule')
                            ->schema([
                                ComponentsGrid::make(4)
                                    ->schema([
                                        // The specific date
                                        DatePicker::make('dtr_date')
                                            ->label('Date')
                                            ->required(),

                                        // The Shifting Toggle



                                    ])
                                    ->columns(1),

                                // You can add second_in/out here as well using the same visible() logic
                            ])
                            ->addable(true)    // Prevents adding random rows
                            ->deletable(true)  // Prevents deleting dates
                            ->reorderable(false)
                            ->addActionLabel("Add Date")
                            ->columns(1),

                    ])
                    ->modalSubmitActionLabel("Save changes & Print DTR")
                    ->modalCancelActionLabel("Cancel")
                    ->action(function ($data) {

                        $portal = PortalSetting::where('external_employee_id', Auth::user()->id)
                            ->where('month', $this->month)
                            ->where('year', $this->year)
                            ->first();
                        if (!$portal) {
                            $portal = PortalSetting::create([
                                'external_employee_id' => Auth::user()->id,
                                'schedule_type' => $data['schedule_type'],
                                'month' => $this->month,
                                'year' => $this->year,
                            ]);
                        }
                        $portal->update([
                            'schedule_type' => $data['schedule_type'],
                            'month' => $this->month,
                            'year' => $this->year,
                        ]);

                        if (isset($data['monthly_schedules'])) {
                            $monthly_schedules = $data['monthly_schedules'];
                            $schedules = [];
                            foreach ($monthly_schedules as $key => $value) {
                                $schedules[] = CustomSchedule::UpdateOrCreate([
                                    'portal_setting_id' => $portal->id,
                                    'dtr_date' => $value['dtr_date'],
                                ], [
                                    'is_shifting' => true,
                                ]);
                            }
                        }

                        if ($data['schedule_type'] !== "custom") {
                            CustomSchedule::where("portal_setting_id", $portal->id)
                                ->whereIn("portal_setting_id", function ($query) {
                                    $query->select("id")
                                        ->from("portal_settings")
                                        ->whereNot("schedule_type", "custom");
                                })->delete();
                        }



                        $url = "https://umis.zcmc.online/generateDtr?" .
                            "biometric_id=[" . Auth::user()->biometric_id .
                            "]&monthof=" . $this->month .
                            "&yearof=" . $this->year .
                            "&view=2&frontview=0&whole_month=1&ext=" . Auth::user()->id;

                        // Trigger download in the browser
                        $this->dispatch('open-new-tab', ['url' => $url]);
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
