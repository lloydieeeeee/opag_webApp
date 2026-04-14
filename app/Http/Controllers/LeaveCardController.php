<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Department;
use App\Models\LeaveCard;
use App\Models\LeaveCardEntry;
use App\Models\LeaveApplication;
use App\Models\LeaveCreditBalance;
use App\Models\LeaveType;
use Carbon\Carbon;

class LeaveCardController extends Controller
{
    /* ═══════════════════════════════════════════════
     * INDEX — employee list + panel scaffold
     * ═══════════════════════════════════════════════ */
    public function index()
    {
        $employees   = Employee::with(['position', 'department'])
                                ->where('is_active', 1)
                                ->orderBy('last_name')
                                ->orderBy('first_name')
                                ->get();

        $departments = Department::orderBy('department_name')->get();

        $leaveTypes  = LeaveType::orderBy('type_name')->get();

        return view('leave_card.index', compact('employees', 'departments', 'leaveTypes'));
    }

    /* ═══════════════════════════════════════════════
     * SHOW — JSON payload for the editor panel
     *
     * MONTH DIVIDER RULE (enforced here, not on frontend):
     *   The month a record appears under is determined
     *   SOLELY by the date the application was FILED
     *   (application_date column, or created_at fallback).
     *
     *   The leave start_date / end_date are NEVER used
     *   for grouping — they only appear in the particulars
     *   text for display purposes.
     *
     *   Examples:
     *     Filed Mar 31, leave Apr 1–5  → March divider ✓
     *     Filed Mar 31, leave Dec 25   → March divider ✓
     *     Filed Apr 1,  leave Mar 28   → April divider ✓
     * ═══════════════════════════════════════════════ */
    public function show(int $employeeId, int $year)
    {
        $employee = Employee::with(['position', 'department'])
                            ->findOrFail($employeeId);

        /* ── Live DB balance for the year ── */
        $vlBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                        ->where('leave_type_id', 1)
                        ->where('year', $year)
                        ->first();

        $slBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                        ->where('leave_type_id', 2)
                        ->where('year', $year)
                        ->first();

        $currentVl = $vlBalance ? (float) $vlBalance->remaining_balance : null;
        $currentSl = $slBalance ? (float) $slBalance->remaining_balance : null;
        $vlAccrued = $vlBalance ? (float) $vlBalance->total_accrued      : null;
        $vlUsed    = $vlBalance ? (float) $vlBalance->total_used         : null;
        $slAccrued = $slBalance ? (float) $slBalance->total_accrued      : null;
        $slUsed    = $slBalance ? (float) $slBalance->total_used         : null;

        /* ── Old balance — prior year's closing balance ──
         * reference_year = year - 1 (e.g. opening 2026 card → read 2025 record).
         * Falls back to 0 if no record exists.
         * ── */
        $oldBalanceRecord = DB::table('old_balance')
            ->where('employee_id', $employeeId)
            ->where('reference_year', $year - 1)
            ->first();

        $oldBalanceVl = $oldBalanceRecord ? (float) $oldBalanceRecord->old_vl_balance : 0;
        $oldBalanceSl = $oldBalanceRecord ? (float) $oldBalanceRecord->old_sl_balance : 0;

        /* ── Resolve which column holds the filing date ──
         * Use application_date if the column exists,
         * otherwise fall back to created_at.
         * ── */
        $hasAppDateCol = DB::getSchemaBuilder()->hasColumn('leave_applications', 'application_date');
        $filingDateCol = $hasAppDateCol ? 'application_date' : 'created_at';

        /* ── Fetch applications filed OR starting in $year ──
         * Sorted by filing date ASC so frontend receives them
         * in the exact order they should appear on the card.
         * ── */
        $applications = LeaveApplication::with('leaveType')
            ->where('employee_id', $employeeId)
            ->where(function ($q) use ($year, $filingDateCol) {
                $q->whereYear($filingDateCol, $year)
                  ->orWhereYear('start_date', $year);
            })
            ->orderByRaw("DATE({$filingDateCol}) ASC")
            ->orderBy('created_at', 'ASC')
            ->get()
            ->map(function ($app) use ($filingDateCol) {
                $rawFiling   = $app->{$filingDateCol} ?? $app->created_at;
                $filingDate  = Carbon::parse($rawFiling ?? $app->created_at)->toDateString();
                $filingMonth = (int) Carbon::parse($filingDate)->month;

                return [
                    'leave_id'         => $app->leave_id,
                    'leave_type'       => $app->leaveType->type_name        ?? '—',
                    'type_code'        => $app->leaveType->type_code         ?? '—',
                    'is_accrual_based' => $app->leaveType->is_accrual_based  ?? 0,
                    'applied_at'       => $filingDate,
                    'month'            => $filingMonth,
                    'application_date' => $app->application_date,
                    'start_date'       => $app->start_date,
                    'end_date'         => $app->end_date,
                    'no_of_days'       => (float) $app->no_of_days,
                    'details_of_leave' => $app->details_of_leave,
                    'reason'           => $app->reason,
                    'is_monetization'  => (bool) $app->is_monetization,
                    'commutation'      => $app->commutation,
                    'status'           => $app->status,
                    'approved_at'      => $app->approved_at,
                    'reject_reason'    => $app->reject_reason,
                ];
            });

        /* ── Saved leave card header ── */
        $card = LeaveCard::where('employee_id', $employeeId)
                         ->where('year', $year)
                         ->first();

        /* ── Shared old_balance payload ── */
        $oldBalancePayload = [
            'vl'             => $oldBalanceVl,
            'sl'             => $oldBalanceSl,
            'reference_year' => $year - 1,
            'found'          => (bool) $oldBalanceRecord,
        ];

        if (!$card) {
            return response()->json([
                'success'      => false,
                'employee'     => $this->employeePayload($employee),
                'current_vl'   => $currentVl,
                'current_sl'   => $currentSl,
                'vl_accrued'   => $vlAccrued,
                'vl_used'      => $vlUsed,
                'sl_accrued'   => $slAccrued,
                'sl_used'      => $slUsed,
                'old_balance'  => $oldBalancePayload,
                'applications' => $applications,
            ]);
        }

        /* ── Saved entries ── */
        $entries = LeaveCardEntry::where('leave_card_id', $card->leave_card_id)
                        ->orderBy('entry_order')
                        ->get()
                        ->map(function ($e) {
                            $isSep = str_contains($e->date_particulars ?? '', '--- MONTH SEPARATOR ---');
                            $isHr  = str_contains(strtolower($e->date_particulars ?? ''), 'as per hr');
                            return [
                                'entry_id'             => $e->entry_id,
                                'is_separator'         => $isSep,
                                'entry_order'          => $e->entry_order,
                                'month'                => $e->month,
                                'date_particulars'     => $isSep ? null : $e->date_particulars,
                                'earned_vl'            => $e->earned_vl,
                                'earned_sl'            => $e->earned_sl,
                                'taken_vl'             => $e->taken_vl,
                                'taken_sl'             => $e->taken_sl,
                                'leave_wop'            => $e->leave_wop,
                                'tardy_undertime'      => $e->tardy_undertime,
                                'balance_vl'           => $e->balance_vl,
                                'balance_sl'           => $e->balance_sl,
                                // For "As per HR" rows, pass the stored balance back
                                // so the frontend can restore the HR override on reload
                                'hr_vl_balance'        => $isHr ? $e->balance_vl : null,
                                'hr_sl_balance'        => $isHr ? $e->balance_sl : null,
                                'remarks'              => $e->remarks,
                                'status'               => $e->status,
                                'leave_application_id' => $e->leave_application_id,
                                'is_manual'            => $e->is_manual,
                            ];
                        });

        return response()->json([
            'success'      => true,
            'employee'     => $this->employeePayload($employee),
            'card'         => [
                'leave_card_id' => $card->leave_card_id,
                'opening_vl'    => $card->opening_vl,
                'opening_sl'    => $card->opening_sl,
            ],
            'entries'      => $entries,
            'current_vl'   => $currentVl,
            'current_sl'   => $currentSl,
            'vl_accrued'   => $vlAccrued,
            'vl_used'      => $vlUsed,
            'sl_accrued'   => $slAccrued,
            'sl_used'      => $slUsed,
            'old_balance'  => $oldBalancePayload,
            'applications' => $applications,
        ]);
    }

