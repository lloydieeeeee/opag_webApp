<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds `dynamic_deductions` JSON column to `payroll_record`.
 *
 * This column stores any deduction that exists in `payroll_deductions` but
 * does NOT have a dedicated hard column in `payroll_record`.
 *
 * Format: { "<deduction_id>": <amount>, ... }
 * Example: { "10": 400.00, "11": 250.00 }
 *
 * Run: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_record', function (Blueprint $table) {
            $table->json('dynamic_deductions')
                  ->nullable()
                  ->after('remarks')
                  ->comment('Stores amounts for deductions added to payroll_deductions that have no hard column. Key = deduction id, value = amount.');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_record', function (Blueprint $table) {
            $table->dropColumn('dynamic_deductions');
        });
    }
};