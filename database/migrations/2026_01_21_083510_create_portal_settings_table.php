<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('external_employees')->create('portal_settings', function (Blueprint $table) {
            $table->id();
            $table->string('external_employee_id')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->string('schedule_type')->nullable()->comment("normal, shifting, custom");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('external_employees')->dropIfExists('portal_settings');
    }
};
