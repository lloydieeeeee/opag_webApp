<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRecord extends Model
{
    protected $table      = 'payroll_record';
    protected $primaryKey = 'payroll_id';

    protected $fillable = [
        'period_id','employee_id','gross_salary',
        'gsis_ee','gsis_govt','pagibig_ee','pagibig_govt',
        'philhealth_ee','philhealth_govt','withholding_tax',
        'loan_dbp','loan_lbp','loan_cngwmpc','loan_paracle',
        'allowance_pera','allowance_rata','allowance_other',
        'total_deductions','total_allowances','net_pay','remarks',
        'gsis_ec','gsis_real_estate','gsis_conso','gsis_emergency',
        'gsis_mpl','gsis_mpl_lite','gsis_gfal','gsis_computer',
        'gsis_policy','pagibig_mpl','pagibig_calamity',
        'philhealth_govt_share','overpayment',
        'employee_code','designation',
    ];

    protected $casts = ['gross_salary'=>'decimal:2','net_pay'=>'decimal:2'];

    // ─── Relationships ─────────────────────────────────────────────

    public function period()
    {
        return $this->belongsTo(PayrollPeriod::class, 'period_id', 'period_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    // ─── Static Computation ────────────────────────────────────────

    public static function computeFromSalary(float $salary): array
    {
        // GSIS: 9% employee share, 12% government share
        $gsisEe   = round($salary * 0.09, 2);
        $gsisGovt = round($salary * 0.12, 2);

        // Pag-IBIG: 2% employee (max ₱100), 2% government
        $pagibigEe   = min(round($salary * 0.02, 2), 100.00);
        $pagibigGovt = min(round($salary * 0.02, 2), 100.00);

        // PhilHealth: 5% total, split equally
        $philhealthEe   = round($salary * 0.025, 2);
        $philhealthGovt = round($salary * 0.025, 2);

        // Withholding tax (BIR 2023 monthly table)
        $wtax = static::computeWithholdingTax($salary);

        $totalDeductions = $gsisEe + $pagibigEe + $philhealthEe + $wtax;
        $netPay          = $salary - $totalDeductions;

        return [
            'gross_salary'     => $salary,
            'gsis_ee'          => $gsisEe,
            'gsis_govt'        => $gsisGovt,
            'pagibig_ee'       => $pagibigEe,
            'pagibig_govt'     => $pagibigGovt,
            'philhealth_ee'    => $philhealthEe,
            'philhealth_govt'  => $philhealthGovt,
            'withholding_tax'  => $wtax,
            'loan_dbp'         => 0,
            'loan_lbp'         => 0,
            'loan_cngwmpc'     => 0,
            'loan_paracle'     => 0,
            'allowance_pera'   => 0,
            'allowance_rata'   => 0,
            'allowance_other'  => 0,
            'total_allowances' => 0,
            'total_deductions' => $totalDeductions,
            'net_pay'          => $netPay,
        ];
    }

    private static function computeWithholdingTax(float $monthly): float
    {
        // BIR TRAIN Law — monthly withholding tax table
        if ($monthly <= 20833)  return 0;
        if ($monthly <= 33332)  return round(($monthly - 20833)  * 0.20, 2);
        if ($monthly <= 66666)  return round(2500  + ($monthly - 33333)  * 0.25, 2);
        if ($monthly <= 166666) return round(10833 + ($monthly - 66667)  * 0.30, 2);
        if ($monthly <= 666666) return round(40833 + ($monthly - 166667) * 0.32, 2);
        return round(200833 + ($monthly - 666667) * 0.35, 2);
    }
}