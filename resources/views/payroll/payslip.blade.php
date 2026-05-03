@extends('layouts.app')
@section('title', 'My Payslip')
@section('page-title', 'Payslip')

@php
    // ── Hard admin guard (blade-level safety net) ──────────────────────────
    // If an admin somehow reaches this view instead of being redirected by
    // the controller, send them straight to Payslip Management here.
    $__user = auth()->user();
    $__isAdmin = false;

    if ($__user) {
        if (method_exists($__user, 'hasRole') && $__user->hasRole('admin'))       $__isAdmin = true;
        if (!$__isAdmin && isset($__user->role) && strtolower($__user->role) === 'admin') $__isAdmin = true;
        if (!$__isAdmin && !empty($__user->is_admin))                              $__isAdmin = true;
        if (!$__isAdmin && method_exists($__user, 'roles') && $__user->roles->contains('name', 'admin')) $__isAdmin = true;
    }

    if ($__isAdmin) {
        $__periodParam = request()->input('period_id')
            ? '?period_id=' . request()->input('period_id')
            : '';
        header('Location: ' . route('payroll.manage') . $__periodParam);
        exit;
    }
@endphp

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');

*, *::before, *::after { box-sizing: border-box; }
body, input, select, button, textarea { font-family: 'Plus Jakarta Sans', sans-serif; }

/* ── Layout ── */
.payslip-page { display: flex; flex-direction: column; min-height: calc(100vh - 120px); }

.breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #9ca3af; margin-bottom: 20px; flex-wrap: wrap; }
.breadcrumb a { color: #9ca3af; text-decoration: none; transition: color .15s; }
.breadcrumb a:hover { color: #1a3a1a; }
.breadcrumb .sep { color: #e5e7eb; }
.breadcrumb .current { color: #1a3a1a; font-weight: 700; }

.main-card { background: #fff; border-radius: 20px; border: 1px solid #e9ecef; box-shadow: 0 2px 20px rgba(0,0,0,.07); overflow: hidden; flex: 1; }

/* ── Top bar ── */
.card-topbar {
    padding: 18px 26px 15px;
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    flex-wrap: wrap; border-bottom: 1px solid #f0f2f0;
    background: linear-gradient(135deg, #fafffe 0%, #f6faf6 100%);
}
.card-topbar-left { display: flex; align-items: center; gap: 12px; }
.card-topbar-icon {
    width: 40px; height: 40px; border-radius: 11px; flex-shrink: 0;
    background: linear-gradient(135deg, #1a3a1a 0%, #2d5a1b 100%);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 3px 8px rgba(26,58,26,.3);
}
.card-topbar-icon svg { width: 20px; height: 20px; color: #fff; }
.card-topbar-title { font-size: 16px; font-weight: 800; color: #111827; margin: 0; letter-spacing: -.3px; }
.card-topbar-sub   { font-size: 11px; color: #9ca3af; margin: 2px 0 0; }

/* ── Topbar buttons ── */
.period-select-wrap { position: relative; min-width: 200px; }
.period-select {
    width: 100%; padding: 10px 38px 10px 14px; font-size: 13px; font-weight: 600;
    border: 1.5px solid #e9ecef; border-radius: 11px; color: #111827; background: #fff;
    appearance: none; -webkit-appearance: none; cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%239ca3af' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 12px center; transition: all .15s;
}
.period-select:focus { outline: none; border-color: #2d5a1b; box-shadow: 0 0 0 3px rgba(45,90,27,.09); }

.btn-download {
    display: inline-flex; align-items: center; gap: 7px; padding: 10px 20px;
    font-size: 13px; font-weight: 700; color: #fff;
    background: linear-gradient(135deg, #1a3a1a, #2d5a1b);
    border: none; border-radius: 11px; cursor: pointer; transition: all .2s;
    box-shadow: 0 3px 10px rgba(26,58,26,.28); text-decoration: none;
}
.btn-download:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(26,58,26,.38); color: #fff; }

.btn-manage {
    display: inline-flex; align-items: center; gap: 7px; padding: 10px 20px;
    font-size: 13px; font-weight: 700; color: #fff;
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    border: none; border-radius: 11px; cursor: pointer; transition: all .2s;
    box-shadow: 0 3px 10px rgba(30,64,175,.28); text-decoration: none;
}
.btn-manage:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(30,64,175,.38); color: #fff; }

.btn-toggle-edit {
    display: inline-flex; align-items: center; gap: 7px; padding: 10px 20px;
    font-size: 13px; font-weight: 700; color: #92400e;
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 1.5px solid #f59e0b; border-radius: 11px; cursor: pointer; transition: all .2s;
    font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 2px 8px rgba(245,158,11,.2);
}
.btn-toggle-edit:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(245,158,11,.35); }
.btn-toggle-edit svg { width: 15px; height: 15px; }

.btn-save-edit {
    display: none; align-items: center; gap: 7px; padding: 10px 20px;
    font-size: 13px; font-weight: 700; color: #fff;
    background: linear-gradient(135deg, #059669, #10b981);
    border: none; border-radius: 11px; cursor: pointer; transition: all .2s;
    font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 2px 8px rgba(5,150,105,.25);
}
.btn-save-edit:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(5,150,105,.4); }
.btn-save-edit svg { width: 15px; height: 15px; }

/* ── Container ── */
.payslip-container { padding: 40px 26px; max-width: 900px; margin: 0 auto; width: 100%; }

.no-record { text-align: center; padding: 80px 20px; color: #9ca3af; }
.no-record-icon { width: 64px; height: 64px; border-radius: 16px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: #9ca3af; }
.no-record-icon svg { width: 32px; height: 32px; }
.no-record h3 { font-size: 18px; font-weight: 700; color: #6b7280; margin: 0 0 8px; }
.no-record p  { font-size: 13px; color: #9ca3af; margin: 0; }

/* ── Payslip Document ── */
.payslip-doc {
    background: #fff; border: 2px solid #e5e7eb; border-radius: 16px;
    padding: 48px; box-shadow: 0 4px 24px rgba(0,0,0,.08);
}

.payslip-header { text-align: center; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 3px solid #1a3a1a; }
.payslip-header h1 { font-size: 13px; font-weight: 600; color: #6b7280; margin: 0 0 4px; text-transform: uppercase; letter-spacing: .5px; }
.payslip-header h2 { font-size: 18px; font-weight: 800; color: #111827; margin: 0 0 4px; text-transform: uppercase; }
.payslip-header h3 { font-size: 15px; font-weight: 700; color: #1a3a1a; margin: 0 0 8px; text-transform: uppercase; }
.payslip-header h4 { font-size: 13px; font-weight: 600; color: #6b7280; margin: 0 0 16px; text-transform: uppercase; }
.payslip-header .pay-period { font-size: 14px; font-weight: 700; color: #111827; margin: 12px 0 0; }

.emp-info-box { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 12px; padding: 20px 24px; margin-bottom: 28px; }
.emp-info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px 32px; }
.emp-info-item { display: flex; align-items: baseline; }
.emp-info-label { font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; min-width: 140px; }
.emp-info-value { font-size: 13px; font-weight: 600; color: #111827; }

.payslip-section { margin-bottom: 24px; }
.section-title { font-size: 12px; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: .5px; padding: 8px 16px; margin: 0 0 12px; border-radius: 8px; }
.section-title.earnings   { background: linear-gradient(135deg, #059669, #10b981); }
.section-title.deductions { background: linear-gradient(135deg, #dc2626, #ef4444); }

.payslip-table { width: 100%; border-collapse: collapse; }
.payslip-table td { padding: 9px 16px; font-size: 12px; border-bottom: 1px solid #f3f4f6; }
.payslip-table tr:last-child td { border-bottom: none; }
.payslip-table .label-cell  { color: #6b7280; font-weight: 500; width: 60%; }
.payslip-table .amount-cell { text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 600; color: #111827; width: 40%; }
.payslip-table .sub-item .label-cell  { padding-left: 32px; font-size: 11px; color: #9ca3af; }
.payslip-table .sub-item .amount-cell { font-size: 11px; color: #6b7280; font-weight: 500; }
.payslip-table .total-row td { padding: 14px 16px; font-weight: 800; font-size: 13px; border-top: 2px solid #e5e7eb; border-bottom: 2px solid #e5e7eb; background: #f9fafb; }
.payslip-table .total-row .amount-cell { font-size: 14px; }
.payslip-table .subtotal-row td { padding: 11px 16px; font-weight: 700; font-size: 12px; background: #f9fafb; }

.net-pay-box {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 2px solid #f59e0b; border-radius: 12px; padding: 20px 24px; margin-top: 24px;
    display: flex; justify-content: space-between; align-items: center;
}
.net-pay-label  { font-size: 16px; font-weight: 800; color: #92400e; text-transform: uppercase; letter-spacing: .5px; }
.net-pay-amount { font-size: 28px; font-weight: 800; color: #92400e; font-family: 'JetBrains Mono', monospace; display: flex; align-items: center; gap: 2px; }

.payslip-footer { margin-top: 48px; padding-top: 24px; border-top: 2px solid #e5e7eb; }
.signature-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 48px; margin-top: 48px; }
.signature-box  { text-align: center; }
.signature-line { border-top: 2px solid #111827; padding-top: 8px; margin-top: 60px; }
.signature-name  { font-size: 13px; font-weight: 700; color: #111827; text-transform: uppercase; }
.signature-title { font-size: 11px; color: #6b7280; margin-top: 2px; }

/* ══════════════════════════════════════════════
   EDIT MODE STYLES
══════════════════════════════════════════════ */
.edit-mode-banner {
    display: none;
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 1.5px solid #f59e0b;
    border-radius: 12px;
    padding: 12px 20px;
    margin-bottom: 20px;
    font-size: 13px;
    font-weight: 600;
    color: #92400e;
    gap: 10px;
    align-items: center;
}
.edit-mode-banner svg { width: 18px; height: 18px; flex-shrink: 0; }

/* Editable text fields in view mode: look static */
.editable-field {
    background: transparent;
    border: none;
    outline: none;
    font: inherit;
    color: inherit;
    width: 100%;
    padding: 0;
    cursor: default;
    resize: none;
}

/* In edit mode: fields become interactive */
.edit-active .editable-field {
    background: #fffbeb;
    border: 1.5px solid #f59e0b;
    border-radius: 6px;
    padding: 4px 8px;
    cursor: text;
    transition: border-color .15s, box-shadow .15s;
}
.edit-active .editable-field:focus {
    border-color: #d97706;
    box-shadow: 0 0 0 3px rgba(217,119,6,.15);
    outline: none;
}

/* Numeric editable amount cells */
.editable-amount {
    background: transparent;
    border: none;
    outline: none;
    font: inherit;
    color: inherit;
    text-align: right;
    width: 100%;
    padding: 0;
    cursor: default;
}
.edit-active .editable-amount {
    background: #fffbeb;
    border: 1.5px solid #f59e0b;
    border-radius: 6px;
    padding: 4px 8px;
    cursor: text;
    text-align: right;
    transition: border-color .15s, box-shadow .15s;
}
.edit-active .editable-amount:focus {
    border-color: #d97706;
    box-shadow: 0 0 0 3px rgba(217,119,6,.15);
    outline: none;
}

/* Net pay amount in edit mode */
.edit-active .net-pay-amount input.editable-amount {
    font-size: 24px;
    font-family: 'JetBrains Mono', monospace;
    font-weight: 800;
    color: #92400e;
    max-width: 220px;
}

/* Highlight changed cells */
.edit-active .editable-field.changed,
.edit-active .editable-amount.changed {
    background: #ecfdf5;
    border-color: #10b981;
}

/* Save/cancel toolbar */
.edit-save-toolbar {
    display: none;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    padding: 14px 20px;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border: 1.5px solid #86efac;
    border-radius: 12px;
    margin-bottom: 20px;
}
.btn-cancel-edit {
    display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px;
    font-size: 13px; font-weight: 700; color: #374151;
    background: #f3f4f6; border: 1.5px solid #d1d5db; border-radius: 10px;
    cursor: pointer; transition: all .2s; font-family: 'Plus Jakarta Sans', sans-serif;
}
.btn-cancel-edit:hover { background: #e5e7eb; }
.btn-confirm-save {
    display: inline-flex; align-items: center; gap: 7px; padding: 9px 20px;
    font-size: 13px; font-weight: 700; color: #fff;
    background: linear-gradient(135deg, #059669, #10b981);
    border: none; border-radius: 10px; cursor: pointer; transition: all .2s;
    font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 2px 8px rgba(5,150,105,.25);
}
.btn-confirm-save:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(5,150,105,.4); }
.btn-confirm-save svg, .btn-cancel-edit svg { width: 15px; height: 15px; }

/* ── Toast notification ── */
#ps-toast {
    position: fixed; bottom: 28px; right: 28px; z-index: 9999;
    background: #111827; color: #fff; font-size: 13px; font-weight: 600;
    padding: 12px 20px; border-radius: 12px; box-shadow: 0 6px 24px rgba(0,0,0,.2);
    opacity: 0; transform: translateY(12px); transition: opacity .25s, transform .25s;
    pointer-events: none; max-width: 320px;
}
#ps-toast.show { opacity: 1; transform: translateY(0); }
#ps-toast.success { background: #059669; }
#ps-toast.error   { background: #dc2626; }

/* ── Print ── */
@media print {
    .payslip-page { min-height: auto; }
    .card-topbar, .breadcrumb, .edit-mode-banner, .edit-save-toolbar { display: none !important; }
    .payslip-doc { border: none; box-shadow: none; padding: 0; }
    .payslip-container { padding: 0; }
    .editable-field, .editable-amount {
        border: none !important; background: transparent !important;
        box-shadow: none !important; padding: 0 !important;
    }
}

/* ── Responsive ── */
@media (max-width: 767px) {
    .payslip-doc { padding: 24px 20px; }
    .emp-info-grid, .signature-grid { grid-template-columns: 1fr; gap: 14px; }
    .emp-info-item { flex-direction: column; }
    .emp-info-label { min-width: 0; margin-bottom: 4px; }
    .net-pay-box { flex-direction: column; gap: 12px; text-align: center; }
    .card-topbar { flex-direction: column; align-items: stretch; }
    .period-select-wrap { min-width: 0; }
}
</style>

@php
    $authUser      = auth()->user();
    $viewerIsAdmin = $authUser && method_exists($authUser, 'hasRole') && $authUser->hasRole('admin');
@endphp

<div class="breadcrumb">
    <a href="{{ route('payroll.index') }}">Payroll</a>
    <span class="sep">›</span>
    <span class="current">My Payslip</span>
</div>

<div class="payslip-page">
    <div class="main-card">

        {{-- ── Top bar ── --}}
        <div class="card-topbar">
            <div class="card-topbar-left">
                <div class="card-topbar-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="card-topbar-title">My Payslip</p>
                    <p class="card-topbar-sub">View your salary details</p>
                </div>
            </div>

            <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">

                {{-- Period selector --}}
                <div class="period-select-wrap">
                    <select class="period-select"
                        onchange="window.location.href='{{ route('payroll.payslip') }}?period_id='+this.value">
                        <option value="">Select Period</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->period_id }}"
                                {{ $selectedPeriodId == $p->period_id ? 'selected' : '' }}>
                                {{ $p->period_label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($record)

                    @if($viewerIsAdmin)
                        {{-- Edit toggle button --}}
                        <button type="button" class="btn-toggle-edit" id="btnToggleEdit" onclick="psToggleEdit()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span id="btnToggleEditLabel">Edit Payslip</span>
                        </button>

                        {{-- Payslip Management --}}
                        <a href="{{ route('payroll.manage') }}?period_id={{ $record->period_id }}" class="btn-manage">
                            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/>
                            </svg>
                            Payslip Management
                        </a>
                    @endif

                    {{-- Download PDF --}}
                    <a href="{{ route('payroll.payslip-all-pdf', $record->period_id) }}?emp_id={{ $record->employee_id }}"
                        class="btn-download" target="_blank">
                        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download PDF
                    </a>

                @endif
            </div>
        </div>{{-- /.card-topbar --}}

        {{-- ── Content ── --}}
        <div class="payslip-container">
            @if(!$record)
                @if($viewerIsAdmin)
                <div class="no-record">
                    <div class="no-record-icon" style="background:#eff6ff;">
                        <svg fill="none" stroke="#3b82f6" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 style="color:#1e40af;">No Personal Payslip Found</h3>
                    <p style="margin-bottom:18px;">Your admin account has no payroll record for this period.<br>Use <strong>Payslip Management</strong> to view or manage employee payslips.</p>
                    <a href="{{ route('payroll.manage') }}{{ $selectedPeriodId ? '?period_id='.$selectedPeriodId : '' }}"
                       style="display:inline-flex;align-items:center;gap:8px;padding:11px 22px;font-size:13px;font-weight:700;color:#fff;background:linear-gradient(135deg,#1e40af,#3b82f6);border-radius:11px;text-decoration:none;box-shadow:0 3px 10px rgba(30,64,175,.28);transition:all .2s;">
                        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/>
                        </svg>
                        Go to Payslip Management
                    </a>
                </div>
                @else
                <div class="no-record">
                    <div class="no-record-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3>No Payslip Available</h3>
                    <p>Please select a payroll period from the dropdown above.</p>
                </div>
                @endif
            @else

            @php
                /* ── helpers ── */
                $empName = strtoupper($record->employee->last_name) . ', '
                         . strtoupper($record->employee->first_name)
                         . ($record->employee->extension_name
                            ? ' ' . strtoupper($record->employee->extension_name) : '');

                $designation = $record->designation
                    ?? optional($record->employee->position)->position_code
                    ?? 'N/A';

                $totalAllowances = ($record->allowance_pera  ?? 0)
                                 + ($record->allowance_rata  ?? 0)
                                 + ($record->allowance_ta    ?? 0)
                                 + ($record->allowance_other ?? 0);

                $sigLast  = strtoupper(optional($record->period->createdBy)->last_name ?? '');
                $sigFirst = strtoupper(optional($record->period->createdBy)->first_name ?? 'MELINDA R. BARCELONA');
                $sigName  = $sigLast ? "{$sigLast}, {$sigFirst}" : $sigFirst;

                $fmt = fn($v) => ($v ?? 0) > 0 ? number_format($v, 2) : '';
                $fmtAlways = fn($v) => number_format($v ?? 0, 2);
            @endphp

            {{-- Edit-mode banner --}}
            <div class="edit-mode-banner" id="editModeBanner">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Edit mode is active. Click any field to edit its value. Changes are saved when you click <strong>Save Changes</strong>.
            </div>

            {{-- Save / Cancel toolbar --}}
            <div class="edit-save-toolbar" id="editSaveToolbar">
                <span style="font-size:13px;font-weight:600;color:#065f46;margin-right:auto;">
                    Unsaved changes — review before saving
                </span>
                <button type="button" class="btn-cancel-edit" onclick="psCancelEdit()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancel
                </button>
                <button type="button" class="btn-confirm-save" onclick="psSaveChanges()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                </button>
            </div>

            {{-- ════════════════════════════════
                 PAYSLIP DOCUMENT
            ════════════════════════════════ --}}
            <div class="payslip-doc" id="payslipDoc">

                {{-- Header --}}
                <div class="payslip-header">
                    <h1><input type="text" class="editable-field" data-field="header_line1" value="Republic of the Philippines"></h1>
                    <h2><input type="text" class="editable-field" data-field="header_line2" value="PROVINCE OF CAMARINES NORTE"></h2>
                    <h3><input type="text" class="editable-field" data-field="header_line3" value="D A E T"></h3>
                    <h4><input type="text" class="editable-field" data-field="header_line4" value="OFFICE OF THE PROVINCIAL AGRICULTURIST"></h4>
                    <div class="pay-period">
                        For the Period: <strong><input type="text" class="editable-field" style="display:inline;width:auto;min-width:160px;"
                            data-field="period_label" value="{{ $record->period->period_label }}"></strong>
                    </div>
                </div>

                {{-- Employee Info --}}
                <div class="emp-info-box">
                    <div class="emp-info-grid">
                        <div class="emp-info-item">
                            <span class="emp-info-label">Employee Name:</span>
                            <span class="emp-info-value">
                                <input type="text" class="editable-field" data-field="emp_name" value="{{ $empName }}">
                            </span>
                        </div>
                        <div class="emp-info-item">
                            <span class="emp-info-label">Employee ID:</span>
                            <span class="emp-info-value">{{ $record->employee_id }}</span>
                        </div>
                        <div class="emp-info-item">
                            <span class="emp-info-label">Position / Designation:</span>
                            <span class="emp-info-value">
                                <input type="text" class="editable-field" data-field="designation" value="{{ $designation }}">
                            </span>
                        </div>
                        <div class="emp-info-item">
                            <span class="emp-info-label">Department:</span>
                            <span class="emp-info-value">
                                {{ optional($record->employee->department)->department_name ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="emp-info-item">
                            <span class="emp-info-label">Gross Salary:</span>
                            <span class="emp-info-value" style="font-family:'JetBrains Mono',monospace;">
                                ₱ <input type="text" class="editable-amount" data-field="gross_salary"
                                    value="{{ $fmtAlways($record->gross_salary) }}">
                            </span>
                        </div>
                        <div class="emp-info-item">
                            <span class="emp-info-label">Pay Period:</span>
                            <span class="emp-info-value">
                                <input type="text" class="editable-field" data-field="period_display"
                                    value="{{ $record->period->period_label }}">
                            </span>
                        </div>
                    </div>
                </div>

                {{-- ── EARNINGS / ALLOWANCES ── --}}
                <div class="payslip-section">
                    <div class="section-title earnings">Earnings &amp; Allowances</div>
                    <table class="payslip-table">
                        <tr>
                            <td class="label-cell" style="font-weight:700;">Basic Salary</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="gross_salary"
                                    value="{{ $fmtAlways($record->gross_salary) }}">
                            </td>
                        </tr>

                        @if($totalAllowances > 0 || true){{-- always show allowances section --}}
                        <tr><td class="label-cell" style="font-weight:700;padding-top:12px;">Allowances</td><td></td></tr>

                        <tr class="sub-item">
                            <td class="label-cell">PERA</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="allowance_pera"
                                    value="{{ $fmtAlways($record->allowance_pera) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">RATA</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="allowance_rata"
                                    value="{{ $fmtAlways($record->allowance_rata) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">Transportation Allowance (TA)</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="allowance_ta"
                                    value="{{ $fmtAlways($record->allowance_ta) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">Other Allowance</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="allowance_other"
                                    value="{{ $fmtAlways($record->allowance_other) }}">
                            </td>
                        </tr>

                        <tr class="subtotal-row">
                            <td class="label-cell">Total Allowances</td>
                            <td class="amount-cell" id="displayTotalAllowances">
                                {{ $fmtAlways($record->total_allowances) }}
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>

                {{-- ── DEDUCTIONS ── --}}
                <div class="payslip-section">
                    <div class="section-title deductions">Deductions</div>
                    <table class="payslip-table">

                        {{-- GSIS --}}
                        <tr><td class="label-cell" style="font-weight:700;">GSIS</td><td></td></tr>
                        <tr class="sub-item">
                            <td class="label-cell">Employee Share (9%)</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="gsis_ee"
                                    value="{{ $fmtAlways($record->gsis_ee) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">Policy Loan</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="gsis_policy"
                                    value="{{ $fmtAlways($record->gsis_policy) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">Emergency Loan</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="gsis_emergency"
                                    value="{{ $fmtAlways($record->gsis_emergency) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">Real Estate Loan</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="gsis_real_estate"
                                    value="{{ $fmtAlways($record->gsis_real_estate) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">MPL</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="gsis_mpl"
                                    value="{{ $fmtAlways($record->gsis_mpl) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">MPL Lite</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="gsis_mpl_lite"
                                    value="{{ $fmtAlways($record->gsis_mpl_lite) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">GFAL</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="gsis_gfal"
                                    value="{{ $fmtAlways($record->gsis_gfal) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">Computer / Education Loan</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="gsis_computer"
                                    value="{{ $fmtAlways($record->gsis_computer) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">Consolidated Loan</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="gsis_conso"
                                    value="{{ $fmtAlways($record->gsis_conso) }}">
                            </td>
                        </tr>

                        {{-- PAG-IBIG --}}
                        <tr><td class="label-cell" style="font-weight:700;padding-top:12px;">PAG-IBIG</td><td></td></tr>
                        <tr class="sub-item">
                            <td class="label-cell">Employee Share (₱200)</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="pagibig_govt"
                                    value="{{ $fmtAlways($record->pagibig_govt) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">MPL</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="pagibig_mpl"
                                    value="{{ $fmtAlways($record->pagibig_mpl) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">Calamity Loan</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="pagibig_calamity"
                                    value="{{ $fmtAlways($record->pagibig_calamity) }}">
                            </td>
                        </tr>

                        {{-- PhilHealth --}}
                        <tr><td class="label-cell" style="font-weight:700;padding-top:12px;">PhilHealth</td><td></td></tr>
                        <tr class="sub-item">
                            <td class="label-cell">Employee Share (2.5%)</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="philhealth_ee"
                                    value="{{ $fmtAlways($record->philhealth_ee) }}">
                            </td>
                        </tr>

                        {{-- Other deductions --}}
                        <tr><td class="label-cell" style="font-weight:700;padding-top:12px;">Other Deductions</td><td></td></tr>
                        <tr class="sub-item">
                            <td class="label-cell">Withholding Tax</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="withholding_tax"
                                    value="{{ $fmtAlways($record->withholding_tax) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">DBP Loan</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="loan_dbp"
                                    value="{{ $fmtAlways($record->loan_dbp) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">LBP Loan</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="loan_lbp"
                                    value="{{ $fmtAlways($record->loan_lbp) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">CNGWMPC</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="loan_cngwmpc"
                                    value="{{ $fmtAlways($record->loan_cngwmpc) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">PARACLE</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="loan_paracle"
                                    value="{{ $fmtAlways($record->loan_paracle) }}">
                            </td>
                        </tr>
                        <tr class="sub-item">
                            <td class="label-cell">Overpayment</td>
                            <td class="amount-cell">
                                <input type="text" class="editable-amount" data-field="overpayment"
                                    value="{{ $fmtAlways($record->overpayment) }}">
                            </td>
                        </tr>

                        {{-- Totals --}}
                        <tr class="total-row">
                            <td class="label-cell">TOTAL DEDUCTIONS</td>
                            <td class="amount-cell" id="displayTotalDeductions">
                                {{ $fmtAlways($record->total_deductions) }}
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Net Pay --}}
                <div class="net-pay-box">
                    <div class="net-pay-label">NET PAY</div>
                    <div class="net-pay-amount">
                        ₱&nbsp;<input type="text" class="editable-amount" data-field="net_pay"
                            id="displayNetPay"
                            value="{{ $fmtAlways($record->net_pay) }}"
                            style="font-size:28px;font-family:'JetBrains Mono',monospace;font-weight:800;color:#92400e;max-width:220px;">
                    </div>
                </div>

                {{-- Footer / Signatories --}}
                <div class="payslip-footer">
                    <p style="font-size:11px;color:#6b7280;text-align:center;">
                        This payslip is computer-generated. No signature required unless otherwise stated.
                    </p>
                    <div class="signature-grid">
                        <div class="signature-box">
                            <div class="signature-line">
                                <div class="signature-name">
                                    <input type="text" class="editable-field" data-field="signatory_name"
                                        value="{{ $sigName }}">
                                </div>
                                <div class="signature-title">
                                    <input type="text" class="editable-field" data-field="signatory_title"
                                        value="AO V / PAYROLL CLERK">
                                </div>
                            </div>
                        </div>
                        <div class="signature-box">
                            <div class="signature-line">
                                <div class="signature-name">
                                    <input type="text" class="editable-field" data-field="approver_name"
                                        value="APPROVED BY">
                                </div>
                                <div class="signature-title">
                                    <input type="text" class="editable-field" data-field="approver_title"
                                        value="Provincial Agriculturist">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /.payslip-doc --}}
            @endif
        </div>{{-- /.payslip-container --}}

    </div>{{-- /.main-card --}}
</div>{{-- /.payslip-page --}}

{{-- Toast --}}
<div id="ps-toast"></div>

@if($record ?? false)
<script>
(function () {
    'use strict';

    /* ── State ── */
    const RECORD_ID  = {{ $record->payroll_id }};
    const SAVE_URL   = '{{ route('payroll.record.update', $record->payroll_id) }}';
    const CSRF_TOKEN = '{{ csrf_token() }}';

    let editActive   = false;
    let originalValues = {};

    /* ── DOM refs ── */
    const doc          = document.getElementById('payslipDoc');
    const banner       = document.getElementById('editModeBanner');
    const toolbar      = document.getElementById('editSaveToolbar');
    const btnLabel     = document.getElementById('btnToggleEditLabel');
    const btnToggle    = document.getElementById('btnToggleEdit');

    /* ── Toggle edit mode ── */
    window.psToggleEdit = function () {
        editActive = !editActive;

        if (editActive) {
            /* Snapshot original values before editing */
            originalValues = {};
            doc.querySelectorAll('.editable-field, .editable-amount').forEach(el => {
                originalValues[el.dataset.field + '_' + Math.random()] = el.value;
                el.removeAttribute('readonly');
                el.style.pointerEvents = 'auto';
            });

            doc.classList.add('edit-active');
            banner.style.display     = 'flex';
            toolbar.style.display    = 'flex';
            btnLabel.textContent     = 'Exit Edit';
            btnToggle.style.background = 'linear-gradient(135deg,#fecaca,#fca5a5)';
            btnToggle.style.borderColor = '#ef4444';
            btnToggle.style.color    = '#7f1d1d';
        } else {
            exitEditMode();
        }
    };

    /* ── Cancel edit ── */
    window.psCancelEdit = function () {
        /* Restore original values */
        doc.querySelectorAll('.editable-field, .editable-amount').forEach(el => {
            /* match by data-field; use first stored value for that field */
            const key = Object.keys(originalValues).find(k => k.startsWith(el.dataset.field + '_'));
            if (key !== undefined) el.value = originalValues[key];
            el.classList.remove('changed');
        });
        exitEditMode();
        showToast('Changes discarded.', 'info');
    };

    function exitEditMode () {
        editActive = false;
        doc.classList.remove('edit-active');
        banner.style.display   = 'none';
        toolbar.style.display  = 'none';
        btnLabel.textContent   = 'Edit Payslip';
        if (btnToggle) {
            btnToggle.style.background   = '';
            btnToggle.style.borderColor  = '';
            btnToggle.style.color        = '';
        }
        doc.querySelectorAll('.editable-field, .editable-amount').forEach(el => {
            el.setAttribute('readonly', true);
            el.style.pointerEvents = 'none';
            el.classList.remove('changed');
        });
    }

    /* ── Mark changed fields ── */
    doc.addEventListener('input', function (e) {
        if (!editActive) return;
        const el = e.target;
        if (el.classList.contains('editable-field') || el.classList.contains('editable-amount')) {
            el.classList.add('changed');
        }
    });

    /* ── Save changes via PATCH ── */
    window.psSaveChanges = async function () {
        const payload = { _method: 'PATCH' };

        /* Collect all numeric field values */
        const numericFields = [
            'gross_salary','gsis_ee','gsis_policy','gsis_emergency','gsis_real_estate',
            'gsis_mpl','gsis_mpl_lite','gsis_gfal','gsis_computer','gsis_conso',
            'pagibig_govt','pagibig_mpl','pagibig_calamity',
            'philhealth_ee','withholding_tax',
            'loan_dbp','loan_lbp','loan_cngwmpc','loan_paracle','overpayment',
            'allowance_pera','allowance_rata','allowance_ta','allowance_other',
            'net_pay'
        ];

        /* We might have duplicated data-field (gross_salary appears twice), take first */
        const seen = new Set();
        doc.querySelectorAll('.editable-amount').forEach(el => {
            const f = el.dataset.field;
            if (!seen.has(f) && numericFields.includes(f)) {
                seen.add(f);
                const raw = el.value.replace(/,/g, '').trim();
                payload[f] = isNaN(parseFloat(raw)) ? 0 : parseFloat(raw);
            }
        });

        /* Text fields saved separately (designation, remarks) */
        const designationEl = doc.querySelector('[data-field="designation"]');
        if (designationEl) payload['designation'] = designationEl.value;

        try {
            const res = await fetch(SAVE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'PATCH',
                },
                body: JSON.stringify(payload),
            });

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                throw new Error(err.message || 'Server error ' + res.status);
            }

            const data = await res.json();

            /* Update computed display fields from server response */
            if (data.record) {
                const r = data.record;
                document.getElementById('displayTotalDeductions').textContent = fmt(r.total_deductions);
                document.getElementById('displayTotalAllowances').textContent = fmt(r.total_allowances);
                document.getElementById('displayNetPay').value                = fmt(r.net_pay);
            }

            exitEditMode();
            showToast('✓ Payslip saved successfully.', 'success');

        } catch (err) {
            showToast('Error: ' + err.message, 'error');
        }
    };

    /* ── Helpers ── */
    function fmt (val) {
        return parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function showToast (msg, type = 'info') {
        const t = document.getElementById('ps-toast');
        t.textContent = msg;
        t.className   = 'show ' + type;
        clearTimeout(t._timer);
        t._timer = setTimeout(() => { t.className = ''; }, 3500);
    }

    /* ── Init: fields start read-only ── */
    doc.querySelectorAll('.editable-field, .editable-amount').forEach(el => {
        el.setAttribute('readonly', true);
        el.style.pointerEvents = 'none';
    });

    @if(!$viewerIsAdmin)
    /* Non-admins never see edit controls — hide defensively */
    const editControls = document.getElementById('btnToggleEdit');
    if (editControls) editControls.style.display = 'none';
    @endif

})();
</script>
@endif

@endsection