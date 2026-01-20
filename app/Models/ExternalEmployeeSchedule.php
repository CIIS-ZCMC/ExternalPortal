<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalEmployeeSchedule extends Model
{

    protected $table = 'external_employee_schedules';

    protected $connection = 'external_employees';

    protected $fillable = [
        'external_employee_id',
        'dtr_date',
        'first_in',
        'first_out',
        'second_in',
        'second_out',
    ];
}
