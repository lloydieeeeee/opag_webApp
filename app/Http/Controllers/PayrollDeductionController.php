<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollDeduction;

class PayrollDeductionController extends Controller
{
    /* ── INDEX ───────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $deductions = PayrollDeduction::when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($request->expectsJson()) {
            return response()->json($deductions);
        }

        return view('payroll.deductions', compact('deductions', 'search'));
    }

    /* ── STORE ───────────────────────────────────────────────── */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'type'         => 'required|in:Fixed,Not Fixed',
            'rate'         => 'nullable|string|max:50',
            'rate_value'   => 'nullable|numeric|min:0',
            'rate_type'    => 'required|in:percent,flat',
            'limit_amount' => 'nullable|numeric|min:0',
            'status'       => 'nullable|string|max:100',
            'sort_order'   => 'nullable|integer|min:0',
        ]);

        // Auto-fill status from name if blank
        if (empty($data['status'])) {
            $data['status'] = $data['name'];
        }

        // Auto-format rate display label
        if (!empty($data['rate_value'])) {
            if ($data['rate_type'] === 'percent') {
                $data['rate'] = ($data['rate_value'] * 100) . '%';
            } else {
                $data['rate'] = '₱' . number_format($data['rate_value'], 2);
            }
        }

        $data['is_active']   = true;
        $data['sort_order']  = $data['sort_order'] ?? (PayrollDeduction::max('sort_order') + 1);

        $deduction = PayrollDeduction::create($data);

        return response()->json(['success' => true, 'deduction' => $deduction]);
    }

    /* ── UPDATE ──────────────────────────────────────────────── */
    public function update(Request $request, $id)
    {
        $deduction = PayrollDeduction::findOrFail($id);

        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'type'         => 'required|in:Fixed,Not Fixed',
            'rate'         => 'nullable|string|max:50',
            'rate_value'   => 'nullable|numeric|min:0',
            'rate_type'    => 'required|in:percent,flat',
            'limit_amount' => 'nullable|numeric|min:0',
            'status'       => 'nullable|string|max:100',
            'sort_order'   => 'nullable|integer|min:0',
        ]);

        if (empty($data['status'])) {
            $data['status'] = $data['name'];
        }

        if (!empty($data['rate_value'])) {
            if ($data['rate_type'] === 'percent') {
                $data['rate'] = ($data['rate_value'] * 100) . '%';
            } else {
                $data['rate'] = '₱' . number_format($data['rate_value'], 2);
            }
        }

        $deduction->update($data);

        return response()->json(['success' => true, 'deduction' => $deduction->fresh()]);
    }

    /* ── TOGGLE ACTIVE ───────────────────────────────────────── */
    public function toggle($id)
    {
        $deduction = PayrollDeduction::findOrFail($id);
        $deduction->update(['is_active' => !$deduction->is_active]);
        return response()->json(['success' => true, 'is_active' => $deduction->is_active]);
    }

    /* ── DESTROY ─────────────────────────────────────────────── */
    public function destroy($id)
    {
        PayrollDeduction::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /* ── REORDER ─────────────────────────────────────────────── */
    public function reorder(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        foreach ($request->ids as $order => $id) {
            PayrollDeduction::where('id', $id)->update(['sort_order' => $order + 1]);
        }
        return response()->json(['success' => true]);
    }
}