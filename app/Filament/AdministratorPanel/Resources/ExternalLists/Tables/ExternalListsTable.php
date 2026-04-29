<?php

namespace App\Filament\AdministratorPanel\Resources\ExternalLists\Tables;

use App\Filament\AdministratorPanel\Pages\ViewSchedule;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\DTR;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Enums\RecordActionsPosition;

class ExternalListsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make("status")
                    ->label("Status")
                    ->searchable()
                    ->sortable()
                    ->badge()

                    ->size("10px")
                    ->color(function ($record) {
                        if ($record->deleted_at) {
                            return "danger";
                        }

                        $dtr = DTR::where("biometric_id", $record->biometric_id)->first();
                        return $dtr ? "success" : "danger";
                    })
                    ->state(function ($record) {

                        if ($record->deleted_at) {
                            return "INACTIVE";
                        }

                        $dtr = DTR::where("biometric_id", $record->biometric_id)->first();
                        return $dtr ? "ACTIVE" : "INACTIVE";
                    }),

                TextColumn::make("biometric_id")
                    ->label("Biometric ID")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("first_name")
                    ->label("First Name")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("last_name")
                    ->label("Last Name")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("middle_name")
                    ->label("Middle Name")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("position")
                    ->label("Position")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("email")
                    ->label("Email")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("contact_number")
                    ->label("Contact Number")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("agency")
                    ->label("Agency")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("created_at")
                    ->label("Created At")
                    ->since()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make("username")
                    ->label("Username")
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActionsPosition(RecordActionsPosition::BeforeColumns)
            ->recordActions([
                Action::make("Print_DTR")
                    ->label("Print DTR")
                    ->icon("heroicon-o-printer")
                    ->schema([
                        Select::make('month')
                            ->options([
                                '01' => 'January',
                                '02' => 'February',
                                '03' => 'March',
                                '04' => 'April',
                                '05' => 'May',
                                '06' => 'June',
                                '07' => 'July',
                                '08' => 'August',
                                '09' => 'September',
                                '10' => 'October',
                                '11' => 'November',
                                '12' => 'December',
                            ])
                            ->required(),
                        Select::make('year')
                            ->options(function () {
                                $years = range(date('Y') - 3, date('Y') + 3);
                                return array_combine($years, $years);
                            })
                            ->required()
                    ])
                    ->modalWidth('md')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->action(function ($record, array $data) {

                        $url = "https://umis.zcmc.online/generateDtr?" .
                            "biometric_id=[" . $record->biometric_id .
                            "]&monthof=" . $data['month'] .
                            "&yearof=" . $data['year'] .
                            "&view=2&frontview=0&whole_month=1&ext=" . $record->id;

                        // Trigger download in the browser
                        return redirect($url);
                    }),

                Action::make("viewSchedule")
                    ->label("View Schedule")
                    ->icon("heroicon-o-eye")
                    ->color("gray")
                    ->url(fn($record): string => ViewSchedule::getUrl([
                        'biometric_id' => $record->biometric_id
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
