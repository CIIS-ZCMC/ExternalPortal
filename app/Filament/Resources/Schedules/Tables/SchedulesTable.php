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
