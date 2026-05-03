<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_deductions', function (Blueprint $table) {
            // parent_id = null means this is a group header (e.g. "GSIS", "PAGIBIG")
            $table->unsignedBigInteger('parent_id')->nullable()->after('id')->default(null);
            // Whether this sub-deduction is actually subtracted from take-home pay
            $table->boolean('is_deducted')->default(true)->after('is_active');

            $table->foreign('parent_id')
                  ->references('id')
                  ->on('payroll_deductions')
                  ->nullOnDelete();
        });

        // Seed the structured hierarchy based on requirements
        // First create the group headers (parent_id = null, no rate — they are just labels)
        DB::statement("
            INSERT INTO payroll_deductions
                (name, type, rate, rate_value, rate_type, limit_amount, status, is_active, is_deducted, sort_order, parent_id, created_at, updated_at)
            VALUES
                -- GSIS group header
                ('GSIS', 'Fixed', NULL, 0, 'flat', NULL, 'GSIS', 1, 0, 10, NULL, NOW(), NOW()),
                -- PAGIBIG group header
                ('PAGIBIG', 'Fixed', NULL, 0, 'flat', NULL, 'PAGIBIG', 1, 0, 20, NULL, NOW(), NOW()),
                -- PHILHEALTH group header
                ('PHILHEALTH', 'Fixed', NULL, 0, 'flat', NULL, 'PHILHEALTH', 1, 0, 30, NULL, NOW(), NOW()),
                -- Withholding Tax (standalone, typable, deducted)
                ('Withholding Tax', 'Not Fixed', NULL, 0, 'flat', NULL, 'Withholding Tax', 1, 1, 40, NULL, NOW(), NOW()),
                -- Others group header
                ('Others', 'Fixed', NULL, 0, 'flat', NULL, 'Others', 1, 0, 50, NULL, NOW(), NOW()),
                -- CNGWPC group header
                ('CNGWPC', 'Fixed', NULL, 0, 'flat', NULL, 'CNGWPC', 1, 0, 60, NULL, NOW(), NOW())
        ");

        // Fetch newly-inserted group IDs
        $gsis      = DB::table('payroll_deductions')->where('name', 'GSIS')->whereNull('parent_id')->where('sort_order', 10)->value('id');
        $pagibig   = DB::table('payroll_deductions')->where('name', 'PAGIBIG')->whereNull('parent_id')->where('sort_order', 20)->value('id');
        $philhealth= DB::table('payroll_deductions')->where('name', 'PHILHEALTH')->whereNull('parent_id')->where('sort_order', 30)->value('id');
        $others    = DB::table('payroll_deductions')->where('name', 'Others')->whereNull('parent_id')->where('sort_order', 50)->value('id');
        $cngwpc    = DB::table('payroll_deductions')->where('name', 'CNGWPC')->whereNull('parent_id')->where('sort_order', 60)->value('id');

        $rows = [];

        // GSIS children
        $gsisSubs = [
            ['Life Retirement Insurance – Personal Share', 'Fixed',     '9%',  0.09,   'percent', null,   true,  1],
            ['Life Retirement Insurance – Government Share','Fixed',     '12%', 0.12,   'percent', null,   false, 2], // not deducted
            ['ECF (Employee Compensation Fund)',            'Not Fixed', null,  0,      'flat',    null,   false, 3], // typable, not deducted
            ['Conso Loan',                                  'Not Fixed', null,  0,      'flat',    null,   true,  4],
            ['Policy Loan',                                 'Not Fixed', null,  0,      'flat',    null,   true,  5],
            ['Emergency Loan',                              'Not Fixed', null,  0,      'flat',    null,   true,  6],
            ['Real Estate Loan',                            'Not Fixed', null,  0,      'flat',    null,   true,  7],
            ['Computer Loan',                               'Not Fixed', null,  0,      'flat',    null,   true,  8],
            ['GFAL',                                        'Not Fixed', null,  0,      'flat',    null,   true,  9],
            ['MPL',                                         'Not Fixed', null,  0,      'flat',    null,   true,  10],
            ['MPL Lite',                                    'Not Fixed', null,  0,      'flat',    null,   true,  11],
        ];
        foreach ($gsisSubs as [$n,$t,$r,$rv,$rt,$lim,$ded,$so]) {
            DB::table('payroll_deductions')->insert([
                'name'=>$n,'type'=>$t,'rate'=>$r,'rate_value'=>$rv,'rate_type'=>$rt,
                'limit_amount'=>$lim,'status'=>$n,'is_active'=>1,'is_deducted'=>$ded,
                'sort_order'=>$so,'parent_id'=>$gsis,'created_at'=>now(),'updated_at'=>now()
            ]);
        }

        // PAGIBIG children
        $pagibigSubs = [
            ['Employee Share',  'Not Fixed', null, 0, 'flat', null, true,  1],
            ['Employer Share',  'Not Fixed', null, 0, 'flat', null, false, 2], // not deducted
            ['MPL',             'Not Fixed', null, 0, 'flat', null, true,  3],
            ['Calamity Loan',   'Not Fixed', null, 0, 'flat', null, true,  4],
            ['Housing Loan',    'Not Fixed', null, 0, 'flat', null, true,  5],
            ['MP2',             'Not Fixed', null, 0, 'flat', null, true,  6],
        ];
        foreach ($pagibigSubs as [$n,$t,$r,$rv,$rt,$lim,$ded,$so]) {
            DB::table('payroll_deductions')->insert([
                'name'=>$n,'type'=>$t,'rate'=>$r,'rate_value'=>$rv,'rate_type'=>$rt,
                'limit_amount'=>$lim,'status'=>$n,'is_active'=>1,'is_deducted'=>$ded,
                'sort_order'=>$so,'parent_id'=>$pagibig,'created_at'=>now(),'updated_at'=>now()
            ]);
        }

        // PHILHEALTH children
        $philSubs = [
            ['Personal Share',   'Fixed', '2.5%', 0.025, 'percent', 2500.00, true,  1],
            ['Government Share', 'Fixed', '2.5%', 0.025, 'percent', 2500.00, false, 2], // not deducted
        ];
        foreach ($philSubs as [$n,$t,$r,$rv,$rt,$lim,$ded,$so]) {
            DB::table('payroll_deductions')->insert([
                'name'=>$n,'type'=>$t,'rate'=>$r,'rate_value'=>$rv,'rate_type'=>$rt,
                'limit_amount'=>$lim,'status'=>$n,'is_active'=>1,'is_deducted'=>$ded,
                'sort_order'=>$so,'parent_id'=>$philhealth,'created_at'=>now(),'updated_at'=>now()
            ]);
        }

        // Others children
        $otherSubs = [
            ['DBP Loan', 'Not Fixed', null, 0, 'flat', null, true, 1],
            ['LBP Loan', 'Not Fixed', null, 0, 'flat', null, true, 2],
        ];
        foreach ($otherSubs as [$n,$t,$r,$rv,$rt,$lim,$ded,$so]) {
            DB::table('payroll_deductions')->insert([
                'name'=>$n,'type'=>$t,'rate'=>$r,'rate_value'=>$rv,'rate_type'=>$rt,
                'limit_amount'=>$lim,'status'=>$n,'is_active'=>1,'is_deducted'=>$ded,
                'sort_order'=>$so,'parent_id'=>$others,'created_at'=>now(),'updated_at'=>now()
            ]);
        }

        // CNGWPC children
        $cngwpcSubs = [
            'Capital Share','Kiddie Savings','Savings','Regular Loan',
            'In Crisis Loan','Coop Canteen','Coop Store','Calamity Loan',
            'Abuloy Program','Handog Kabuhayan / Ituro Mo',
            'Back to Back Loan / Special Loan','Petty Cash','Commodity Loan',
        ];
        foreach ($cngwpcSubs as $i => $n) {
            DB::table('payroll_deductions')->insert([
                'name'=>$n,'type'=>'Not Fixed','rate'=>null,'rate_value'=>0,'rate_type'=>'flat',
                'limit_amount'=>null,'status'=>$n,'is_active'=>1,'is_deducted'=>true,
                'sort_order'=>$i+1,'parent_id'=>$cngwpc,'created_at'=>now(),'updated_at'=>now()
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('payroll_deductions', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'is_deducted']);
        });
    }
};