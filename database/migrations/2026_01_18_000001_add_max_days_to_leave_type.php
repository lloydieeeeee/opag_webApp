<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_type', function (Blueprint $table) {
            if (! Schema::hasColumn('leave_type', 'max_days')) {
                $table->decimal('max_days', 8, 2)->nullable()->after('accrual_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leave_type', function (Blueprint $table) {
            if (Schema::hasColumn('leave_type', 'max_days')) {
                $table->dropColumn('max_days');
            }
        });
    }
};