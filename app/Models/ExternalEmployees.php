<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExternalEmployees extends Authenticatable implements FilamentUser
{
    use HasFactory, SoftDeletes;
    protected $table = "external_employees";

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'portal';
    }

    protected $fillable = [
        'username',
        'password',
        'first_name',
        'last_name',
        'middle_name',
        'ext_name',
        'email',
        'email_verified_at',
        'contact_number',
        'address',
        'agency',
        'position',
        'status',
        'biometric_id',
        'is_2fa_enabled',
        'two_factor_confirmed_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_2fa_enabled' => 'boolean',
        'two_factor_confirmed_at' => 'datetime',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function fullName()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function dtr()
    {
        return $this->hasMany(DTR::class, 'biometric_id', 'biometric_id');
    }
}
