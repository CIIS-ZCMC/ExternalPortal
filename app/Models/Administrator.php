<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as AuthenticatableUser;
use Illuminate\Support\Facades\Hash;

class Administrator extends AuthenticatableUser implements FilamentUser
{
    protected $connection = 'external_employees';

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'administratorPanel';
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'role',
        'assigned_agencies',
    ];

    protected $casts = [
        'assigned_agencies' => 'array',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::make($value);
        }
    }
}
