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
        'permissions',
        'is_2fa_enabled',
        'two_factor_confirmed_at',
    ];

    protected $casts = [
        'assigned_agencies' => 'array',
        'permissions' => 'array',
        'is_2fa_enabled' => 'boolean',
        'two_factor_confirmed_at' => 'datetime',
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

    /**
     * Check if administrator is a super administrator
     */
    public function isSuperAdmin(): bool
    {
        return (int) $this->role === 1;
    }

    /**
     * Check if administrator has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

}
