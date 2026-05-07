<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomSchedule extends Model
{

    protected $table = 'custom_schedules';
    protected $connection = 'external_employees';

    protected $fillable = [
        'portal_setting_id',
        'dtr_date',
        'is_shifting',
    ];
}
