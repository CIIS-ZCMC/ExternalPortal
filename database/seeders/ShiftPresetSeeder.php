<?php

namespace Database\Seeders;

use App\Models\ShiftPreset;
use Illuminate\Database\Seeder;

class ShiftPresetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $presets = [
            [
                'name'         => 'Morning: 06:00 AM - 02:00 PM',
                'code'         => 'MORNING_6_2',
                'is_shifting'  => true,
                'first_in'     => '06:00:00',
                'first_out'    => null,
                'second_in'    => null,
                'second_out'   => '14:00:00',
                'total_hours'  => '8',
                'badge_label'  => '8h',
                'color'        => '#0284c7',
                'border_color' => '#0369a1',
                'is_active'    => true,
                'sort_order'   => 1,
            ],
            [
                'name'         => 'Afternoon: 02:00 PM - 10:00 PM',
                'code'         => 'AFTERNOON_2_10',
                'is_shifting'  => true,
                'first_in'     => '14:00:00',
                'first_out'    => null,
                'second_in'    => null,
                'second_out'   => '22:00:00',
                'total_hours'  => '8',
                'badge_label'  => '8h',
                'color'        => '#d97706',
                'border_color' => '#b45309',
                'is_active'    => true,
                'sort_order'   => 2,
            ],
            [
                'name'         => 'Night: 10:00 PM - 06:00 AM',
                'code'         => 'NIGHT_10_6',
                'is_shifting'  => true,
                'first_in'     => '22:00:00',
                'first_out'    => null,
                'second_in'    => null,
                'second_out'   => '06:00:00',
                'total_hours'  => '8',
                'badge_label'  => 'Grave',
                'color'        => '#4f46e5',
                'border_color' => '#4338ca',
                'is_active'    => true,
                'sort_order'   => 3,
            ],
            [
                'name'         => 'Office: 08:00 AM - 05:00 PM',
                'code'         => 'OFFICE_8_5',
                'is_shifting'  => false,
                'first_in'     => '08:00:00',
                'first_out'    => '12:00:00',
                'second_in'    => '13:00:00',
                'second_out'   => '17:00:00',
                'total_hours'  => '8',
                'badge_label'  => 'Std',
                'color'        => '#059669',
                'border_color' => '#047857',
                'is_active'    => true,
                'sort_order'   => 4,
            ],
            [
                'name'         => 'Day: 08:00 AM - 04:00 PM',
                'code'         => 'DAY_8_4',
                'is_shifting'  => true,
                'first_in'     => '08:00:00',
                'first_out'    => null,
                'second_in'    => null,
                'second_out'   => '16:00:00',
                'total_hours'  => '8',
                'badge_label'  => '8h',
                'color'        => '#0d9488',
                'border_color' => '#0f766e',
                'is_active'    => true,
                'sort_order'   => 5,
            ],
            [
                'name'         => '24-Hour: 08:00 AM - 08:00 AM',
                'code'         => 'DUTY_24H',
                'is_shifting'  => true,
                'first_in'     => '08:00:00',
                'first_out'    => null,
                'second_in'    => null,
                'second_out'   => '08:00:00',
                'total_hours'  => '24',
                'badge_label'  => '24h',
                'color'        => '#e11d48',
                'border_color' => '#be123c',
                'is_active'    => true,
                'sort_order'   => 6,
            ],
        ];

        foreach ($presets as $preset) {
            ShiftPreset::updateOrCreate(
                ['code' => $preset['code']],
                $preset
            );
        }
    }
}
