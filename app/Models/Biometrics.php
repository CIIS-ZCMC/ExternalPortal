<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biometrics extends Model
{

    protected $table = 'biometrics';

    protected $fillable = [
        'biometric_id',
        'name',
        'privilege',
        'biometric',
        'name_with_biometric',
    ];
}
