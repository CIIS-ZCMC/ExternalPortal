<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::connection('external_employees')->hasTable('shift_presets')) {
            Schema::connection('external_employees')->create('shift_presets', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable();
                $table->boolean('is_shifting')->default(true);
                $table->string('first_in')->nullable();
                $table->string('first_out')->nullable();
                $table->string('second_in')->nullable();
                $table->string('second_out')->nullable();
                $table->string('total_hours')->nullable();
                $table->string('badge_label')->nullable();
                $table->string('color')->default('#0284c7');
                $table->string('border_color')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });

            // Seed standard presets if empty
            $presets = [
                [
                    'name' => 'Morning: 06:00 AM - 02:00 PM',
                    'code' => 'MORNING_6_2',
                    'is_shifting' => 1,
                    'first_in' => '06:00:00',
                    'first_out' => null,
                    'second_in' => null,
                    'second_out' => '14:00:00',
                    'total_hours' => '8',
                    'badge_label' => '8h',
                    'color' => '#0284c7',
                    'border_color' => '#0369a1',
                    'is_active' => 1,
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Afternoon: 02:00 PM - 10:00 PM',
                    'code' => 'AFTERNOON_2_10',
                    'is_shifting' => 1,
                    'first_in' => '14:00:00',
                    'first_out' => null,
                    'second_in' => null,
                    'second_out' => '22:00:00',
                    'total_hours' => '8',
                    'badge_label' => '8h',
                    'color' => '#d97706',
                    'border_color' => '#b45309',
                    'is_active' => 1,
                    'sort_order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Night: 10:00 PM - 06:00 AM',
                    'code' => 'NIGHT_10_6',
                    'is_shifting' => 1,
                    'first_in' => '22:00:00',
                    'first_out' => null,
                    'second_in' => null,
                    'second_out' => '06:00:00',
                    'total_hours' => '8',
                    'badge_label' => 'Grave',
                    'color' => '#4f46e5',
                    'border_color' => '#4338ca',
                    'is_active' => 1,
                    'sort_order' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Office: 08:00 AM - 05:00 PM',
                    'code' => 'OFFICE_8_5',
                    'is_shifting' => 0,
                    'first_in' => '08:00:00',
                    'first_out' => '12:00:00',
                    'second_in' => '13:00:00',
                    'second_out' => '17:00:00',
                    'total_hours' => '8',
                    'badge_label' => 'Std',
                    'color' => '#059669',
                    'border_color' => '#047857',
                    'is_active' => 1,
                    'sort_order' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Day: 08:00 AM - 04:00 PM',
                    'code' => 'DAY_8_4',
                    'is_shifting' => 1,
                    'first_in' => '08:00:00',
                    'first_out' => null,
                    'second_in' => null,
                    'second_out' => '16:00:00',
                    'total_hours' => '8',
                    'badge_label' => '8h',
                    'color' => '#0d9488',
                    'border_color' => '#0f766e',
                    'is_active' => 1,
                    'sort_order' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => '24-Hour: 08:00 AM - 08:00 AM',
                    'code' => 'DUTY_24H',
                    'is_shifting' => 1,
                    'first_in' => '08:00:00',
                    'first_out' => null,
                    'second_in' => null,
                    'second_out' => '08:00:00',
                    'total_hours' => '24',
                    'badge_label' => '24h',
                    'color' => '#e11d48',
                    'border_color' => '#be123c',
                    'is_active' => 1,
                    'sort_order' => 6,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::connection('external_employees')->table('shift_presets')->insert($presets);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('external_employees')->dropIfExists('shift_presets');
    }
};
