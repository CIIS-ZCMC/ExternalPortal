<?php

namespace App\Filament\Resources\Schedules\Tables;


use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\ExternalEmployeeSchedule;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SchedulesTable
{

    public static function configure(Table $table): Table
    {
        return $table

            ->modifyQueryUsing(function (Builder $query, $livewire) {
                return $query
                    ->where('external_employee_id', Auth::guard('external')->id())
                    ->when($livewire->month, fn($q) => $q->whereMonth('dtr_date', $livewire->month))
                    ->when($livewire->year, fn($q) => $q->whereYear('dtr_date', $livewire->year))
                    ->orderBy('dtr_date', 'asc');
            })
            ->recordUrl(null)
            ->columns([
                TextColumn::make('dtr_date')
                    ->label('DTR Date')
                    ->date()
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_shifting')
                    ->label('Is Shifting')
                    ->icon(fn($state): ?string => $state ? 'heroicon-o-check-circle' : null)
                    ->color('success'),
                TextColumn::make('first_in')
                    ->label('First In'),
                TextColumn::make('first_out')
                    ->default("--:--:--")
                    ->label('First Out'),
                TextColumn::make('second_in')
                    ->default("--:--:--")
                    ->label('Second In'),
                TextColumn::make('second_out')
                    ->label('Second Out'),
            ])
            ->filters([
                Filter::make('dtr_date_filter')
                    ->schema([
                        DatePicker::make('dtr_date')
                            ->label('Select Month/Year')
                            ->native(false)
                            ->displayFormat('F j, Y')
                            ->minDate(fn($livewire) => Carbon::create($livewire->year, $livewire->month, 1)->startOfMonth())
                            ->maxDate(fn($livewire) => Carbon::create($livewire->year, $livewire->month, 1)->endOfMonth())
                            ->required(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['dtr_date'] ?? null,
                            function (Builder $query, $date) {
                                $carbonDate = Carbon::parse($date);
                                return $query
                                    ->where('dtr_date', $carbonDate);
                            }
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['dtr_date']) {
                            return null;
                        }
                        return 'Date: ' . Carbon::parse($data['dtr_date'])->format('F j, Y');
                    })
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->modalHeading('Edit Schedule')
                    ->modalDescription('Are you sure you want to edit this schedule?')
                    ->modalSubmitActionLabel('Save')
                    ->modalCancelActionLabel('Cancel')
                    ->modalWidth('2xl')
                    ->modalIcon('heroicon-o-pencil')
                    ->modalIconColor('primary')
                    ->mountUsing(function ($form, $record) {
                        $form->fill([
                            'is_shifting' => $record->is_shifting,
                            'dtr_date' => $record->dtr_date,
                            'first_in' => $record->first_in,
                            'first_out' => $record->first_out,
                            'second_in' => $record->second_in,
                            'second_out' => $record->second_out,
                        ]);
                    })
                    ->schema(function ($record) {
                        return [
                            Checkbox::make('is_shifting')
                                ->label("Is Shifting")
                                ->live()
                                ->columnSpan(1),
                            Select::make('time_shift')
                                ->label("Time Shift")
                                ->disabled(fn($get) => !$get('is_shifting'))
                                ->options([
                                    '1' => '10:00 AM - 06:00 PM',
                                    '2' => '02:00 PM - 10:00 PM',
                                    '3' => '10:00 PM - 06:00 AM',
                                    '4' => '06:00 AM - 02:00 PM',
                                    '5' => '08:00 AM - 04:00 PM',
                                    '6' => '08:00 AM - 08:00 AM',
                                    '7' => '08:00 AM - 12:00 PM',
                                    '8' => '01:00 PM - 05:00 PM',
                                    '9' => '07:00 AM - 07:00 AM',
                                    '10' => '03:00 PM - 07:00 AM',
                                    '11' => '06:00 AM - 10:00 PM',
                                ])
                                ->live()
                                ->afterStateUpdated(function ($set, $get) {
                                    if ($get('time_shift')) {
                                        $shift = $get('time_shift');
                                        $shiftMap = [
                                            '1' => ['10:00:00', '18:00:00'],
                                            '2' => ['14:00:00', '22:00:00'],
                                            '3' => ['22:00:00', '06:00:00'],
                                            '4' => ['06:00:00', '14:00:00'],
                                            '5' => ['08:00:00', '16:00:00'],
                                            '6' => ['08:00:00', '08:00:00'],
                                            '7' => ['08:00:00', '12:00:00'],
                                            '8' => ['13:00:00', '17:00:00'],
                                            '9' => ['07:00:00', '07:00:00'],
                                            '10' => ['15:00:00', '23:00:00'],
                                            '11' => ['06:00:00', '22:00:00'],
                                        ];
                                        $times = $shiftMap[$shift] ?? ['08:00:00', '16:00:00'];
                                        $set("first_in", $times[0]);
                                        $set("second_out", $times[1]);
                                        $set("first_out", null);
                                        $set("second_in", null);
                                    }
                                })
                                ->columnSpan(1),
                            DatePicker::make("dtr_date")
                                ->label("Schedule Date")
                                ->required(),
                            TimePicker::make("first_in")
                                ->label("First In")
                                ->required(),
                            TimePicker::make("first_out")
                                ->label("First Out")
                                ->hidden(fn($get) => $get('is_shifting'))
                                ->required(),
                            TimePicker::make("second_in")
                                ->label("Second In")
                                ->hidden(fn($get) => $get('is_shifting'))
                                ->required(),
                            TimePicker::make("second_out")
                                ->label("Second Out")
                                ->required(),
                        ];
                    })
                    ->action(function ($record, $data) {

                        ExternalEmployeeSchedule::find($record->id)->update([
                            'is_shifting' => $data['is_shifting'],
                            'dtr_date' => $data['dtr_date'],
                            'first_in' => $data['first_in'],
                            'first_out' => isset($data['first_out']) ? $data['first_out'] : null,
                            'second_in' => isset($data['second_in']) ? $data['second_in'] : null,
                            'second_out' => $data['second_out'],
                        ]);
                        Notification::make()
                            ->title('Schedule updated successfully')
                            ->success()
                            ->send();
                    }),
                DeleteAction::make(),

            ])
            ->emptyStateHeading('No schedules found')
            ->emptyStateDescription('You may create a new schedule using the "Create Schedule" button below.')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
