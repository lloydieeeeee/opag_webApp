<?php

// ── REPLACE your storePeriod() method in PayrollController.php with this ──

public function storePeriod(Request $request)
{
    $request->validate([
        'month'         => 'required|integer|min:1|max:12',
        'year'          => 'required|integer|min:2020|max:2099',
        'period_label'  => 'required|string|max:100',
        'employee_ids'  => 'required|array|min:1',
        'employee_ids.*'=> 'integer|exists:employee,employee_id',
    ]);

    // Check for duplicate
    $exists = \App\Models\PayrollPeriod::where('month', $request->month)
        ->where('year', $request->year)->exists();

    if ($exists) {
        return back()->withErrors(['month' => 'Payroll period already exists for that month/year.'])
                     ->withInput();
    }

    \Illuminate\Support\Facades\DB::transaction(function () use ($request) {

        $period = \App\Models\PayrollPeriod::create([
            'period_label' => $request->period_label,
            'month'        => $request->month,
            'year'         => $request->year,
            'status'       => 'DRAFT',
            'created_by'   => \Illuminate\Support\Facades\Auth::id(),
        ]);

        // Only generate for the selected employee IDs
        $employees = \App\Models\Employee::with('position')
            ->whereIn('employee_id', $request->employee_ids)
            ->where('is_active', 1)
            ->get();

        foreach ($employees as $emp) {
            $gross = $emp->salary;

            // ── GSIS ──
            $gsisEe   = round($gross * 0.09, 2);
            $gsisGovt = round($gross * 0.12, 2);
            $gsisEc   = 100.00;

            // ── Pag-Ibig ──
            $pagibigEe   = 200.00;
            $pagibigGovt = 200.00;

            // ── PhilHealth 5% split 50/50, capped at ₱5,000 each side ──
            $phicBase = min(round($gross * 0.05, 2), 10000);
            $phicEe   = round($phicBase / 2, 2);
            $phicGovt = round($phicBase / 2, 2);

            // ── PERA ──
            $pera = 2000.00;
            $rata = 0.00;
            $ta   = 0.00;

            $totalDeductions = $gsisEe + $gsisEc + $pagibigEe + $phicEe;
            $totalAllowances = $pera + $rata + $ta;
            $netPay          = $gross - $totalDeductions + $totalAllowances;

            \App\Models\PayrollRecord::create([
                'period_id'        => $period->period_id,
                'employee_id'      => $emp->employee_id,
                'employee_code'    => null,
                'designation'      => optional($emp->position)->position_code,
                'gross_salary'     => $gross,
                'gsis_ee'          => $gsisEe,
                'gsis_govt'        => $gsisGovt,
                'gsis_ec'          => $gsisEc,
                'pagibig_ee'       => $pagibigEe,
                'pagibig_govt'     => $pagibigGovt,
                'philhealth_ee'    => $phicEe,
                'philhealth_govt'  => $phicGovt,
                'withholding_tax'  => 0,
                'loan_dbp'         => 0,
                'loan_lbp'         => 0,
                'loan_cngwmpc'     => 0,
                'loan_paracle'     => 0,
                'gsis_real_estate' => 0,
                'gsis_conso'       => 0,
                'gsis_emergency'   => 0,
                'gsis_mpl'         => 0,
                'gsis_mpl_lite'    => 0,
                'gsis_gfal'        => 0,
                'gsis_computer'    => 0,
                'gsis_policy'      => 0,
                'pagibig_mpl'      => 0,
                'pagibig_calamity' => 0,
                'overpayment'      => 0,
                'allowance_pera'   => $pera,
                'allowance_rata'   => $rata,
                'allowance_other'  => $ta,
                'total_deductions' => $totalDeductions,
                'total_allowances' => $totalAllowances,
                'net_pay'          => $netPay,
            ]);
        }
    });

    return redirect()->route('payroll.index')
        ->with('success', 'Payroll period "' . $request->period_label . '" created for ' . count($request->employee_ids) . ' employees.');
}