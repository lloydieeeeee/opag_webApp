<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_deductions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('type', 20)->default('Fixed'); // Fixed | Not Fixed
            $table->string('rate', 50)->nullable();       // e.g. "9%", "2.5%", "₱2000.00"
            $table->decimal('rate_value', 10, 4)->default(0); // numeric rate (fraction for %, flat for fixed)
            $table->enum('rate_type', ['percent', 'flat'])->default('flat');
            $table->decimal('limit_amount', 12, 2)->nullable(); // max cap, null = N/A
            $table->string('status', 100)->nullable();    // label shown in Status column
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed with the deductions from the screenshot + Excel file
        DB::table('payroll_deductions')->insert([
            ['name'=>'PhilHealth Gov\'t Share',          'type'=>'Fixed',     'rate'=>'2.5%',      'rate_value'=>0.025,   'rate_type'=>'percent','limit_amount'=>2500.00,'status'=>'PhilHealth Gov\'t Share',          'is_active'=>1,'sort_order'=>1, 'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'PhilHealth Employee Share',         'type'=>'Fixed',     'rate'=>'2.5%',      'rate_value'=>0.025,   'rate_type'=>'percent','limit_amount'=>2500.00,'status'=>'PhilHealth Employee Share',         'is_active'=>1,'sort_order'=>2, 'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'PAGIBIG Gov\'t Share',              'type'=>'Fixed',     'rate'=>'₱200.00',   'rate_value'=>200,     'rate_type'=>'flat',  'limit_amount'=>null,   'status'=>'PAGIBIG Gov\'t Share',              'is_active'=>1,'sort_order'=>3, 'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'GSIS Gov\'t Share',                 'type'=>'Fixed',     'rate'=>'12%',       'rate_value'=>0.12,    'rate_type'=>'percent','limit_amount'=>null,   'status'=>'GSIS Gov\'t Share',                 'is_active'=>1,'sort_order'=>4, 'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'GSIS Employee Share',               'type'=>'Fixed',     'rate'=>'9%',        'rate_value'=>0.09,    'rate_type'=>'percent','limit_amount'=>null,   'status'=>'GSIS Employee Share',               'is_active'=>1,'sort_order'=>5, 'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'ECF (Employee Compensation Fund)',  'type'=>'Fixed',     'rate'=>'₱100.00',   'rate_value'=>100,     'rate_type'=>'flat',  'limit_amount'=>null,   'status'=>'ECF (Employee Compensation Fund)',  'is_active'=>1,'sort_order'=>6, 'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'PERA',                              'type'=>'Fixed',     'rate'=>'₱2,000.00', 'rate_value'=>2000,    'rate_type'=>'flat',  'limit_amount'=>null,   'status'=>'PERA',                              'is_active'=>1,'sort_order'=>7, 'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'RA (Representation Allowance)',     'type'=>'Fixed',     'rate'=>'₱9,500.00', 'rate_value'=>9500,    'rate_type'=>'flat',  'limit_amount'=>null,   'status'=>'RA (Representation Allowance)',     'is_active'=>1,'sort_order'=>8, 'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'TA (Transportation Allowance)',     'type'=>'Fixed',     'rate'=>'₱9,500.00', 'rate_value'=>9500,    'rate_type'=>'flat',  'limit_amount'=>null,   'status'=>'TA (Transportation Allowance)',     'is_active'=>1,'sort_order'=>9, 'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'COOP Capital Share',                'type'=>'Not Fixed', 'rate'=>'₱400',      'rate_value'=>400,     'rate_type'=>'flat',  'limit_amount'=>null,   'status'=>'COOP Capital Share',                'is_active'=>1,'sort_order'=>10,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_deductions');
    }
};