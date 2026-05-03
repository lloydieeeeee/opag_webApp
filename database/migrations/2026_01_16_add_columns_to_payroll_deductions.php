<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_deductions', function (Blueprint $table) {
            // Parent-child grouping
            if (!Schema::hasColumn('payroll_deductions', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
                $table->foreign('parent_id')->references('id')->on('payroll_deductions')->nullOnDelete();
            }

            // Whether this entry DEDUCTS from salary (true) or ADDS to it (false = allowance/addition)
            if (!Schema::hasColumn('payroll_deductions', 'is_deducted')) {
                $table->boolean('is_deducted')->default(true)->after('is_active');
            }

            // entry_kind: 'deduction' | 'addition'
            if (!Schema::hasColumn('payroll_deductions', 'entry_kind')) {
                $table->enum('entry_kind', ['deduction', 'addition'])->default('deduction')->after('is_deducted');
            }
        });

        // Seed known additions from existing data (PERA=7, RA=8, TA=9)
        DB::table('payroll_deductions')
            ->whereIn('id', [7, 8, 9])
            ->update(['is_deducted' => false, 'entry_kind' => 'addition']);

        // Government shares are NOT deducted from employee take-home
        DB::table('payroll_deductions')
            ->where('name', 'like', "%Gov't%")
            ->update(['is_deducted' => false]);
    }

    public function down(): void
    {
        Schema::table('payroll_deductions', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'is_deducted', 'entry_kind']);
        });
    }
};