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
        if (Schema::hasTable('external_employees')) {
            Schema::table('external_employees', function (Blueprint $table) {
                if (!Schema::hasColumn('external_employees', 'is_2fa_enabled')) {
                    $table->boolean('is_2fa_enabled')->default(false)->after('password');
                }
                if (!Schema::hasColumn('external_employees', 'two_factor_confirmed_at')) {
                    $table->timestamp('two_factor_confirmed_at')->nullable()->after('is_2fa_enabled');
                }
            });
        }

        if (Schema::hasTable('administrators')) {
            Schema::table('administrators', function (Blueprint $table) {
                if (!Schema::hasColumn('administrators', 'is_2fa_enabled')) {
                    $table->boolean('is_2fa_enabled')->default(false)->after('password');
                }
                if (!Schema::hasColumn('administrators', 'two_factor_confirmed_at')) {
                    $table->timestamp('two_factor_confirmed_at')->nullable()->after('is_2fa_enabled');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('external_employees')) {
            Schema::table('external_employees', function (Blueprint $table) {
                $columns = array_filter(['is_2fa_enabled', 'two_factor_confirmed_at'], fn($col) => Schema::hasColumn('external_employees', $col));
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }

        if (Schema::hasTable('administrators')) {
            Schema::table('administrators', function (Blueprint $table) {
                $columns = array_filter(['is_2fa_enabled', 'two_factor_confirmed_at'], fn($col) => Schema::hasColumn('administrators', $col));
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
