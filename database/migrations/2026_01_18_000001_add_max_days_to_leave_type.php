<?php
// database/migrations/xxxx_add_max_days_to_leave_type.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leave_type', function (Blueprint $table) {
            $table->decimal('max_days', 6, 2)
                  ->nullable()
                  ->after('accrual_rate')
                  ->comment('Maximum days per application; null = unlimited');
        });
    }

    public function down(): void
    {
        Schema::table('leave_type', function (Blueprint $table) {
            $table->dropColumn('max_days');
        });
    }
};