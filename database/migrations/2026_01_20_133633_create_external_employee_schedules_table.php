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
        Schema::connection('external_employees')->create('external_employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("external_employee_id");
            $table->date("dtr_date");
            $table->string('first_in')->nullable();
            $table->string('first_out')->nullable();
            $table->string('second_in')->nullable();
            $table->string('second_out')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('external_employees')->dropIfExists('external_employee_schedules');
    }
};
