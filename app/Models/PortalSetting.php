<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalSetting extends Model
{
    protected $table = 'portal_settings';
    protected $connection = 'external_employees';

    protected $fillable = [
        'external_employee_id',
        'month',
        'year',
        'schedule_type',
    ];
}
