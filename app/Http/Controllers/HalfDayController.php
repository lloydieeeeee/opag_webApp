<?php

namespace App\Http\Controllers;

use App\Models\HalfDay;
use App\Models\LeaveType;
use App\Models\LeaveCreditBalance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HalfDayController extends Controller
{
    /* ════════════════════════════════════════════════════
       PRIVATE HELPER — resolves the current Provincial
       Agriculturist dynamically (position_id = 1, active).
    ════════════════════════════════════════════════════ */
    private function getOfficeHeadName(): string
    {
        $head = Employee::where('position_id', 1)
                        ->where('is_active', 1)
                        ->first();

        if (!$head) {
            return config('app.office_head', 'ALMIRANTE A. ABAD');
        }

        $firstName  = strtoupper(trim($head->first_name ?? ''));
        $middleInit = $head->middle_name
            ? strtoupper($head->middle_name[0]) . '.'
            : '';
        $lastName   = strtoupper(trim($head->last_name ?? ''));

        return trim("{$firstName} {$middleInit} {$lastName}");
    }

    /* ─────────────────────────────────────────────────
       Employee view
       GET /application/halfday
    ───────────────────────────────────────────────── */
    public function index()
    {
        $employee    = Auth::user()->employee;
        $currentYear = now()->year;

        $leaveTypes = LeaveType::where('is_active', 1)
            ->whereIn(DB::raw('LOWER(TRIM(type_name))'), ['vacation leave', 'sick leave'])
            ->get();

        $creditBalances = LeaveCreditBalance::where('employee_id', $employee->employee_id)
            ->where('year', $currentYear)
            ->get()
            ->keyBy('leave_type_id');

        $halfDays = HalfDay::where('employee_id', $employee->employee_id)
            ->with('leaveType')
            ->latest('application_date')
            ->get();

        $officeHead = $this->getOfficeHeadName();

        return view('application.half-day', compact('employee', 'leaveTypes', 'creditBalances', 'halfDays'))
            ->with('officeHead', $officeHead);
    }

    /* ─────────────────────────────────────────────────
       PDF view — opens halfday-pdf.blade.php in browser
       GET /application/halfday/{id}/pdf
    ───────────────────────────────────────────────── */
    public function pdf(int $id)
    {
        $employee = Auth::user()->employee;

        $halfDay = HalfDay::with('leaveType')
            ->where('half_day_id', $id)
            ->where('employee_id', $employee->employee_id)
            ->firstOrFail();

        $officeHead = $this->getOfficeHeadName();

        return view('application.halfday-pdf', compact('halfDay', 'employee', 'officeHead'));
    }

    /* ─────────────────────────────────────────────────
       Admin cert view
       GET /admin/halfday/{id}/cert
       (kept here as fallback — AdminHalfDayController
        also has its own cert() method for its route)
    ───────────────────────────────────────────────── */
    public function cert(int $id)
    {
        $halfDay = HalfDay::with(['employee.department', 'employee.position', 'leaveType'])
            ->findOrFail($id);

        $employee   = $halfDay->employee;
        $officeHead = $this->getOfficeHeadName();

        return view('application.halfday-pdf', compact('halfDay', 'employee', 'officeHead'));
    }

    /* ─────────────────────────────────────────────────
       Employee files a new half day
       POST /application/halfday
    ───────────────────────────────────────────────── */
    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id'     => 'required|exists:leave_type,leave_type_id',
            'credit_balance_id' => 'required|exists:leave_credit_balance,credit_balance_id',
            'date_of_absence'   => 'required|date',
            'time_period'       => 'required|in:AM,PM',
            'reason'            => 'nullable|string|max:1000',
        ]);

        $employee  = Auth::user()->employee;
        $leaveType = LeaveType::find($request->leave_type_id);

        if (!$leaveType || !in_array(strtolower(trim($leaveType->type_name)), ['vacation leave', 'sick leave'])) {
            return response()->json(['success' => false, 'message' => 'Only Vacation Leave and Sick Leave are eligible.'], 422);
        }

        $balance = LeaveCreditBalance::where('credit_balance_id', $request->credit_balance_id)
            ->where('employee_id', $employee->employee_id)
            ->first();

        if (!$balance) {
            return response()->json(['success' => false, 'message' => 'Invalid leave credit balance.'], 422);
        }
        if ($balance->remaining_balance < 0.5) {
            return response()->json(['success' => false, 'message' => 'Insufficient leave balance.'], 422);
        }

        // Remove old cancelled/rejected duplicates so employee can re-file
        DB::table('half_day')
            ->where('employee_id',     $employee->employee_id)
            ->where('date_of_absence', $request->date_of_absence)
            ->where('time_period',     $request->time_period)
            ->whereIn('status',        ['CANCELLED', 'REJECTED'])
            ->delete();

        $exists = HalfDay::where('employee_id',     $employee->employee_id)
            ->where('date_of_absence', $request->date_of_absence)
            ->where('time_period',     $request->time_period)
            ->whereNotIn('status',     ['CANCELLED', 'REJECTED'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => "You already have a {$request->time_period} half day application for that date.",
            ], 422);
        }

        try {
            $halfDayId = DB::table('half_day')->insertGetId([
                'employee_id'       => $employee->employee_id,
                'leave_type_id'     => $request->leave_type_id,
                'credit_balance_id' => $request->credit_balance_id,
                'application_date'  => now()->toDateString(),
                'date_of_absence'   => $request->date_of_absence,
                'time_period'       => $request->time_period,
                'reason'            => $request->reason,
                'status'            => 'PENDING',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $empName = strtoupper($employee->last_name) . ', ' . $employee->first_name;
            $ltName  = $leaveType->type_name;
            $absDate = \Carbon\Carbon::parse($request->date_of_absence)->format('M d, Y');

            $adminIds = DB::table('user_access')
                ->where('user_access', 'admin')
                ->where('is_active', 1)
                ->pluck('employee_id');

            $now = now();
            foreach ($adminIds as $adminEmpId) {
                DB::table('notifications')->insert([
                    'recipient_id'   => $adminEmpId,
                    'sender_id'      => $employee->employee_id,
                    'type'           => 'halfday_pending',
                    'title'          => 'New Half Day Application',
                    'message'        => "{$empName} filed a {$request->time_period} half day ({$ltName}) for {$absDate}.",
                    'reference_id'   => $halfDayId,
                    'reference_type' => 'half_day',
                    'is_read'        => 0,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
            }

            return response()->json([
                'success'     => true,
                'message'     => 'Half day certification submitted.',
                'half_day_id' => $halfDayId,
            ]);

        } catch (\Exception $e) {
            Log::error('HalfDay store: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save: ' . $e->getMessage()], 500);
        }
    }

    /* ─────────────────────────────────────────────────
       Employee cancels
       POST /application/halfday/{id}/cancel
    ───────────────────────────────────────────────── */
    public function cancel(Request $request, $id)
    {
        $employee = Auth::user()->employee;
        $halfDay  = HalfDay::where('half_day_id', $id)
            ->where('employee_id', $employee->employee_id)
            ->firstOrFail();

        if ($halfDay->status !== 'PENDING') {
            return response()->json(['success' => false, 'message' => 'Only pending applications can be cancelled.'], 422);
        }

        $halfDay->update(['status' => 'CANCELLED']);

        return response()->json(['success' => true, 'message' => 'Half day certification cancelled.']);
    }
}