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
        Schema::table('external_employees', function (Blueprint $table) {
            if (!Schema::hasColumn('external_employees', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
        });

        // Ensure all existing external employees are marked as verified so they are not locked out
        DB::table('external_employees')
            ->whereNull('email_verified_at')
            ->update([
                'email_verified_at' => DB::raw('COALESCE(created_at, NOW())')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_employees', function (Blueprint $table) {
            if (Schema::hasColumn('external_employees', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
        });
    }
};
