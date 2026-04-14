<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\PayrollDeduction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    /* ══════════════════════════════════════════════════════
       INDEX
    ══════════════════════════════════════════════════════ */
    public function index(Request $request)
    {
        $periods = PayrollPeriod::orderByDesc('year')->orderByDesc('month')->get();
        $selectedPeriodId = $request->query('period_id', optional($periods->first())->period_id);
        $selectedPeriod   = $periods->find($selectedPeriodId);

        $records = collect();
        $summary = (object)[
            'gross'      => 0, 'deductions' => 0,
            'net'        => 0, 'employees'  => 0,
            'pera_total' => 0, 'gsis_ee'    => 0,
            'philhealth' => 0, 'pagibig'    => 0,
            'wtax'       => 0,
        ];

        if ($selectedPeriod) {
            $records = PayrollRecord::with(['employee.position', 'employee.department'])
                ->where('period_id', $selectedPeriod->period_id)
                ->get();

            $summary = (object)[
                'gross'      => $records->sum('gross_salary'),
                'deductions' => $records->sum('total_deductions'),
                'net'        => $records->sum('net_pay'),
                'employees'  => $records->count(),
                'pera_total' => $records->sum('allowance_pera'),
                'gsis_ee'    => $records->sum('gsis_ee'),
                'philhealth' => $records->sum('philhealth_ee'),
                'pagibig'    => $records->sum('pagibig_ee'),
                'wtax'       => $records->sum('withholding_tax'),
            ];
        }

        return view('payroll.index', compact(
            'periods', 'selectedPeriod', 'records', 'summary'
        ));
    }

    /* ══════════════════════════════════════════════════════
       CREATE PERIOD FORM
    ══════════════════════════════════════════════════════ */
    public function createPeriod()
    {
        // DB table is `employee` (singular) — Employee model must have $table = 'employee'
        $employees = Employee::with(['position', 'department'])
            ->where('is_active', 1)
            ->orderBy('last_name')
            ->get();

        return view('payroll.create', compact('employees'));
    }

    /* ══════════════════════════════════════════════════════
       STORE PERIOD + RECORDS
    ══════════════════════════════════════════════════════ */
    public function storePeriod(Request $request)
    {
        $request->validate([
            'month'          => 'required|integer|between:1,12',
            'year'           => 'required|integer|min:2020|max:2099',
            'period_label'   => 'required|string|max:100',
            'employee_ids'   => 'required|array|min:1',
            // FIX #1: table is `employee` (singular), not `employees`
            'employee_ids.*' => 'exists:employee,employee_id',
        ]);

        // ── SERVER-SIDE DUPLICATE CHECK ────────────────────────────────────
        $existing = PayrollPeriod::where('month', $request->month)
            ->where('year', $request->year)
            ->first();

        if ($existing) {
            return back()->withErrors([
                'month' => "A payroll period for {$existing->period_label} already exists ({$existing->status}). Please select a different month/year.",
            ]);
        }
        // ──────────────────────────────────────────────────────────────────

        DB::transaction(function () use ($request) {

            $period = PayrollPeriod::create([
                'period_label' => $request->period_label,
                'month'        => $request->month,
                'year'         => $request->year,
                'status'       => 'DRAFT',
                'created_by'   => Auth::id(),
            ]);

            // Load active deductions from `payroll_deductions` table, keyed by name
            $dbDeductions = PayrollDeduction::where('is_active', 1)->get()->keyBy('name');

            // Helper: resolve amount from a deduction config row
            $computeAmount = function ($record, float $gross): float {
                if (!$record) return 0.0;
                if ($record->rate_type === 'percent') {
                    $amount = round($gross * (float) $record->rate_value, 2);
                    if ($record->limit_amount !== null) {
                        $amount = min($amount, (float) $record->limit_amount);
                    }
                    return $amount;
                }
                return round((float) $record->rate_value, 2);
            };

            $overrides = $request->input('overrides', []);

            foreach ($request->employee_ids as $empId) {
                $employee = Employee::find($empId);
                if (!$employee) continue;

                $gross = (float) ($employee->salary ?? 0);

                // ── Fixed deductions (from payroll_deductions table) ────────
                // Exact name matches from DB data:
                $gsisEe   = $computeAmount($dbDeductions->get('GSIS Employee Share'), $gross);   // 9%
                $gsisGovt = $computeAmount($dbDeductions->get("GSIS Gov't Share"), $gross);      // 40%
                $gsisEc   = $computeAmount($dbDeductions->get('ECF (Employee Compensation Fund)'), $gross); // flat 100

                // FIX #2: "PAGIBIG Employee Share" does NOT exist in DB.
                // DB only has "PAGIBIG Gov't Share" (flat 200). Use it for both sides.
                $pagibigEe = $computeAmount($dbDeductions->get("PAGIBIG Gov't Share"), $gross); // flat 200
                $pagibigGv = $computeAmount($dbDeductions->get("PAGIBIG Gov't Share"), $gross); // flat 200

                $phicEe   = $computeAmount($dbDeductions->get('PhilHealth Employee Share'), $gross); // 2.5% cap 2500
                $phicGovt = $computeAmount($dbDeductions->get("PhilHealth Gov't Share"), $gross);    // 12% cap 2500

                $pera = $computeAmount($dbDeductions->get('PERA'), $gross); // flat 2000

                // ── Per-employee overrides (loan / variable allowance fields) ──
                $ov = $overrides[$empId] ?? [];

                $gsisPolicy     = (float) ($ov['gsis_policy']      ?? 0);
                $gsisEmergency  = (float) ($ov['gsis_emergency']   ?? 0);
                $gsisRealEstate = (float) ($ov['gsis_real_estate'] ?? 0);
                $gsisMpl        = (float) ($ov['gsis_mpl']         ?? 0);
                $gsisMplLite    = (float) ($ov['gsis_mpl_lite']    ?? 0);
                $gsisGfal       = (float) ($ov['gsis_gfal']        ?? 0);
                $gsisComputer   = (float) ($ov['gsis_computer']    ?? 0);
                $gsisConso      = (float) ($ov['gsis_conso']       ?? 0);
                $pagibigMpl     = (float) ($ov['pagibig_mpl']      ?? 0);
                $pagibigCal     = (float) ($ov['pagibig_calamity'] ?? 0);
                $wtax           = (float) ($ov['withholding_tax']  ?? 0);
                $loanDbp        = (float) ($ov['loan_dbp']         ?? 0);
                $loanLbp        = (float) ($ov['loan_lbp']         ?? 0);
                $loanCngwmpc    = (float) ($ov['loan_cngwmpc']     ?? 0);
                $loanParacle    = (float) ($ov['loan_paracle']      ?? 0);
                $overpayment    = (float) ($ov['overpayment']       ?? 0);
                $allowRata      = (float) ($ov['allowance_rata']   ?? 0);
                $allowTa        = (float) ($ov['allowance_ta']     ?? 0);

                // ── Compute totals ──────────────────────────────────────────
                $totalDeductions =
                    $gsisEe + $gsisEc
                    + $gsisPolicy + $gsisEmergency + $gsisRealEstate
                    + $gsisMpl + $gsisMplLite + $gsisGfal + $gsisComputer + $gsisConso
                    + $pagibigEe + $pagibigMpl + $pagibigCal
                    + $phicEe
                    + $wtax + $loanDbp + $loanLbp + $loanCngwmpc + $loanParacle
                    + $overpayment;

                $totalAllowances = $pera + $allowRata + $allowTa;
                $netPay          = $gross - $totalDeductions + $totalAllowances;

                // FIX #3: `allowance_ta` is the actual DB column (not `allowance_other`)
                PayrollRecord::updateOrCreate(
                    [
                        'period_id'   => $period->period_id,
                        'employee_id' => $empId,
                    ],
                    [
                        'employee_code'    => null,
                        'designation'      => optional($employee->position)->position_code,
                        'gross_salary'     => $gross,

                        // GSIS
                        'gsis_ee'          => $gsisEe,
                        'gsis_govt'        => $gsisGovt,
                        'gsis_ec'          => $gsisEc,
                        'gsis_policy'      => $gsisPolicy,
                        'gsis_emergency'   => $gsisEmergency,
                        'gsis_real_estate' => $gsisRealEstate,
                        'gsis_mpl'         => $gsisMpl,
                        'gsis_mpl_lite'    => $gsisMplLite,
                        'gsis_gfal'        => $gsisGfal,
                        'gsis_computer'    => $gsisComputer,
                        'gsis_conso'       => $gsisConso,

                        // PAG-IBIG
                        'pagibig_ee'       => $pagibigEe,
                        'pagibig_govt'     => $pagibigGv,
                        'pagibig_mpl'      => $pagibigMpl,
                        'pagibig_calamity' => $pagibigCal,

                        // PhilHealth
                        'philhealth_ee'    => $phicEe,
                        'philhealth_govt'  => $phicGovt,

                        // Tax & Loans
                        'withholding_tax'  => $wtax,
                        'loan_dbp'         => $loanDbp,
                        'loan_lbp'         => $loanLbp,
                        'loan_cngwmpc'     => $loanCngwmpc,
                        'loan_paracle'     => $loanParacle,
                        'overpayment'      => $overpayment,

                        // Allowances — exact column names from `payroll_record` schema
                        'allowance_pera'   => $pera,
                        'allowance_rata'   => $allowRata,
                        'allowance_ta'     => $allowTa,  // FIX #3: was 'allowance_other'
                        'allowance_other'  => 0,         // spare column

                        // Totals
                        'total_deductions' => $totalDeductions,
                        'total_allowances' => $totalAllowances,
                        'net_pay'          => $netPay,
                    ]
                );
            }
        });

        $count = count($request->employee_ids);

        return redirect()
            ->route('payroll.index')
            ->with('success', "Payroll for {$request->period_label} created successfully with {$count} employee(s).");
    }

    /* ══════════════════════════════════════════════════════
       EDIT A SINGLE RECORD (inline AJAX)
       FIX: PayrollRecord model must have $primaryKey = 'payroll_id'
    ══════════════════════════════════════════════════════ */
    public function updateRecord(Request $request, $payrollId)
    {
        $record = PayrollRecord::findOrFail($payrollId);

        if ($record->period->status === 'FINALIZED') {
            return response()->json(['error' => 'Cannot edit a finalized payroll.'], 422);
        }

        $data = $request->only([
            'gross_salary', 'designation', 'remarks',
            'gsis_ee', 'gsis_govt', 'gsis_ec',
            'gsis_real_estate', 'gsis_conso', 'gsis_emergency',
            'gsis_mpl', 'gsis_mpl_lite', 'gsis_gfal', 'gsis_computer', 'gsis_policy',
            'pagibig_ee', 'pagibig_govt', 'pagibig_mpl', 'pagibig_calamity',
            'philhealth_ee', 'philhealth_govt',
            'withholding_tax', 'loan_dbp', 'loan_lbp', 'loan_cngwmpc', 'loan_paracle',
            'overpayment',
            'allowance_pera', 'allowance_rata', 'allowance_ta', 'allowance_other',
        ]);

        $deductions = collect([
            $data['gsis_ee']          ?? $record->gsis_ee,
            $data['gsis_ec']          ?? $record->gsis_ec,
            $data['gsis_real_estate'] ?? $record->gsis_real_estate,
            $data['gsis_conso']       ?? $record->gsis_conso,
            $data['gsis_emergency']   ?? $record->gsis_emergency,
            $data['gsis_mpl']         ?? $record->gsis_mpl,
            $data['gsis_mpl_lite']    ?? $record->gsis_mpl_lite,
            $data['gsis_gfal']        ?? $record->gsis_gfal,
            $data['gsis_computer']    ?? $record->gsis_computer,
            $data['gsis_policy']      ?? $record->gsis_policy,
            $data['pagibig_ee']       ?? $record->pagibig_ee,
            $data['pagibig_mpl']      ?? $record->pagibig_mpl,
            $data['pagibig_calamity'] ?? $record->pagibig_calamity,
            $data['philhealth_ee']    ?? $record->philhealth_ee,
            $data['withholding_tax']  ?? $record->withholding_tax,
            $data['loan_dbp']         ?? $record->loan_dbp,
            $data['loan_lbp']         ?? $record->loan_lbp,
            $data['loan_cngwmpc']     ?? $record->loan_cngwmpc,
            $data['loan_paracle']     ?? $record->loan_paracle,
            $data['overpayment']      ?? $record->overpayment,
        ])->sum();

        $allowances = collect([
            $data['allowance_pera']  ?? $record->allowance_pera,
            $data['allowance_rata']  ?? $record->allowance_rata,
            $data['allowance_ta']    ?? $record->allowance_ta,    // FIX: was allowance_other
            $data['allowance_other'] ?? $record->allowance_other,
        ])->sum();

        $gross  = (float) ($data['gross_salary'] ?? $record->gross_salary);
        $netPay = $gross - $deductions + $allowances;

        $data['total_deductions'] = $deductions;
        $data['total_allowances'] = $allowances;
        $data['net_pay']          = $netPay;

        $record->update($data);

        return response()->json([
            'success'          => true,
            'net_pay'          => number_format($netPay, 2),
            'total_deductions' => number_format($deductions, 2),
            'total_allowances' => number_format($allowances, 2),
        ]);
    }

    /* ══════════════════════════════════════════════════════
       FINALIZE PERIOD
    ══════════════════════════════════════════════════════ */
    public function finalizePeriod(Request $request, $periodId)
    {
        $period = PayrollPeriod::findOrFail($periodId);
        $period->update(['status' => 'FINALIZED']);

        return back()->with('success', 'Payroll period finalized.');
    }

    /* ══════════════════════════════════════════════════════
       PAYSLIP — employee self-service view
    ══════════════════════════════════════════════════════ */
    public function payslip(Request $request)
    {
        $empId   = Auth::user()->employee_id ?? null;
        $periods = PayrollPeriod::orderByDesc('year')->orderByDesc('month')->get();
        $selectedPeriodId = $request->query('period_id', optional($periods->first())->period_id);

        $record = null;
        if ($empId && $selectedPeriodId) {
            $record = PayrollRecord::with(['employee.position', 'employee.department', 'period'])
                ->where('employee_id', $empId)
                ->where('period_id', $selectedPeriodId)
                ->first();
        }

        return view('payroll.payslip', compact('periods', 'selectedPeriodId', 'record'));
    }

    /* ══════════════════════════════════════════════════════
       PAYSLIP PDF
    ══════════════════════════════════════════════════════ */
    public function payslipPdf(Request $request, $periodId)
    {
        $empId = $request->query('emp_id', Auth::user()->employee_id ?? null);

        $record = PayrollRecord::with(['employee.position', 'employee.department', 'period'])
            ->where('employee_id', $empId)
            ->where('period_id', $periodId)
            ->firstOrFail();

        $pdf = Pdf::loadView('payroll.payslip-pdf', compact('record'))
            ->setPaper('letter', 'portrait');

        return $pdf->stream("payslip_{$record->employee->last_name}_{$record->period->period_label}.pdf");
    }

    /* ══════════════════════════════════════════════════════
       PAYROLL PDF (full list printout)
    ══════════════════════════════════════════════════════ */
    public function payrollPdf($periodId)
    {
        $period  = PayrollPeriod::findOrFail($periodId);
        $records = PayrollRecord::with(['employee.position', 'employee.department'])
            ->where('period_id', $periodId)
            ->get();

        $pdf = Pdf::loadView('payroll.payroll-pdf', compact('period', 'records'))
            ->setPaper('legal', 'landscape');

        return $pdf->stream("payroll_{$period->period_label}.pdf");
    }

    /* ══════════════════════════════════════════════════════
       REMITTANCES VIEW
    ══════════════════════════════════════════════════════ */
    public function remittances(Request $request)
    {
        $periods = PayrollPeriod::orderByDesc('year')->orderByDesc('month')->get();
        $selectedPeriodId = $request->query('period_id', optional($periods->first())->period_id);
        $selectedPeriod   = $periods->find($selectedPeriodId);

        $records = collect();
        if ($selectedPeriod) {
            $records = PayrollRecord::with(['employee'])
                ->where('period_id', $selectedPeriod->period_id)
                ->get();
        }

        $gsis = [
            'ee'          => $records->sum('gsis_ee'),
            'govt'        => $records->sum('gsis_govt'),
            'ec'          => $records->sum('gsis_ec'),
            'mpl'         => $records->sum('gsis_mpl'),
            'policy'      => $records->sum('gsis_policy'),
            'emergency'   => $records->sum('gsis_emergency'),
            'real_estate' => $records->sum('gsis_real_estate'),
            'computer'    => $records->sum('gsis_computer'),
            'gfal'        => $records->sum('gsis_gfal'),
            'mpl_lite'    => $records->sum('gsis_mpl_lite'),
            'conso'       => $records->sum('gsis_conso'),
        ];

        $pagibig = [
            'ee'       => $records->sum('pagibig_ee'),
            'govt'     => $records->sum('pagibig_govt'),
            'mpl'      => $records->sum('pagibig_mpl'),
            'calamity' => $records->sum('pagibig_calamity'),
        ];

        $philhealth = [
            'ee'   => $records->sum('philhealth_ee'),
            'govt' => $records->sum('philhealth_govt'),
        ];

        $loans = [
            'dbp'     => $records->sum('loan_dbp'),
            'lbp'     => $records->sum('loan_lbp'),
            'cngwmpc' => $records->sum('loan_cngwmpc'),
            'paracle' => $records->sum('loan_paracle'),
        ];

        $wtax = $records->sum('withholding_tax');

        return view('payroll.remittances', compact(
            'periods', 'selectedPeriod', 'records',
            'gsis', 'pagibig', 'philhealth', 'loans', 'wtax'
        ));
    }

    /* ══════════════════════════════════════════════════════
       REMITTANCE PDF
    ══════════════════════════════════════════════════════ */
    public function remittancePdf(Request $request, $periodId, $type)
    {
        $period  = PayrollPeriod::findOrFail($periodId);
        $records = PayrollRecord::with('employee')
            ->where('period_id', $periodId)
            ->get();

        $pdf = Pdf::loadView("payroll.remittance-pdf.{$type}", compact('period', 'records'))
            ->setPaper('legal', 'portrait');

        return $pdf->stream("remittance_{$type}_{$period->period_label}.pdf");
    }
}