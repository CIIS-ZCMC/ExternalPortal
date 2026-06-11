<?php

namespace App\Filament\AdministratorPanel\Resources\Administrators\Pages;

use App\Filament\AdministratorPanel\Resources\Administrators\AdministratorsResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAdministrators extends CreateRecord
{
    protected static string $resource = AdministratorsResource::class;


}
