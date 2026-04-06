<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use App\Models\ExternalEmployeeSchedule;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    public function handleRecordCreation(array $data): Model
    {
        $employeeExternalId = Auth::guard('external')->user()->id;
        if (isset($data['schedule_4entries']) && is_array($data['schedule_4entries'])) {
            $data['schedule_4entries'] = array_map(function ($item) use ($employeeExternalId) {
                $item['external_employee_id'] = $employeeExternalId;
                if (isset($item['is_shifting']) && $item['is_shifting']) {
                    $item['first_out'] = null;
                    $item['second_in'] = null;
                }
                return $item;
            }, $data['schedule_4entries']);
        }

        foreach ($data['schedule_4entries'] as $entry) {
            $lastRecord = ExternalEmployeeSchedule::create($entry);
        }

        return $lastRecord;
    }
}