    /* ═══════════════════════════════════════════════
     * SAVE — upsert card + entries
     * ═══════════════════════════════════════════════ */
    public function save(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'year'        => 'required|integer|min:2000|max:2099',
            'opening_vl'  => 'nullable|numeric',
            'opening_sl'  => 'nullable|numeric',
            'entries'     => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $card = LeaveCard::updateOrCreate(
                ['employee_id' => $request->employee_id, 'year' => $request->year],
                [
                    'opening_vl' => $request->opening_vl ?? 0,
                    'opening_sl' => $request->opening_sl ?? 0,
                    'created_by' => Auth::id(),
                ]
            );

            LeaveCardEntry::where('leave_card_id', $card->leave_card_id)->delete();

            foreach (($request->entries ?? []) as $entry) {
                $isSep = (bool) ($entry['is_separator'] ?? false);

                LeaveCardEntry::create([
                    'leave_card_id'        => $card->leave_card_id,
                    'entry_order'          => $entry['entry_order'] ?? 0,
                    'month'                => isset($entry['month']) && $entry['month'] ? (int) $entry['month'] : null,
                    'date_particulars'     => $isSep
                                                ? '--- MONTH SEPARATOR ---'
                                                : ($entry['date_particulars'] ?? null),
                    'earned_vl'            => $isSep ? null : ($entry['earned_vl']       ?? null),
                    'earned_sl'            => $isSep ? null : ($entry['earned_sl']       ?? null),
                    'taken_vl'             => $isSep ? null : ($entry['taken_vl']        ?? null),
                    'taken_sl'             => $isSep ? null : ($entry['taken_sl']        ?? null),
                    'leave_wop'            => $isSep ? null : ($entry['leave_wop']       ?? null),
                    'tardy_undertime'      => $isSep ? null : ($entry['tardy_undertime'] ?? null),
                    'balance_vl'           => $isSep ? null : (isset($entry['balance_vl']) && $entry['balance_vl'] !== '' && $entry['balance_vl'] !== '—' ? $entry['balance_vl'] : null),
                    'balance_sl'           => $isSep ? null : (isset($entry['balance_sl']) && $entry['balance_sl'] !== '' && $entry['balance_sl'] !== '—' ? $entry['balance_sl'] : null),
                    'remarks'              => $isSep ? null : ($entry['remarks'] ?? null),
                    'status'               => $isSep ? null : ($entry['status']  ?? null),
                    'leave_application_id' => $isSep ? null : ($entry['leave_application_id'] ?? null),
                    'is_manual'            => (int) ($entry['is_manual'] ?? 1),
                    'created_by'           => Auth::id(),
                ]);
            }

            /* ══════════════════════════════════════════════════════════
             * RECALCULATE & SYNC LEAVE CREDIT BALANCES
             *
             * Walks entries in entry_order (same as frontend recalcAll).
             * "As per HR" rows hard-override the running VL/SL balance,
             * exactly mirroring the frontend behaviour.
             * ══════════════════════════════════════════════════════════ */
            $openingVl = (float) ($request->opening_vl ?? 0);
            $openingSl = (float) ($request->opening_sl ?? 0);

            $runningVl     = $openingVl;
            $runningSl     = $openingSl;
            $totalEarnedVl = 0;
            $totalEarnedSl = 0;
            $totalTakenVl  = 0;
            $totalTakenSl  = 0;
            $totalWop      = 0;
            $totalTardy    = 0;

            // Sort by entry_order so we walk in the same sequence as the sheet
            $sortedEntries = collect($request->entries ?? [])
                ->sortBy('entry_order')
                ->values();

            foreach ($sortedEntries as $entry) {
                if ($entry['is_separator'] ?? false) continue;

                $earnedVl = (float) ($entry['earned_vl']       ?? 0);
                $earnedSl = (float) ($entry['earned_sl']       ?? 0);
                $takenVl  = (float) ($entry['taken_vl']        ?? 0);
                $takenSl  = (float) ($entry['taken_sl']        ?? 0);
                $wop      = (float) ($entry['leave_wop']       ?? 0);
                $tardy    = (float) ($entry['tardy_undertime'] ?? 0);

                $runningVl += $earnedVl - $takenVl - $tardy - $wop;
                $runningSl += $earnedSl - $takenSl;

                $totalEarnedVl += $earnedVl;
                $totalEarnedSl += $earnedSl;
                $totalTakenVl  += $takenVl;
                $totalTakenSl  += $takenSl;
                $totalWop      += $wop;
                $totalTardy    += $tardy;

                // "As per HR" hard override — mirrors frontend recalcAll()
                $particulars = strtolower($entry['date_particulars'] ?? '');
                if (str_contains($particulars, 'as per hr')) {
                    $hrVl = isset($entry['balance_vl'])
                            && $entry['balance_vl'] !== ''
                            && $entry['balance_vl'] !== '—'
                        ? (float) $entry['balance_vl'] : null;

                    $hrSl = isset($entry['balance_sl'])
                            && $entry['balance_sl'] !== ''
                            && $entry['balance_sl'] !== '—'
                        ? (float) $entry['balance_sl'] : null;

                    if ($hrVl !== null) $runningVl = $hrVl;
                    if ($hrSl !== null) $runningSl = $hrSl;
                }
            }

            $finalVl     = $runningVl;
            $finalSl     = $runningSl;
            $totalUsedVl = $totalTakenVl + $totalTardy + $totalWop;
            $totalUsedSl = $totalTakenSl;

            LeaveCreditBalance::updateOrCreate(
                ['employee_id' => $request->employee_id, 'leave_type_id' => 1, 'year' => $request->year],
                [
                    'total_accrued'     => round($openingVl + $totalEarnedVl, 3),
                    'total_used'        => round($totalUsedVl, 3),
                    'remaining_balance' => round($finalVl, 3),
                ]
            );

            LeaveCreditBalance::updateOrCreate(
                ['employee_id' => $request->employee_id, 'leave_type_id' => 2, 'year' => $request->year],
                [
                    'total_accrued'     => round($openingSl + $totalEarnedSl, 3),
                    'total_used'        => round($totalUsedSl, 3),
                    'remaining_balance' => round($finalSl, 3),
                ]
            );

            DB::commit();
            return response()->json([
                'success'       => true,
                'leave_card_id' => $card->leave_card_id,
                'current_vl'    => round($finalVl, 3),
                'current_sl'    => round($finalSl, 3),
                'vl_accrued'    => round($openingVl + $totalEarnedVl, 3),
                'sl_accrued'    => round($openingSl + $totalEarnedSl, 3),
                'vl_used'       => round($totalUsedVl, 3),
                'sl_used'       => round($totalUsedSl, 3),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /* ═══════════════════════════════════════════════
     * PRINT — single employee leave card print view
     * ═══════════════════════════════════════════════ */
    public function print(int $employeeId, int $year)
    {
        $employee = Employee::with(['position', 'department'])->findOrFail($employeeId);

        $card = LeaveCard::where('employee_id', $employeeId)
                         ->where('year', $year)
                         ->first();

        $entries = $card
            ? LeaveCardEntry::where('leave_card_id', $card->leave_card_id)
                            ->orderBy('entry_order')
                            ->get()
            : collect();

        $applications = LeaveApplication::with('leaveType')
            ->where('employee_id', $employeeId)
            ->whereYear('start_date', $year)
            ->orderBy('start_date')
            ->get();

        $vlBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                        ->where('leave_type_id', 1)->where('year', $year)->first();
        $slBalance = LeaveCreditBalance::where('employee_id', $employeeId)
                        ->where('leave_type_id', 2)->where('year', $year)->first();

        return view('leave_card.print', compact(
            'employee', 'card', 'entries', 'applications', 'year', 'vlBalance', 'slBalance'
        ));
    }

    /* ═══════════════════════════════════════════════
     * PRINT ALL — all active employees
     * ═══════════════════════════════════════════════ */
    public function printAll()
    {
        $year      = now()->year;
        $employees = Employee::with(['position', 'department'])
                             ->where('is_active', 1)
                             ->orderBy('last_name')
                             ->get();

        $allData = $employees->map(function ($employee) use ($year) {
            $card = LeaveCard::where('employee_id', $employee->employee_id)
                             ->where('year', $year)->first();

            $entries = $card
                ? LeaveCardEntry::where('leave_card_id', $card->leave_card_id)
                                ->orderBy('entry_order')->get()
                : collect();

            $applications = LeaveApplication::with('leaveType')
                ->where('employee_id', $employee->employee_id)
                ->whereYear('start_date', $year)
                ->orderBy('start_date')
                ->get();

            $vlBalance = LeaveCreditBalance::where('employee_id', $employee->employee_id)
                            ->where('leave_type_id', 1)->where('year', $year)->first();
            $slBalance = LeaveCreditBalance::where('employee_id', $employee->employee_id)
                            ->where('leave_type_id', 2)->where('year', $year)->first();

            return compact('employee', 'card', 'entries', 'applications', 'vlBalance', 'slBalance');
        });

        return view('leave_card.print-all', compact('allData', 'year'));
    }

    /* ═══════════════════════════════════════════════
     * PRIVATE HELPER
     * ═══════════════════════════════════════════════ */
    private function employeePayload(Employee $emp): array
    {
        return [
            'employee_id'           => $emp->employee_id,
            'last_name'             => $emp->last_name,
            'first_name'            => $emp->first_name,
            'middle_name'           => $emp->middle_name,
            'extension_name'        => $emp->extension_name,
            'position_name'         => $emp->position->position_name    ?? '—',
            'department_name'       => $emp->department->department_name ?? '—',
            'salary'                => $emp->salary,
            'formatted_employee_id' => $emp->formatted_employee_id ?? $emp->employee_id,
        ];
    }
}