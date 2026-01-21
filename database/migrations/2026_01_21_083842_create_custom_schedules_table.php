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
        Schema::connection('external_employees')->create('custom_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('portal_setting_id')->nullable();
            $table->string('dtr_date')->nullable();
            $table->boolean('is_shifting')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('external_employees')->dropIfExists('custom_schedules');
    }
};
