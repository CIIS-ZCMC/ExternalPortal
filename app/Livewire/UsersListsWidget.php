<?php

namespace App\Livewire;

use App\Models\ExternalEmployees;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use App\Models\DTR;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

class UsersListsWidget extends TableWidget
{
    public function getTableHeading(): string|Htmlable|null
    {
        return "External Employees Lists";
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    public function getTableQuery(): Builder
    {
        return ExternalEmployees::query();
    }
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make("status")
                    ->label("Status")
                    ->searchable()
                    ->sortable()
                    ->badge()
                
                    ->size("10px")
                    ->color(function ($record) {
                        if($record->deleted_at){
                            return "danger";
                        }

                        $dtr = DTR::where("biometric_id", $record->biometric_id)->first();
                        return $dtr ? "success" : "danger";
                    })
                    ->state(function ($record) {

                        if($record->deleted_at){
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
            ])
            ->filters([

                TrashedFilter::make()
                ->label('Employee Status')
              
                ->falseLabel('Show DeActivated Only')
                ->native(false) // Better UI
              
                ->placeholder('All Employees'),

                SelectFilter::make('status_filter')
                    ->label('Status')
                    ->options([
                        'ACTIVE' => 'ACTIVE',
                        'INACTIVE' => 'INACTIVE',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;

                        if ($value === 'ACTIVE') {
                            return $query->whereHas('dtr');
                        }

                        if ($value === 'INACTIVE') {
                            return $query->whereDoesntHave('dtr');
                        }

                        return $query;
                    }),


                SelectFilter::make('agency_filter') // virtual filter name
                    ->label('Agency')
                    ->options([
                        "Department of Health (DOH)" => "Department of Health (DOH)",
                        "Department of Education (DepEd)" => "Department of Education (DepEd)",
                        "Department of the Interior and Local Government (DILG)" => "Department of the Interior and Local Government (DILG)",
                        "Department of Social Welfare and Development (DSWD)" => "Department of Social Welfare and Development (DSWD)",
                        "Department of Finance (DOF)" => "Department of Finance (DOF)",
                        "Department of Budget and Management (DBM)" => "Department of Budget and Management (DBM)",
                        "Department of Science and Technology (DOST)" => "Department of Science and Technology (DOST)",
                        "Department of Tourism (DOT)" => "Department of Tourism (DOT)",
                        "Department of Justice (DOJ)" => "Department of Justice (DOJ)",
                        "Department of Agriculture (DA)" => "Department of Agriculture (DA)",
                        "Department of Labor and Employment (DOLE)" => "Department of Labor and Employment (DOLE)",
                        "Department of National Defense (DND)" => "Department of National Defense (DND)",
                        "Department of Transportation (DOTr)" => "Department of Transportation (DOTr)",
                        "Department of Public Works and Highways (DPWH)" => "Department of Public Works and Highways (DPWH)",
                        "Department of Trade and Industry (DTI)" => "Department of Trade and Industry (DTI)",
                        "Department of Environment and Natural Resources (DENR)" => "Department of Environment and Natural Resources (DENR)",
                        "PhilHealth" => "PhilHealth",
                        "Food and Drug Administration (FDA)" => "Food and Drug Administration (FDA)",
                        "Professional Regulation Commission (PRC)" => "Professional Regulation Commission (PRC)",
                        "Commission on Audit (COA)" => "Commission on Audit (COA)",
                        "Government Service Insurance System (GSIS)" => "Government Service Insurance System (GSIS)",
                        "Civil Service Commission (CSC)" => "Civil Service Commission (CSC)",
                        "Provincial Government" => "Provincial Government",
                        "City Government" => "City Government",
                        "Municipal Government" => "Municipal Government",
                        "Barangay Government" => "Barangay Government",
                        "Commission on Elections (COMELEC)" => "Commission on Elections (COMELEC)",
                        "Commission on Higher Education (CHED)" => "Commission on Higher Education (CHED)",
                        "Technical Education and Skills Development Authority (TESDA)" => "Technical Education and Skills Development Authority (TESDA)",
                        "Philippine National Police (PNP)" => "Philippine National Police (PNP)",
                        "Armed Forces of the Philippines (AFP)" => "Armed Forces of the Philippines (AFP)",
                        "Zamboanga City Medical Center (ZCMC)" => "Zamboanga City Medical Center (ZCMC)",
                        "Other Hospital / Medical Institution" => "Other Hospital / Medical Institution",
                        "Other Government Agency" => "Other Government Agency",
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if ($value) {
                            return $query->where('agency', $value);
                        }
                        return $query;
                    })
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('deactivate')
                ->label('Deactivate')
                ->icon('heroicon-o-user-minus')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Deactivate Employee')
                ->modalDescription('Are you sure you want to deactivate this employee? They will no longer be able to log in.')
                ->action(function ($record) {
                   $record->delete();
                })  
                ->visible(fn ($record) => $record->deleted_at === null)
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    
                ]),
            ]);
    }
}
