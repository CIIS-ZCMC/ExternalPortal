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
        Schema::connection('external_employees')->table('administrators', function (Blueprint $table) {
            $table->json('assigned_agencies')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('external_employees')->table('administrators', function (Blueprint $table) {
            $table->dropColumn('assigned_agencies');
        });
    }
};
