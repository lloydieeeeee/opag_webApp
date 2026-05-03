<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\LeaveCreditBalance;
use App\Models\LeaveApplication;
use App\Models\HalfDay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $employee = $user->employee;
        $access   = optional(optional($employee)->access)->user_access ?? 'employee';
        $viewAs   = session('view_as', $access);

        // Try to get the latest finalized period — if none exists, use current month
        // without auto-generating payroll (avoids crash when DB is empty)
        $period = PayrollPeriod::where('status', 'FINALIZED')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();

        if (!$period) {
            // Get or create a DRAFT period for the current month; do NOT
            // auto-generate payroll so the dashboard just shows empty state.
            $period = PayrollPeriod::current();
        }

        return $viewAs === 'admin'
            ? $this->adminDashboard($period)
            : $this->employeeDashboard($employee, $period);
    }

    // ─── ADMIN ────────────────────────────────────────────────────
    private function adminDashboard(PayrollPeriod $period)
    {
        // Aggregate totals — safe: returns zeros when no rows exist
        $totals = DB::table('payroll_record')
            ->where('period_id', $period->period_id)
            ->selectRaw('
                COUNT(*)                 AS total_employees,
                SUM(gross_salary)        AS total_gross,
                SUM(net_pay)             AS total_net,
                SUM(gsis_ee)             AS total_gsis,
                SUM(pagibig_ee)          AS total_pagibig,
                SUM(philhealth_ee)       AS total_philhealth,
                SUM(withholding_tax)     AS total_wtax,
                SUM(total_deductions)    AS total_deductions
            ')->first();

        // Per-employee rows for the summary table
        $payrollData = DB::table('payroll_record as pr')
            ->join('employee as e', 'e.employee_id', '=', 'pr.employee_id')
            ->join('position as p', 'p.position_id', '=', 'e.position_id')
            ->where('pr.period_id', $period->period_id)
            ->select(
                'e.employee_id',
                DB::raw("CONCAT(e.last_name, ', ', e.first_name) AS name"),
                'p.position_name AS designation',
                'pr.gross_salary AS gross',
                'pr.gsis_ee      AS gsis',
                'pr.pagibig_ee   AS pagibig',
                'pr.philhealth_ee AS phic',
                'pr.withholding_tax AS wtax',
                'pr.loan_dbp    AS loan_dbp',
                'pr.loan_lbp    AS loan_lbp',
                'pr.loan_cngwmpc AS loan_cngwmpc',
                'pr.loan_paracle AS loan_paracle',
                'pr.allowance_pera AS allowance_pera',
                'pr.allowance_rata AS allowance_rata',
                'pr.allowance_other AS allowance_other',
                'pr.total_deductions',
                'pr.net_pay AS net'
            )
            ->orderBy('e.last_name')
            ->get()
            ->map(fn($r) => (array) $r)
            ->toArray();

        // ── Build a 12-month axis for the CURRENT YEAR (Jan → Dec) ──
        $currentYear = (int) now()->year;

        $monthSlots = [];
        for ($m = 1; $m <= 12; $m++) {
            $dt = Carbon::create($currentYear, $m, 1);
            $monthSlots[] = [
                'year'  => $currentYear,
                'month' => $m,
                'label' => $dt->format('M Y'),
            ];
        }

        // Fetch all FINALIZED payroll data for those 12 months
        $rawTrend = DB::table('payroll_record as pr')
            ->join('payroll_period as pp', 'pp.period_id', '=', 'pr.period_id')
            ->where('pp.status', 'FINALIZED')
            ->where('pp.year', $currentYear)
            ->whereBetween('pp.month', [1, 12])
            ->groupBy('pp.year', 'pp.month')
            ->selectRaw("
                pp.year,
                pp.month,
                SUM(pr.gross_salary)        AS total_gross,
                SUM(pr.gsis_ee)             AS total_gsis,
                SUM(pr.pagibig_ee)          AS total_pagibig,
                SUM(pr.philhealth_ee)       AS total_philhealth,
                SUM(pr.withholding_tax)     AS total_wtax,
                SUM(pr.total_deductions)    AS total_deductions,
                SUM(pr.net_pay)             AS total_net
            ")
            ->get()
            ->keyBy(fn($r) => $r->year . '-' . str_pad($r->month, 2, '0', STR_PAD_LEFT));

        // Merge into 12-slot scaffold (null = no data yet)
        $monthlyTrend = collect($monthSlots)->map(function ($slot) use ($rawTrend) {
            $key  = $slot['year'] . '-' . str_pad($slot['month'], 2, '0', STR_PAD_LEFT);
            $data = $rawTrend->get($key);
            return (object) [
                'period_label'     => $slot['label'],
                'year'             => $slot['year'],
                'month'            => $slot['month'],
                'has_data'         => !is_null($data),
                'total_gross'      => $data ? (float) $data->total_gross      : null,
                'total_gsis'       => $data ? (float) $data->total_gsis       : null,
                'total_pagibig'    => $data ? (float) $data->total_pagibig    : null,
                'total_philhealth' => $data ? (float) $data->total_philhealth : null,
                'total_wtax'       => $data ? (float) $data->total_wtax       : null,
                'total_deductions' => $data ? (float) $data->total_deductions : null,
                'total_net'        => $data ? (float) $data->total_net        : null,
            ];
        })->values()->toArray();

        // Safe counts — default to 0 if tables are empty
        $pendingLeave   = 0;
        $pendingHalfDay = 0;
        $totalEmployees = 0;

        try { $pendingLeave   = LeaveApplication::where('status', 'PENDING')->count(); } catch (\Throwable $e) {}
        try { $pendingHalfDay = HalfDay::where('status', 'PENDING')->count();          } catch (\Throwable $e) {}
        try { $totalEmployees = Employee::where('is_active', 1)->count();              } catch (\Throwable $e) {}

        return view('dashboard', [
            'period'          => $period,
            'payrollData'     => $payrollData,
            'monthlyTrend'    => $monthlyTrend,
            'totalEmployees'  => $totalEmployees,
            'totalGross'      => (float)($totals->total_gross      ?? 0),
            'totalNet'        => (float)($totals->total_net        ?? 0),
            'totalGsis'       => (float)($totals->total_gsis       ?? 0),
            'totalPagibig'    => (float)($totals->total_pagibig    ?? 0),
            'totalPhilhealth' => (float)($totals->total_philhealth ?? 0),
            'totalWtax'       => (float)($totals->total_wtax       ?? 0),
            'totalDeductions' => (float)($totals->total_deductions ?? 0),
            'pendingLeave'    => $pendingLeave,
            'pendingHalfDay'  => $pendingHalfDay,
        ]);
    }

    // ─── EMPLOYEE ─────────────────────────────────────────────────
    private function employeeDashboard(?Employee $employee, PayrollPeriod $period)
    {
        $employeeId = $employee?->employee_id;
        $year       = now()->year;

        // Get this employee's payroll_record row
        $row = null;
        if ($employeeId) {
            $row = PayrollRecord::where('period_id', $period->period_id)
                ->where('employee_id', $employeeId)
                ->first();

            // Auto-compute if not yet generated
            if (!$row && $employee->salary > 0) {
                try {
                    $computed = PayrollRecord::computeFromSalary((float) $employee->salary);
                    $row = new PayrollRecord(array_merge(
                        ['period_id' => $period->period_id, 'employee_id' => $employeeId],
                        $computed
                    ));
                } catch (\Throwable $e) {
                    // computeFromSalary not available — row stays null, view shows zeros
                }
            }
        }

        // Leave balances (VL / SL) — safe even if table doesn't exist yet
        $vlBalance = null;
        $slBalance = null;

        try {
            $vlBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                ->whereHas('leaveType', fn($q) => $q->where('type_code', 'VL'))
                ->where('year', $year)->first();

            $slBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                ->whereHas('leaveType', fn($q) => $q->where('type_code', 'SL'))
                ->where('year', $year)->first();
        } catch (\Throwable $e) {}

        $pendingLeave   = 0;
        $pendingHalfDay = 0;

        try { $pendingLeave   = LeaveApplication::where('employee_id', $employeeId)->where('status', 'PENDING')->count(); } catch (\Throwable $e) {}
        try { $pendingHalfDay = HalfDay::where('employee_id', $employeeId)->where('status', 'PENDING')->count();          } catch (\Throwable $e) {}

        return view('dashboard_user', [
            'period'          => $period,
            'gross'           => (float)($row?->gross_salary    ?? $employee?->salary ?? 0),
            'net'             => (float)($row?->net_pay         ?? 0),
            'gsis'            => (float)($row?->gsis_ee         ?? 0),
            'pagibig'         => (float)($row?->pagibig_ee      ?? 0),
            'phic'            => (float)($row?->philhealth_ee   ?? 0),
            'wtax'            => (float)($row?->withholding_tax ?? 0),
            'totDed'          => (float)($row?->total_deductions?? 0),
            'vlBalance'       => $vlBalance,
            'slBalance'       => $slBalance,
            'pendingLeave'    => $pendingLeave,
            'pendingHalfDay'  => $pendingHalfDay,
        ]);
    }

    // ─── Auto-generate payroll records ────────────────────────────
    private function generatePayrollForPeriod(PayrollPeriod $period): void
    {
        try {
            Employee::where('is_active', 1)->where('salary', '>', 0)
                ->each(function ($emp) use ($period) {
                    $data = PayrollRecord::computeFromSalary((float) $emp->salary);
                    PayrollRecord::updateOrCreate(
                        ['period_id' => $period->period_id, 'employee_id' => $emp->employee_id],
                        $data
                    );
                });
            $period->update(['status' => 'FINALIZED']);
        } catch (\Throwable $e) {
            // Log but don't crash — dashboard will display empty state
            \Log::error('generatePayrollForPeriod failed: ' . $e->getMessage());
        }
    }
}