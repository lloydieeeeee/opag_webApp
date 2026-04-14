@extends('layouts.app')
@section('title', 'Leave Card')
@section('page-title', 'Leave Card')

@section('content')
<style>
/* ═══════════════════════════════════════
   LEAVE CARD MODULE — Full Styles
═══════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; }

.lc-page { display:flex; flex-direction:column; gap:16px; }

/* ── Employee List Card ── */
.lc-list-card {
    background:#fff; border-radius:16px;
    border:1px solid #f3f4f6;
    box-shadow:0 1px 4px rgba(0,0,0,.05);
    overflow:hidden;
}
.lc-list-header {
    display:flex; align-items:center; justify-content:space-between;
    gap:12px; padding:14px 20px; border-bottom:1px solid #f3f4f6;
    flex-wrap:wrap;
}
.lc-list-title { font-size:14px; font-weight:700; color:#1f2937; margin:0; }
.lc-list-sub   { font-size:11px; color:#9ca3af; margin:2px 0 0; }
.lc-filter-row {
    display:flex; align-items:center; gap:8px; flex-wrap:wrap;
    padding:12px 20px; border-bottom:1px solid #f9fafb;
}
.lc-filter-row .rel { position:relative; }
.lc-filter-row .rel-search { flex:1 1 200px; min-width:140px; }
.lc-filter-row input, .lc-filter-row select {
    width:100%; padding:7px 10px; font-size:12px;
    border:1px solid #e5e7eb; border-radius:8px;
    background:#fff; color:#374151; outline:none;
    appearance:none; -webkit-appearance:none; transition:border-color .15s;
}
.lc-filter-row select { padding-right:26px; }
.lc-filter-row input  { padding-left:30px; }
.lc-filter-row input:focus, .lc-filter-row select:focus { border-color:#2d5a1b; }
.search-icon { position:absolute; left:9px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none; }
.chevron-ico { position:absolute; right:8px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none; }
.lc-table { width:100%; font-size:13px; border-collapse:collapse; }
.lc-table thead tr { background:#fafafa; border-bottom:1px solid #f3f4f6; }
.lc-table th { padding:9px 14px; text-align:left; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; white-space:nowrap; }
.lc-table td { padding:11px 14px; border-bottom:1px solid #f9fafb; color:#374151; }
.lc-table tbody tr:hover { background:#fafafa; cursor:pointer; }
.view-btn {
    display:inline-flex; align-items:center; gap:5px;
    padding:5px 12px; border-radius:8px; font-size:11px; font-weight:700;
    border:1.5px solid #d1fae5; background:#f0fdf4; color:#065f46;
    cursor:pointer; transition:all .15s; text-decoration:none;
}
.view-btn:hover { background:#d1fae5; border-color:#6ee7b7; }

/* ══════════════════════════════════════
   EDITOR PANEL
══════════════════════════════════════ */
#lcEditorOverlay {
    position:fixed; inset:0; z-index:80;
    background:rgba(0,0,0,.3); backdrop-filter:blur(4px);
    opacity:0; pointer-events:none; transition:opacity .3s;
}
#lcEditorOverlay.show { opacity:1; pointer-events:all; }
#lcEditorPanel {
    position:fixed; top:0; right:0; bottom:0; z-index:90;
    width:95vw; max-width:1180px;
    display:flex; flex-direction:column;
    transform:translateX(100%);
    transition:transform .36s cubic-bezier(.32,.72,0,1);
    background:#fff; box-shadow:-12px 0 60px rgba(0,0,0,.18);
}
#lcEditorPanel.open { transform:translateX(0); }
.lce-header {
    background:linear-gradient(135deg,#1a3a1a 0%,#2d5a1b 100%);
    padding:14px 20px;
    display:flex; align-items:center; justify-content:space-between; gap:12px;
    flex-shrink:0;
}
.lce-header-left h2 { font-size:15px; font-weight:700; color:#fff; margin:0 0 2px; }
.lce-header-left p  { font-size:11px; color:rgba(255,255,255,.6); margin:0; }
.lce-header-actions { display:flex; gap:8px; flex-shrink:0; }
.lce-btn {
    padding:7px 14px; border-radius:8px; font-size:11px; font-weight:700;
    cursor:pointer; border:none; transition:all .15s; white-space:nowrap;
    display:inline-flex; align-items:center; gap:5px;
}
.lce-btn-save  { background:#22c55e; color:#fff; }
.lce-btn-save:hover  { background:#16a34a; }
.lce-btn-print { background:#fff; color:#1a3a1a; }
.lce-btn-print:hover { background:#dcfce7; }
.lce-btn-close { background:rgba(255,255,255,.15); color:rgba(255,255,255,.85); border:1.5px solid rgba(255,255,255,.2); }
.lce-btn-close:hover { background:rgba(255,255,255,.28); color:#fff; }

/* ── Year + opening balance row ── */
.lce-year-row {
    padding:8px 20px; border-bottom:1px solid #f3f4f6;
    display:flex; align-items:center; gap:14px; flex-shrink:0;
    background:#fff; flex-wrap:wrap;
}
.lce-year-row label { font-size:12px; font-weight:600; color:#374151; }
.lce-year-select {
    padding:5px 28px 5px 10px; font-size:12px; font-weight:700;
    border:1px solid #d1d5db; border-radius:8px; background:#fff;
    color:#1a3a1a; cursor:pointer; outline:none;
    appearance:none; -webkit-appearance:none;
}
.lce-year-select:focus { border-color:#2d5a1b; }
.opening-vl-input, .opening-sl-input {
    width:90px; padding:5px 8px; font-size:12px; font-weight:700;
    border:1px solid #d1d5db; border-radius:8px; color:#1a3a1a; outline:none;
    text-align:right; font-family:monospace;
}
.opening-vl-input:focus, .opening-sl-input:focus { border-color:#2d5a1b; }
.bal-label { font-size:11px; color:#6b7280; font-weight:600; }

.db-sync-btn {
    padding:5px 11px; border-radius:7px; font-size:10px; font-weight:700;
    border:1.5px solid #bfdbfe; background:#eff6ff; color:#2563eb;
    cursor:pointer; transition:all .15s; display:inline-flex; align-items:center; gap:4px;
}
.db-sync-btn:hover { background:#dbeafe; }

.lce-body {
    flex:1; overflow:auto;
    scrollbar-width:thin; scrollbar-color:#d1d5db transparent;
}
.lce-body::-webkit-scrollbar       { width:5px; height:5px; }
.lce-body::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:99px; }
.lce-grid-wrap { min-width:900px; padding:0 0 20px; }
.lce-form-header {
    text-align:center; padding:14px 20px 10px; border-bottom:2px solid #1a3a1a;
}
.lce-form-header h3 { font-size:13px; font-weight:800; color:#1a3a1a; margin:0 0 2px; letter-spacing:.05em; }
.lce-form-header p  { font-size:12px; font-weight:700; color:#1a3a1a; margin:0; }
.lce-opening-display {
    padding:8px 20px; background:#f0fdf4;
    font-size:12px; color:#166534; font-weight:600;
    border-bottom:1px solid #d1fae5;
}
.lce-sheet {
    width:100%; border-collapse:collapse; font-size:12px;
    font-family:'Courier New', Courier, monospace;
}
.lce-sheet thead tr { background:#f1f5f9; position:sticky; top:0; z-index:3; }
.lce-sheet th {
    padding:7px 6px; text-align:center; font-size:10px; font-weight:700;
    color:#475569; border:1px solid #e2e8f0; white-space:nowrap; background:#f1f5f9;
}
.lce-sheet th.col-particulars { text-align:left; }
.lce-sheet td { padding:0; border:1px solid #e2e8f0; vertical-align:middle; }
.lce-sheet tr.month-sep td {
    background:#1a3a1a; color:#fff; font-size:10px; font-weight:700;
    padding:4px 8px; text-transform:uppercase; letter-spacing:.08em;
}
.lce-sheet .cell-input {
    width:100%; border:none; outline:none; background:transparent;
    padding:5px 6px; font-size:12px; font-family:inherit; color:#1e293b;
    text-align:right; min-width:52px;
}
.lce-sheet .cell-input.particulars-input { text-align:left; min-width:160px; }
.lce-sheet .cell-input.remarks-input     { text-align:left; min-width:100px; }
.lce-sheet .cell-input.status-input      { text-align:left; min-width:80px; }
.lce-sheet .cell-input:focus { background:#fffbeb; box-shadow:inset 0 0 0 2px #fbbf24; }
.lce-sheet .cell-balance {
    background:#f0fdf4 !important; color:#166534 !important;
    font-weight:700; text-align:right; padding:5px 6px;
    font-family:'Courier New', monospace; font-size:12px; min-width:70px;
}
.lce-sheet .cell-balance.negative { background:#fef2f2 !important; color:#991b1b !important; }
.lce-sheet tr.add-row-row td { background:#fafafa; padding:4px 8px; }
.lce-sheet tr.auto-row td { background:#f0fdf4; }
.add-entry-btn {
    display:inline-flex; align-items:center; gap:4px;
    padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700;
    border:1.5px dashed #d1d5db; background:#fff; color:#6b7280; cursor:pointer; transition:all .15s;
}
.add-entry-btn:hover { border-color:#2d5a1b; color:#1a3a1a; background:#f0fdf4; }
.del-row-btn {
    background:none; border:none; cursor:pointer; color:#f87171;
    padding:3px; border-radius:4px; display:inline-flex; align-items:center;
    opacity:.5; transition:opacity .15s;
}
.del-row-btn:hover { opacity:1; }
.auto-badge {
    display:inline-block; font-size:8px; font-weight:800; padding:1px 5px;
    border-radius:4px; background:#d1fae5; color:#065f46; letter-spacing:.04em;
    vertical-align:middle; margin-right:3px; pointer-events:none;
}
@media print { .auto-badge { display:none !important; } }

.lce-import-bar {
    padding:6px 20px; border-bottom:1px solid #f3f4f6;
    background:#fafafa; display:flex; align-items:center; gap:8px; flex-shrink:0;
    font-size:11px; color:#6b7280; flex-wrap:wrap;
}
.lce-import-bar .import-pill {
    display:inline-flex; align-items:center; gap:4px;
    padding:2px 9px; border-radius:5px; font-size:10px; font-weight:700;
    background:#dcfce7; color:#166534; border:1px solid #bbf7d0;
}
.lce-import-bar .import-pill.none {
    background:#f3f4f6; color:#9ca3af; border-color:#e5e7eb;
}
.lce-import-bar .import-pill.old-bal {
    background:#fef9c3; color:#854d0e; border-color:#fde68a;
}

#lcToast {
    position:fixed; bottom:20px; right:20px; z-index:400;
    background:#fff; border-radius:12px; padding:12px 16px;
    box-shadow:0 8px 28px rgba(0,0,0,.15);
    display:flex; align-items:center; gap:10px;
    opacity:0; transform:translateY(12px);
    transition:all .3s; pointer-events:none; min-width:220px;
}
#lcToast.show { opacity:1; transform:translateY(0); pointer-events:all; }

/* ── Add Row Modal ── */
#addRowModal {
    position:fixed; inset:0; z-index:200;
    background:rgba(0,0,0,.5); backdrop-filter:blur(4px);
    display:none; align-items:center; justify-content:center;
}
#addRowModal.show { display:flex; }
.arm-box {
    background:#fff; border-radius:16px; width:95vw; max-width:640px;
    box-shadow:0 20px 60px rgba(0,0,0,.25);
    display:flex; flex-direction:column; max-height:92vh; overflow:hidden;
}
.arm-header {
    background:linear-gradient(135deg,#1a3a1a 0%,#2d5a1b 100%);
    padding:14px 20px; display:flex; align-items:center;
    justify-content:space-between; flex-shrink:0;
}
.arm-header h3 { font-size:14px; font-weight:700; color:#fff; margin:0 0 2px; }
.arm-header p  { font-size:11px; color:rgba(255,255,255,.6); margin:0; }
.arm-close {
    background:rgba(255,255,255,.15); border:1.5px solid rgba(255,255,255,.2);
    border-radius:8px; color:rgba(255,255,255,.85); padding:5px 10px;
    font-size:12px; font-weight:700; cursor:pointer; transition:all .15s;
}
.arm-close:hover { background:rgba(255,255,255,.28); color:#fff; }
.arm-body { padding:20px; overflow-y:auto; flex:1; }
.arm-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.arm-field { display:flex; flex-direction:column; gap:5px; }
.arm-field.full { grid-column:1/-1; }
.arm-label {
    font-size:11px; font-weight:700; color:#6b7280;
    text-transform:uppercase; letter-spacing:.05em;
}
.arm-label.blue  { color:#0369a1; }
.arm-label.green { color:#166534; }
.arm-label.amber { color:#854d0e; }
.arm-label.red   { color:#991b1b; }
.arm-input {
    padding:8px 10px; font-size:13px;
    border:1.5px solid #e5e7eb; border-radius:8px;
    outline:none; background:#fff; color:#111827;
    transition:border-color .15s;
    width:100%;
}
.arm-input:focus { border-color:#2d5a1b; box-shadow:0 0 0 3px rgba(45,90,27,.08); }
.arm-input.num { text-align:right; font-family:'Courier New',monospace; }

/* ── Particulars combo ── */
.arm-particulars-wrap { display:flex; flex-direction:column; gap:6px; }
.arm-particulars-pills { display:flex; flex-wrap:wrap; gap:6px; }
.arm-pill {
    padding:4px 12px; border-radius:999px; font-size:11px; font-weight:700;
    border:1.5px solid #e5e7eb; background:#f9fafb; color:#374151;
    cursor:pointer; transition:all .15s; white-space:nowrap;
}
.arm-pill:hover        { border-color:#2d5a1b; background:#f0fdf4; color:#1a3a1a; }
.arm-pill.active       { border-color:#2d5a1b; background:#dcfce7; color:#15803d; }
.arm-pill.active-late  { border-color:#dc2626; background:#fee2e2; color:#991b1b; }

/* ── Late / Undertime calc box ── */
#armLateCalcBox {
    display:none;
    background:#fff8f8; border:1.5px solid #fca5a5; border-radius:10px;
    padding:12px 14px; margin-top:2px;
    animation:fadeSlideDown .2s ease;
}
@keyframes fadeSlideDown {
    from { opacity:0; transform:translateY(-6px); }
    to   { opacity:1; transform:translateY(0); }
}
.arm-calc-row { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.arm-calc-label { font-size:11px; font-weight:700; color:#991b1b; white-space:nowrap; }
.arm-calc-input {
    width:90px; padding:6px 8px; font-size:13px; font-weight:700;
    border:1.5px solid #fca5a5; border-radius:7px; outline:none;
    text-align:right; font-family:monospace; color:#991b1b; background:#fff;
    transition:border-color .15s;
}
.arm-calc-input:focus { border-color:#dc2626; box-shadow:0 0 0 3px rgba(220,38,38,.08); }
.arm-calc-formula { font-size:11px; color:#6b7280; font-family:monospace; }
.arm-calc-result {
    font-size:13px; font-weight:800; color:#991b1b;
    font-family:monospace; background:#fee2e2; padding:4px 10px;
    border-radius:6px; white-space:nowrap;
}

/* ── As per HR balance box ── */
#armHRBalanceBox {
    display:none;
    background:#f0f9ff; border:1.5px solid #93c5fd; border-radius:10px;
    padding:12px 14px; margin-top:2px;
    animation:fadeSlideDown .2s ease;
}
.arm-hr-balance-container { display:flex; flex-direction:column; gap:10px; }
.arm-balance-row { display:flex; align-items:center; gap:10px; }
.arm-balance-label {
    font-size:11px; font-weight:700; color:#0369a1;
    white-space:nowrap; min-width:100px;
}
/* Blue-themed number inputs inside HR box */
#arm_hr_vl_balance, #arm_hr_sl_balance {
    width:130px; padding:6px 8px; font-size:13px; font-weight:700;
    border:1.5px solid #93c5fd; border-radius:7px; outline:none;
    text-align:right; font-family:monospace; color:#0369a1; background:#fff;
    transition:border-color .15s;
}
#arm_hr_vl_balance:focus, #arm_hr_sl_balance:focus {
    border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.08);
}

.arm-footer {
    padding:14px 20px; border-top:1px solid #f3f4f6; background:#fff;
    display:flex; align-items:center; justify-content:flex-end; gap:10px; flex-shrink:0;
}
.arm-btn-cancel {
    padding:8px 18px; border-radius:8px; font-size:12px; font-weight:600;
    border:1.5px solid #e5e7eb; background:#fff; color:#6b7280; cursor:pointer; transition:all .15s;
}
.arm-btn-cancel:hover { border-color:#9ca3af; color:#374151; }
.arm-btn-save {
    padding:8px 22px; border-radius:8px; font-size:12px; font-weight:700;
    border:none; background:#1a3a1a; color:#fff; cursor:pointer; transition:background .15s;
    display:inline-flex; align-items:center; gap:6px;
}
.arm-btn-save:hover { background:#2d5a1b; }

@media (max-width:767px) {
    #lcEditorPanel { width:100%; max-width:none; top:0; left:0; right:0; bottom:0; transform:translateY(100%); }
    #lcEditorPanel.open { transform:translateY(0); }
    .arm-grid { grid-template-columns:1fr; }
    .arm-field.full { grid-column:1; }
}
</style>

<div class="lc-page">
    <div class="lc-list-card">
        <div class="lc-list-header">
            <div>
                <p class="lc-list-title">Leave Card</p>
                <p class="lc-list-sub">Click an employee to open and edit their leave card. Approved applications are auto-imported grouped by the month they were filed.</p>
            </div>
            <div style="display:flex;gap:8px;">
                <button onclick="exportAllPdf()"
                    style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border-radius:8px;font-size:11px;font-weight:700;background:#1a3a1a;color:#fff;border:none;cursor:pointer;">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print All
                </button>
            </div>
        </div>
        <div class="lc-filter-row">
            <div class="rel rel-search">
                <svg class="search-icon" style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                <input type="text" id="empSearch" placeholder="Search name or ID…" oninput="filterEmpList()">
            </div>
            <div class="rel">
                <select id="deptFilter" onchange="filterEmpList()">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                    @endforeach
                </select>
                <svg class="chevron-ico" style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="lc-table">
                <thead>
                    <tr>
                        <th>Employee ID</th><th>Name</th><th>Position</th><th>Department</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="empTbody">
                    @forelse($employees as $emp)
                    <tr data-emp-id="{{ $emp->employee_id }}"
                        data-dept="{{ $emp->department_id }}"
                        data-search="{{ strtolower($emp->last_name) }} {{ strtolower($emp->first_name) }} {{ $emp->employee_id }}"
                        onclick="openLeaveCard({{ $emp->employee_id }})">
                        <td style="font-family:monospace;font-weight:700;font-size:12px;">{{ $emp->formatted_employee_id ?? $emp->employee_id }}</td>
                        <td style="font-weight:600;">{{ $emp->last_name }}, {{ $emp->first_name }} {{ $emp->middle_name ? strtoupper(substr($emp->middle_name,0,1)).'.' : '' }}</td>
                        <td style="font-size:11px;color:#6b7280;">{{ $emp->position->position_name ?? '—' }}</td>
                        <td style="font-size:11px;color:#6b7280;">{{ $emp->department->department_name ?? '—' }}</td>
                        <td style="text-align:center;" onclick="event.stopPropagation()">
                            <a class="view-btn" onclick="openLeaveCard({{ $emp->employee_id }})">
                                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View / Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;color:#9ca3af;padding:40px;">No active employees found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="lcEditorOverlay" onclick="closeLeaveCard()"></div>

<div id="lcEditorPanel">
    <div class="lce-header">
        <div class="lce-header-left">
            <h2 id="lceTitle">Record of Leave of Absence</h2>
            <p id="lceSubtitle">Loading…</p>
        </div>
        <div class="lce-header-actions">
            <button class="lce-btn lce-btn-print" onclick="printLeaveCard()">
                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
            <button class="lce-btn lce-btn-save" onclick="saveLeaveCard()">
                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save
            </button>
            <button class="lce-btn lce-btn-close" onclick="closeLeaveCard()">✕ Close</button>
        </div>
    </div>

    <div class="lce-year-row">
        <label>Year:</label>
        <div style="position:relative;">
            <select class="lce-year-select" id="lceYear" onchange="loadYearData()">
                @for($y = now()->year; $y >= 2025; $y--)
                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <svg style="width:12px;height:12px;right:8px;top:50%;transform:translateY(-50%);position:absolute;pointer-events:none;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
        <span style="margin:0 4px;color:#9ca3af;font-size:11px;">|</span>
        <span class="bal-label">Opening Balance:</span>
        <span class="bal-label" style="color:#1a3a1a;">VL</span>
        <input type="number" class="opening-vl-input" id="openingVL" step="0.001" placeholder="0.000" oninput="recalcAll()">
        <span class="bal-label" style="color:#1a3a1a;">SL</span>
        <input type="number" class="opening-sl-input" id="openingSL" step="0.001" placeholder="0.000" oninput="recalcAll()">
        <button class="db-sync-btn" onclick="syncFromDb()" title="Reset opening balance from old balance record">
            <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Sync Balance
        </button>
        <span style="flex:1;"></span>
        <span style="font-size:11px;color:#9ca3af;" id="lceAutoSaveLbl"></span>
    </div>

    <div class="lce-import-bar" id="lceImportBar">
        <svg style="width:12px;height:12px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        <span id="lceImportStatus">Approved applications will be auto-imported on load.</span>
        <span id="lceOldBalBadge" style="margin-left:4px;"></span>
    </div>

    <div class="lce-body">
        <div class="lce-grid-wrap">
            <div class="lce-form-header">
                <h3>PROVINCIAL GOVERNMENT OF CAMARINES NORTE</h3>
                <p>RECORD OF LEAVE OF ABSENCE</p>
            </div>
            <div class="lce-opening-display">
                Opening Balance (as carried forward): &nbsp; VL: <strong id="dispVL">0.000</strong> &nbsp;&nbsp; SL: <strong id="dispSL">0.000</strong>
            </div>
            <table class="lce-sheet">
                <thead>
                    <tr>
                        <th style="width:28px;">#</th>
                        <th style="width:68px;">Month</th>
                        <th class="col-particulars" style="min-width:200px;">Date / Particulars</th>
                        <th colspan="2" style="background:#e0f2fe;color:#0369a1;">Leave Earned</th>
                        <th colspan="2" style="background:#dcfce7;color:#166534;">Leave Taken (W/Pay)</th>
                        <th style="background:#fef9c3;color:#854d0e;width:64px;">W/O Pay</th>
                        <th style="background:#fee2e2;color:#991b1b;width:64px;">Tardy / UT</th>
                        <th colspan="2" style="background:#f0fdf4;color:#166534;">Balance</th>
                        <th style="min-width:110px;text-align:left;">Remarks</th>
                        <th style="min-width:72px;text-align:left;">Status</th>
                        <th style="width:28px;"></th>
                    </tr>
                    <tr style="background:#f8fafc;">
                        <th></th><th></th><th></th>
                        <th style="background:#e0f2fe;color:#0369a1;font-size:9px;">VL</th>
                        <th style="background:#e0f2fe;color:#0369a1;font-size:9px;">SL</th>
                        <th style="background:#dcfce7;color:#166534;font-size:9px;">VL</th>
                        <th style="background:#dcfce7;color:#166534;font-size:9px;">SL</th>
                        <th></th><th></th>
                        <th style="background:#f0fdf4;color:#166534;font-size:9px;">VL BAL</th>
                        <th style="background:#f0fdf4;color:#166534;font-size:9px;">SL BAL</th>
                        <th></th><th></th><th></th>
                    </tr>
                </thead>
                <tbody id="lceSheetBody"></tbody>
            </table>
        </div>
    </div>
</div>

{{-- ════ ADD ROW MODAL ════ --}}
<div id="addRowModal">
    <div class="arm-box">
        <div class="arm-header">
            <div>
                <h3>Add Leave Entry</h3>
                <p>Fill in the fields below then click Add Row.</p>
            </div>
            <button class="arm-close" onclick="closeAddRowModal()">✕</button>
        </div>
        <div class="arm-body">
            <div class="arm-grid">

                {{-- Month --}}
                <div class="arm-field full">
                    <label class="arm-label">Month</label>
                    <select id="arm_month" class="arm-input">
                        <option value="">— Select Month —</option>
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>

                {{-- Date / Particulars --}}
                <div class="arm-field full">
                    <label class="arm-label">Date / Particulars</label>
                    <div class="arm-particulars-wrap">
                        <div class="arm-particulars-pills">
                            <button type="button" class="arm-pill" data-val="As per HR" onclick="setParticulars('As per HR', this)">
                                📋 As per HR
                            </button>
                            <button type="button" class="arm-pill" data-val="late" onclick="setParticulars('late', this)">
                                ⏰ Late / Undertime
                            </button>
                        </div>

                        {{-- Particulars free-text input — always editable, decoupled from pill state --}}
                        <input type="text" id="arm_particulars" class="arm-input"
                               placeholder="Type date or description…"
                               oninput="onParticularsInput()">

                        {{-- As per HR balance input box --}}
                        <div id="armHRBalanceBox">
                            <div class="arm-hr-balance-container">
                                <div class="arm-balance-row">
                                    <label class="arm-balance-label">📊 VL Balance:</label>
                                    <input type="number" id="arm_hr_vl_balance"
                                           min="0" step="0.001" placeholder="0.000">
                                </div>
                                <div class="arm-balance-row">
                                    <label class="arm-balance-label">📊 SL Balance:</label>
                                    <input type="number" id="arm_hr_sl_balance"
                                           min="0" step="0.001" placeholder="0.000">
                                </div>
                            </div>
                            <p style="font-size:10px;color:#9ca3af;margin:6px 0 0;">
                                These values will override the computed VL/SL balance for this row and all rows that follow.
                            </p>
                        </div>

                        {{-- Late / Undertime calculator --}}
                        <div id="armLateCalcBox">
                            <div class="arm-calc-row">
                                <span class="arm-calc-label">⏱ Minutes late / undertime:</span>
                                <input type="number" id="arm_late_minutes" class="arm-calc-input"
                                    min="0" step="1" placeholder="0"
                                    oninput="calcLateTardy()">
                                <span class="arm-calc-formula">→</span>
                                <span class="arm-calc-result" id="armLateResult">0.000</span>
                                <span style="font-size:11px;color:#6b7280;">days</span>
                            </div>
                            <p style="font-size:10px;color:#9ca3af;margin:6px 0 0;">
                                Result is auto-filled into <strong>Tardy / Undertime</strong> field below.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Earned VL --}}
                <div class="arm-field">
                    <label class="arm-label blue">Earned VL</label>
                    <input type="number" id="arm_earned_vl" class="arm-input num" step="0.001" placeholder="0.000">
                </div>

                {{-- Earned SL --}}
                <div class="arm-field">
                    <label class="arm-label blue">Earned SL</label>
                    <input type="number" id="arm_earned_sl" class="arm-input num" step="0.001" placeholder="0.000">
                </div>

                {{-- Taken VL --}}
                <div class="arm-field">
                    <label class="arm-label green">Taken VL (W/Pay)</label>
                    <input type="number" id="arm_taken_vl" class="arm-input num" step="0.001" placeholder="0.000">
                </div>

                {{-- Taken SL --}}
                <div class="arm-field">
                    <label class="arm-label green">Taken SL (W/Pay)</label>
                    <input type="number" id="arm_taken_sl" class="arm-input num" step="0.001" placeholder="0.000">
                </div>

                {{-- W/O Pay --}}
                <div class="arm-field">
                    <label class="arm-label amber">W/O Pay</label>
                    <input type="number" id="arm_wop" class="arm-input num" step="0.001" placeholder="0.000">
                </div>

                {{-- Tardy / Undertime --}}
                <div class="arm-field">
                    <label class="arm-label red">Tardy / Undertime</label>
                    <input type="number" id="arm_tardy" class="arm-input num" step="0.001" placeholder="0.000"
                           oninput="onTardyManualInput()">
                </div>

                {{-- Remarks --}}
                <div class="arm-field">
                    <label class="arm-label">Remarks</label>
                    <input type="text" id="arm_remarks" class="arm-input" placeholder="Optional remarks">
                </div>

                {{-- Status --}}
                <div class="arm-field">
                    <label class="arm-label">Status</label>
                    <input type="text" id="arm_status" class="arm-input" placeholder="e.g. APPROVED">
                </div>

            </div>
        </div>
        <div class="arm-footer">
            <button class="arm-btn-cancel" onclick="closeAddRowModal()">Cancel</button>
            <button class="arm-btn-save" onclick="confirmAddRow()">
                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Row
            </button>
        </div>
    </div>
</div>

<div id="lcToast">
    <div id="lcToastIcon" style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:justify-content:center;flex-shrink:0;"></div>
    <div>
        <p style="font-size:12px;font-weight:700;color:#1f2937;margin:0;" id="lcToastTitle"></p>
        <p style="font-size:11px;color:#6b7280;margin:2px 0 0;" id="lcToastMsg"></p>
    </div>
</div>

<script>
const CSRF        = "{{ csrf_token() }}";
const LC_BASE_URL = "{{ url('admin/leave-card') }}";

let currentEmployeeId = null;
let currentYear       = {{ now()->year }};
let currentCardId     = null;
let rowCounter        = 0;
let dbCurrentVl       = null;
let dbCurrentSl       = null;
let allApplications   = [];
let importedLeaveIds  = new Set();
let excludedLeaveIds  = new Set();

// ── Cached old_balance values ──
let _oldBalVl    = 0;
let _oldBalSl    = 0;
let _oldBalYear  = null;
let _oldBalFound = false;

// ── Tracks whether the late calc is driving the tardy field ──
let _lateCalcActive = false;

/* ════ SESSION "NEW" BADGE HELPERS ════ */
function _newBadgeKey(empId, yr) { return `lc_new_${empId}_${yr}`; }
function _getNewIds(empId, yr) {
    try { const raw = sessionStorage.getItem(_newBadgeKey(empId, yr)); return raw ? new Set(JSON.parse(raw)) : new Set(); }
    catch { return new Set(); }
}
function _addNewIds(empId, yr, ids) {
    const existing = _getNewIds(empId, yr);
    ids.forEach(id => existing.add(id));
    try { sessionStorage.setItem(_newBadgeKey(empId, yr), JSON.stringify([...existing])); } catch {}
}
function _clearNewIds(empId, yr) {
    try { sessionStorage.removeItem(_newBadgeKey(empId, yr)); } catch {}
}

const MONTHS = ['January','February','March','April','May','June',
                'July','August','September','October','November','December'];

/* ════ DATE HELPERS ════ */
function fmtDate(str) {
    if (!str) return '';
    const parts = String(str).substring(0, 10).split('-');
    if (parts.length < 3) return str;
    const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

/* ════ FILTER ════ */
function filterEmpList() {
    const search = document.getElementById('empSearch').value.toLowerCase();
    const dept   = document.getElementById('deptFilter').value;
    document.querySelectorAll('#empTbody tr').forEach(row => {
        const ok = (!search || (row.dataset.search||'').includes(search))
                && (!dept   || row.dataset.dept === dept);
        row.style.display = ok ? '' : 'none';
    });
}

/* ════ OPEN PANEL ════ */
async function openLeaveCard(empId) {
    currentEmployeeId = empId;
    currentYear = parseInt(document.getElementById('lceYear')?.value || {{ now()->year }});
    document.getElementById('lcEditorOverlay').classList.add('show');
    document.getElementById('lcEditorPanel').classList.add('open');
    document.body.style.overflow = 'hidden';
    await loadYearData();
}

/* ════ LOAD YEAR DATA ════ */
async function loadYearData() {
    if (!currentEmployeeId) return;
    currentYear = parseInt(document.getElementById('lceYear').value);
    importedLeaveIds.clear();
    excludedLeaveIds.clear();
    setImportStatus('loading');
    dbCurrentVl = null; dbCurrentSl = null;
    _oldBalVl = 0; _oldBalSl = 0; _oldBalYear = null; _oldBalFound = false;
    document.getElementById('lceOldBalBadge').innerHTML = '';

    try {
        const res  = await fetch(`${LC_BASE_URL}/${currentEmployeeId}/${currentYear}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();

        dbCurrentVl     = data.current_vl;
        dbCurrentSl     = data.current_sl;
        allApplications = data.applications || [];

        if (data.old_balance) {
            _oldBalVl    = parseFloat(data.old_balance.vl   ?? 0);
            _oldBalSl    = parseFloat(data.old_balance.sl   ?? 0);
            _oldBalYear  = data.old_balance.reference_year  ?? (currentYear - 1);
            _oldBalFound = data.old_balance.found            ?? false;
        }

        if (data.success) {
            populateEditor(data);
            (data.entries || []).forEach(e => {
                if (e.leave_application_id && e.date_particulars !== '--- EXCLUDED ---')
                    importedLeaveIds.add(parseInt(e.leave_application_id));
            });
        } else {
            populateEditorBlank(data.employee);
        }

        _renderOldBalBadge();
        autoImportApproved();

    } catch (e) {
        setImportStatus('error');
        showLcToast('Error', 'Could not load leave card data.', 'error');
    }
}

/* ════ OLD BALANCE BADGE ════ */
function _renderOldBalBadge() {
    const el = document.getElementById('lceOldBalBadge');
    if (!_oldBalFound) {
        el.innerHTML = '<span class="import-pill none">No prior balance record found</span>';
        return;
    }
    el.innerHTML = `<span class="import-pill old-bal" title="Sourced from old_balance table (reference_year ${_oldBalYear})">📂 Prior balance (${_oldBalYear}): VL ${_oldBalVl.toFixed(3)} · SL ${_oldBalSl.toFixed(3)}</span>`;
}

/* ════ AUTO-IMPORT APPROVED ON LOAD ════ */
function autoImportApproved() {
    const approved = allApplications.filter(a =>
        a.status === 'APPROVED'
        && !importedLeaveIds.has(parseInt(a.leave_id))
        && !excludedLeaveIds.has(parseInt(a.leave_id))
    );

    if (!approved.length) { setImportStatus('none'); return; }

    const tbody  = document.getElementById('lceSheetBody');
    const addRow = tbody.querySelector('tr.add-row-row');
    if (addRow) addRow.remove();

    let lastMonth = _getLastMonthInSheet(tbody);
    const newlyImportedIds = [];

    approved.forEach(app => {
        const filingMonth = parseInt(app.month) || 0;
        if (filingMonth && filingMonth !== lastMonth) {
            appendMonthSep(tbody, filingMonth);
            lastMonth = filingMonth;
        }
        const entry = {
            leave_application_id : parseInt(app.leave_id),
            month                : filingMonth,
            date_particulars     : buildParticulars(app),
            earned_vl            : null, earned_sl : null,
            taken_vl             : (app.is_accrual_based && app.type_code === 'VL') ? app.no_of_days : null,
            taken_sl             : (app.is_accrual_based && app.type_code === 'SL') ? app.no_of_days : null,
            leave_wop            : (!app.is_accrual_based && !app.is_monetization)  ? app.no_of_days : null,
            tardy_undertime      : null,
            remarks              : app.leave_type + (app.is_monetization ? ' (Monetization)' : ''),
            status               : app.status,
            is_manual            : 0,
            hr_vl_balance        : null,
            hr_sl_balance        : null,
        };
        appendDataRow(tbody, entry, true);
        importedLeaveIds.add(parseInt(app.leave_id));
        newlyImportedIds.push(parseInt(app.leave_id));
    });

    if (newlyImportedIds.length) _addNewIds(currentEmployeeId, currentYear, newlyImportedIds);
    appendAddRowPrompt(tbody);
    recalcAll();
    setImportStatus('done', approved.length);
    showLcToast('Auto-imported', `${approved.length} approved application(s) added to sheet.`, 'success');
}

function _getLastMonthInSheet(tbody) {
    const rows = [...tbody.querySelectorAll('tr[data-month]')];
    for (let i = rows.length - 1; i >= 0; i--) { const m = parseInt(rows[i].dataset.month); if (m) return m; }
    return 0;
}

/* ════ IMPORT STATUS BAR ════ */
function setImportStatus(state, count) {
    const el = document.getElementById('lceImportStatus');
    if      (state === 'loading') el.innerHTML = '<span style="color:#9ca3af;">Loading applications…</span>';
    else if (state === 'done')    el.innerHTML = `<span class="import-pill">✓ ${count} approved application${count !== 1 ? 's' : ''} auto-imported</span>`;
    else if (state === 'none')    el.innerHTML = '<span class="import-pill none">No new approved applications to import</span>';
    else if (state === 'error')   el.innerHTML = '<span style="color:#dc2626;">Failed to load applications</span>';
}

/* ════ DATE FORMATTING FOR PARTICULARS ════ */
function buildParticulars(app) {
    const startClean = fmtDate(app.start_date);
    const endClean   = fmtDate(app.end_date);
    const dateRange  = app.start_date.substring(0,10) === app.end_date.substring(0,10)
                       ? startClean : `${startClean} to ${endClean}`;
    return `${app.type_code} — ${dateRange}${app.details_of_leave ? ' (' + app.details_of_leave + ')' : ''}`;
}

/* ════ DB BALANCE / SYNC ════ */
function fmt(v) { return (v === null || v === undefined) ? '—' : parseFloat(v).toFixed(3); }

function syncFromDb() {
    document.getElementById('openingVL').value = _oldBalVl.toFixed(3);
    document.getElementById('openingSL').value = _oldBalSl.toFixed(3);
    recalcAll();
    showLcToast('Synced', `Opening balance reset from ${_oldBalFound ? `old balance record (${_oldBalYear})` : 'default (no prior balance record found)'}.`, 'info');
}

/* ════ POPULATE ════ */
function populateEditorBlank(emp) {
    if (!emp) return;
    document.getElementById('lceTitle').textContent    = `Record of Leave of Absence — ${currentYear}`;
    document.getElementById('lceSubtitle').textContent = `${emp.last_name}, ${emp.first_name}`;
    document.getElementById('openingVL').value = _oldBalVl.toFixed(3);
    document.getElementById('openingSL').value = _oldBalSl.toFixed(3);
    currentCardId = null;
    renderRows([]);
    updateOpeningDisplay();
}

function populateEditor(data) {
    const emp = data.employee;
    document.getElementById('lceTitle').textContent    = `Record of Leave of Absence — ${currentYear}`;
    document.getElementById('lceSubtitle').textContent = `${emp.last_name}, ${emp.first_name}`;
    document.getElementById('openingVL').value = parseFloat(data.card.opening_vl || 0).toFixed(3);
    document.getElementById('openingSL').value = parseFloat(data.card.opening_sl || 0).toFixed(3);
    currentCardId = data.card.leave_card_id;
    renderRows(data.entries || []);
    updateOpeningDisplay();
}

/* ════ RENDER ROWS ════ */
function renderRows(entries) {
    const tbody = document.getElementById('lceSheetBody');
    tbody.innerHTML = '';
    rowCounter = 0;

    if (!entries.length) { appendAddRowPrompt(tbody); recalcAll(); return; }

    let lastMonth = 0;
    entries.forEach(entry => {
        if (entry.date_particulars === '--- EXCLUDED ---' && entry.leave_application_id) {
            excludedLeaveIds.add(parseInt(entry.leave_application_id));
            importedLeaveIds.add(parseInt(entry.leave_application_id));
            return;
        }
        if (entry.is_separator) {
            appendMonthSep(tbody, entry.month);
            lastMonth = entry.month || 0;
        } else {
            if (entry.month && entry.month !== lastMonth) {
                appendMonthSep(tbody, entry.month);
                lastMonth = entry.month;
            }
            appendDataRow(tbody, entry);
        }
    });

    appendAddRowPrompt(tbody);
    recalcAll();
}

function appendMonthSep(tbody, monthNum) {
    const tr = document.createElement('tr');
    tr.className = 'month-sep'; tr.dataset.type = 'sep'; tr.dataset.month = monthNum;
    tr.innerHTML = `<td colspan="14" style="position:relative;">── ${MONTHS[(monthNum||1)-1] || 'Month '+monthNum} ${currentYear} ──</td>`;
    tbody.appendChild(tr);
}

/* ════ APPEND DATA ROW ════ */
function appendDataRow(tbody, entry = {}, isNew = false) {
    rowCounter++;
    const id     = entry.entry_id || `new_${rowCounter}`;
    const isAuto = !!(entry.leave_application_id || entry.is_manual === 0);
    const lid        = entry.leave_application_id ? parseInt(entry.leave_application_id) : 0;
    const sessionNew = lid ? _getNewIds(currentEmployeeId, currentYear).has(lid) : false;
    const showBadge  = isNew || sessionNew;
    const autoBadge  = showBadge ? '<span class="auto-badge">NEW</span>' : '';

    // ── Resolve HR balance override ──
    const isHrRow = String(entry.date_particulars || '').toLowerCase().includes('as per hr');
    let hrVlBalance = '';
    let hrSlBalance = '';
    if (isHrRow) {
        const rawVl = (entry.hr_vl_balance != null && entry.hr_vl_balance !== '')
                        ? entry.hr_vl_balance
                        : (entry.balance_vl != null && entry.balance_vl !== '' ? entry.balance_vl : '');
        const rawSl = (entry.hr_sl_balance != null && entry.hr_sl_balance !== '')
                        ? entry.hr_sl_balance
                        : (entry.balance_sl != null && entry.balance_sl !== '' ? entry.balance_sl : '');
        hrVlBalance = rawVl !== '' ? String(rawVl) : '';
        hrSlBalance = rawSl !== '' ? String(rawSl) : '';
    }

    const tr = document.createElement('tr');
    tr.className = 'data-row' + (isAuto ? ' auto-row' : '');
    tr.dataset.type               = 'data';
    tr.dataset.entryId            = id;
    tr.dataset.month              = entry.month || '';
    tr.dataset.leaveApplicationId = entry.leave_application_id || '';
    tr.dataset.isManual           = (entry.is_manual !== undefined ? entry.is_manual : 1);
    tr.dataset.hrVlBalance        = hrVlBalance;
    tr.dataset.hrSlBalance        = hrSlBalance;

    const v = val => (val != null && val !== '') ? parseFloat(val).toFixed(3) : '';
    tr.innerHTML = `
        <td style="text-align:center;font-size:10px;color:#9ca3af;padding:4px;">${rowCounter}</td>
        <td style="padding:5px 6px;font-size:11px;color:#374151;white-space:nowrap;text-align:left;">${entry.month ? MONTHS[entry.month - 1] : ''}</td>
        <td style="min-width:200px;"><input type="text" class="cell-input particulars-input" data-col="date_particulars" value="${escHtml(entry.date_particulars || '')}" placeholder="Date / Particulars…" oninput="onParticularsEdit(this)"></td>
        <td><input type="number" class="cell-input" data-col="earned_vl"       step="0.001" value="${v(entry.earned_vl)}"       oninput="recalcAll()"></td>
        <td><input type="number" class="cell-input" data-col="earned_sl"       step="0.001" value="${v(entry.earned_sl)}"       oninput="recalcAll()"></td>
        <td><input type="number" class="cell-input" data-col="taken_vl"        step="0.001" value="${v(entry.taken_vl)}"        oninput="recalcAll()"></td>
        <td><input type="number" class="cell-input" data-col="taken_sl"        step="0.001" value="${v(entry.taken_sl)}"        oninput="recalcAll()"></td>
        <td><input type="number" class="cell-input" data-col="leave_wop"       step="0.001" value="${v(entry.leave_wop)}"       oninput="recalcAll()"></td>
        <td><input type="number" class="cell-input" data-col="tardy_undertime" step="0.001" value="${v(entry.tardy_undertime)}" oninput="recalcAll()"></td>
        <td class="cell-balance" data-col="balance_vl">${v(entry.balance_vl) || '—'}</td>
        <td class="cell-balance" data-col="balance_sl">${v(entry.balance_sl) || '—'}</td>
        <td><input type="text" class="cell-input remarks-input" data-col="remarks" value="${escHtml(entry.remarks || '')}"></td>
        <td style="position:relative;">
            ${autoBadge}
            <input type="text" class="cell-input status-input" data-col="status" value="${escHtml(entry.status || '')}" style="width:${showBadge ? 'calc(100% - 34px)' : '100%'}">
        </td>
        <td>
            <button class="del-row-btn" onclick="deleteRow(this.closest('tr'))">
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </td>`;
    tbody.appendChild(tr);
}

/* If user edits the particulars cell inline and removes "as per hr",
   clear the HR override so recalc goes back to computed values. */
function onParticularsEdit(input) {
    const tr = input.closest('tr');
    if (!tr) return;
    const val = input.value.toLowerCase();
    if (!val.includes('as per hr')) {
        tr.dataset.hrVlBalance = '';
        tr.dataset.hrSlBalance = '';
    }
    recalcAll();
}

function appendAddRowPrompt(tbody) {
    const tr = document.createElement('tr');
    tr.className = 'add-row-row'; tr.dataset.type = 'add';
    tr.innerHTML = `<td colspan="14"><button class="add-entry-btn" onclick="openAddRowModal()"><svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> + Add row</button></td>`;
    tbody.appendChild(tr);
}

function openAddRowModal() {
    ['arm_month','arm_earned_vl','arm_earned_sl','arm_taken_vl','arm_taken_sl',
     'arm_wop','arm_tardy','arm_status','arm_late_minutes',
     'arm_hr_vl_balance','arm_hr_sl_balance'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.getElementById('arm_particulars').value = '';
    document.getElementById('arm_remarks').value     = '';
    document.getElementById('armLateResult').textContent = '0.000';

    document.querySelectorAll('.arm-pill').forEach(p => p.classList.remove('active','active-late'));
    document.getElementById('armLateCalcBox').style.display = 'none';
    document.getElementById('armHRBalanceBox').style.display = 'none';
    _lateCalcActive = false;

    document.getElementById('addRowModal').classList.add('show');
    setTimeout(() => document.getElementById('arm_month').focus(), 60);
}

function closeAddRowModal() {
    document.getElementById('addRowModal').classList.remove('show');
}

/* ════ PARTICULARS PILL HANDLER ════
 *
 * FIX: The "late" pill no longer writes into or controls the particulars
 * text input. It only toggles the minutes calculator box. The user can
 * freely type any date/description in the particulars field while the
 * calculator is open — the two are fully independent.
 * ═══════════════════════════════════════════════════════════════════ */
function setParticulars(val, btn) {
    const allPills = document.querySelectorAll('.arm-pill');
    const lateBox  = document.getElementById('armLateCalcBox');
    const hrBox    = document.getElementById('armHRBalanceBox');
    const input    = document.getElementById('arm_particulars');

    // Toggle off if already active
    if (btn.classList.contains('active') || btn.classList.contains('active-late')) {
        allPills.forEach(p => p.classList.remove('active','active-late'));
        lateBox.style.display = 'none';
        hrBox.style.display   = 'none';
        _lateCalcActive = false;
        // Do NOT clear the particulars input — user may have typed something
        return;
    }

    allPills.forEach(p => p.classList.remove('active','active-late'));

    if (val === 'late') {
        btn.classList.add('active-late');
        lateBox.style.display = 'block';
        hrBox.style.display   = 'none';
        _lateCalcActive = true;
        // Particulars input intentionally left untouched — user types freely
        document.getElementById('arm_late_minutes').value = '';
        document.getElementById('armLateResult').textContent = '0.000';
        document.getElementById('arm_tardy').value = '';
        setTimeout(() => document.getElementById('arm_late_minutes').focus(), 60);

    } else if (val === 'As per HR') {
        btn.classList.add('active');
        hrBox.style.display   = 'block';
        lateBox.style.display = 'none';
        _lateCalcActive = false;
        input.value = 'As per HR';
        document.getElementById('arm_hr_vl_balance').value = '';
        document.getElementById('arm_hr_sl_balance').value = '';
        setTimeout(() => document.getElementById('arm_hr_vl_balance').focus(), 60);

    } else {
        btn.classList.add('active');
        hrBox.style.display   = 'none';
        lateBox.style.display = 'none';
        _lateCalcActive = false;
        input.value = val;
    }
}

/* ════ PARTICULARS INPUT HANDLER ════
 *
 * FIX: The late pill is fully decoupled from the particulars field.
 * Typing in particulars never dismisses the late calculator.
 * Only the "As per HR" pill is deactivated when the user types
 * something different (since its value IS tied to the text field).
 * ═══════════════════════════════════════════════════════════════════ */
function onParticularsInput() {
    const val        = document.getElementById('arm_particulars').value;
    const activePill = document.querySelector('.arm-pill.active, .arm-pill.active-late');
    if (!activePill) return;

    // Late pill is decoupled from particulars — never auto-deactivate it on text change
    if (activePill.classList.contains('active-late')) return;

    // For "As per HR" and any other pill, deactivate if the text drifts away from the pill value
    if (val !== activePill.dataset.val) {
        activePill.classList.remove('active');
        document.getElementById('armHRBalanceBox').style.display = 'none';
    }
}

/* ════ LATE / UNDERTIME MINUTES CALCULATOR ════ */
const CONVERSION_TABLE = {
    1:0.002,2:0.004,3:0.006,4:0.008,5:0.010,6:0.012,7:0.015,8:0.017,
    9:0.019,10:0.021,11:0.023,12:0.025,13:0.027,14:0.029,15:0.031,
    16:0.033,17:0.035,18:0.037,19:0.040,20:0.042,21:0.044,22:0.046,
    23:0.048,24:0.050,25:0.052,26:0.054,27:0.056,28:0.058,29:0.060,
    30:0.062,31:0.065,32:0.067,33:0.069,34:0.071,35:0.073,36:0.075,
    37:0.077,38:0.079,39:0.081,40:0.083,41:0.085,42:0.087,43:0.090,
    44:0.092,45:0.094,46:0.096,47:0.098,48:0.100,49:0.102,50:0.104,
    51:0.106,52:0.108,53:0.110,54:0.112,55:0.115,56:0.117,57:0.119,
    58:0.121,59:0.123,60:0.125
};

function calcLateTardy() {
    const mins = parseFloat(document.getElementById('arm_late_minutes').value) || 0;
    let result = 0;
    if (mins <= 0) {
        result = 0;
    } else if (mins <= 60) {
        result = CONVERSION_TABLE[Math.floor(mins)] || 0;
    } else {
        const fullHours = Math.floor(mins / 60);
        const remainder = mins % 60;
        result = fullHours * 0.125;
        if (remainder > 0) result += CONVERSION_TABLE[Math.floor(remainder)] || 0;
    }
    result = Math.round(result * 1000) / 1000;
    document.getElementById('armLateResult').textContent = result.toFixed(3);
    document.getElementById('arm_tardy').value = result > 0 ? result.toFixed(3) : '';
}

function onTardyManualInput() {
    if (_lateCalcActive) {
        _lateCalcActive = false;
        document.getElementById('arm_late_minutes').value = '';
        document.getElementById('armLateResult').textContent = '0.000';
    }
}

/* ════ CONFIRM ADD ROW ════ */
function confirmAddRow() {
    const isHrRow  = document.querySelector('.arm-pill.active')?.dataset.val === 'As per HR';
    const hrVlRaw  = document.getElementById('arm_hr_vl_balance').value.trim();
    const hrSlRaw  = document.getElementById('arm_hr_sl_balance').value.trim();

    const entry = {
        month            : document.getElementById('arm_month').value              || null,
        date_particulars : document.getElementById('arm_particulars').value.trim() || null,
        earned_vl        : document.getElementById('arm_earned_vl').value          || null,
        earned_sl        : document.getElementById('arm_earned_sl').value          || null,
        taken_vl         : document.getElementById('arm_taken_vl').value           || null,
        taken_sl         : document.getElementById('arm_taken_sl').value           || null,
        leave_wop        : document.getElementById('arm_wop').value                || null,
        tardy_undertime  : document.getElementById('arm_tardy').value              || null,
        remarks          : document.getElementById('arm_remarks').value            || null,
        status           : document.getElementById('arm_status').value.trim()      || null,
        is_manual        : 1,
        hr_vl_balance    : (isHrRow && hrVlRaw !== '') ? hrVlRaw : null,
        hr_sl_balance    : (isHrRow && hrSlRaw !== '') ? hrSlRaw : null,
    };

    const tbody  = document.getElementById('lceSheetBody');
    const addRow = tbody.querySelector('tr.add-row-row');

    if (entry.month) {
        const lastMonth = _getLastMonthInSheet(tbody);
        if (parseInt(entry.month) !== lastMonth) appendMonthSep(tbody, parseInt(entry.month));
    }

    if (addRow) addRow.remove();
    appendDataRow(tbody, entry);
    appendAddRowPrompt(tbody);
    recalcAll();
    closeAddRowModal();
    showLcToast('Row Added', 'New entry appended to the sheet.', 'success');
}

// Close modal on backdrop click
document.getElementById('addRowModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddRowModal();
});

/* ════ ROW OPERATIONS ════ */
function deleteRow(tr) {
    const lid = tr.dataset.leaveApplicationId;
    if (lid) { importedLeaveIds.delete(parseInt(lid)); excludedLeaveIds.add(parseInt(lid)); }
    tr.remove();
    recalcAll();
    renumberRows();
}

function renumberRows() {
    let n = 0;
    document.querySelectorAll('#lceSheetBody tr.data-row').forEach(tr => {
        tr.querySelector('td:first-child').textContent = ++n;
    });
}

/* ════ RECALC ALL ════ */
function recalcAll() {
    let vl = parseFloat(document.getElementById('openingVL').value) || 0;
    let sl = parseFloat(document.getElementById('openingSL').value) || 0;
    updateOpeningDisplay();

    document.querySelectorAll('#lceSheetBody tr.data-row').forEach(tr => {
        const g = col => parseFloat(tr.querySelector(`[data-col="${col}"]`)?.value) || 0;

        vl = vl + g('earned_vl') - g('taken_vl') - g('tardy_undertime') - g('leave_wop');
        sl = sl + g('earned_sl') - g('taken_sl');

        // "As per HR" hard override
        const hrVl = tr.dataset.hrVlBalance;
        const hrSl = tr.dataset.hrSlBalance;
        if (hrVl !== '' && hrVl !== undefined && !isNaN(parseFloat(hrVl))) {
            vl = parseFloat(hrVl);
        }
        if (hrSl !== '' && hrSl !== undefined && !isNaN(parseFloat(hrSl))) {
            sl = parseFloat(hrSl);
        }

        const vlEl = tr.querySelector('[data-col="balance_vl"]');
        const slEl = tr.querySelector('[data-col="balance_sl"]');
        if (vlEl) { vlEl.textContent = vl.toFixed(3); vlEl.classList.toggle('negative', vl < 0); }
        if (slEl) { slEl.textContent = sl.toFixed(3); slEl.classList.toggle('negative', sl < 0); }
    });
}

function updateOpeningDisplay() {
    document.getElementById('dispVL').textContent = fmt(parseFloat(document.getElementById('openingVL').value) || 0);
    document.getElementById('dispSL').textContent = fmt(parseFloat(document.getElementById('openingSL').value) || 0);
}

/* ════ SAVE ════ */
async function saveLeaveCard() {
    if (!currentEmployeeId) return;
    const entries = [];
    document.querySelectorAll('#lceSheetBody tr').forEach((tr, idx) => {
        if (tr.dataset.type === 'sep') {
            entries.push({ is_separator: true, month: parseInt(tr.dataset.month || 0), entry_order: idx });
        } else if (tr.dataset.type === 'data') {
            const g = col => {
                const el  = tr.querySelector(`[data-col="${col}"]`);
                if (!el) return null;
                const val = el.tagName === 'SELECT' ? el.value : el.value.trim();
                return val === '' ? null : val;
            };
            entries.push({
                entry_id             : tr.dataset.entryId.startsWith('new_') ? null : tr.dataset.entryId,
                is_separator         : false,
                entry_order          : idx,
                month                : tr.dataset.month ? parseInt(tr.dataset.month) : null,
                date_particulars     : g('date_particulars'),
                earned_vl            : g('earned_vl'),
                earned_sl            : g('earned_sl'),
                taken_vl             : g('taken_vl'),
                taken_sl             : g('taken_sl'),
                leave_wop            : g('leave_wop'),
                tardy_undertime      : g('tardy_undertime'),
                balance_vl           : tr.querySelector('[data-col="balance_vl"]')?.textContent?.replace('—','').trim() || null,
                balance_sl           : tr.querySelector('[data-col="balance_sl"]')?.textContent?.replace('—','').trim() || null,
                remarks              : g('remarks'),
                status               : g('status'),
                leave_application_id : tr.dataset.leaveApplicationId || null,
                is_manual            : parseInt(tr.dataset.isManual ?? 1),
            });
        }
    });

    excludedLeaveIds.forEach(lid => {
        entries.push({
            entry_id: null, is_separator: false, entry_order: 99999,
            month: null, date_particulars: '--- EXCLUDED ---',
            earned_vl: null, earned_sl: null, taken_vl: null, taken_sl: null,
            leave_wop: null, tardy_undertime: null, balance_vl: null, balance_sl: null,
            remarks: null, status: null, leave_application_id: lid, is_manual: 0,
        });
    });

    const payload = {
        employee_id : currentEmployeeId,
        year        : currentYear,
        opening_vl  : document.getElementById('openingVL').value,
        opening_sl  : document.getElementById('openingSL').value,
        entries,
    };

    try {
        const res  = await fetch(`${LC_BASE_URL}/save`, {
            method  : 'POST',
            headers : { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body    : JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.success) {
            currentCardId = data.leave_card_id;
            document.getElementById('lceAutoSaveLbl').textContent = 'Saved ✓ ' + new Date().toLocaleTimeString();
            showLcToast('Saved', 'Leave card saved successfully.', 'success');
        } else {
            showLcToast('Error', data.message || 'Could not save.', 'error');
        }
    } catch (e) {
        showLcToast('Network Error', 'Please check your connection.', 'error');
    }
}

/* ════ PRINT / CLOSE ════ */
function printLeaveCard() { window.open(`${LC_BASE_URL}/${currentEmployeeId}/${currentYear}/print`, '_blank'); }
function exportAllPdf()   { window.open(`${LC_BASE_URL}/print-all`, '_blank'); }
function closeLeaveCard() {
    if (currentEmployeeId && currentYear) _clearNewIds(currentEmployeeId, currentYear);
    document.getElementById('lcEditorPanel').classList.remove('open');
    document.getElementById('lcEditorOverlay').classList.remove('show');
    document.body.style.overflow = '';
    currentEmployeeId = currentCardId = dbCurrentVl = dbCurrentSl = null;
    _oldBalVl = 0; _oldBalSl = 0; _oldBalYear = null; _oldBalFound = false;
    allApplications = []; importedLeaveIds.clear(); excludedLeaveIds.clear();
    document.getElementById('lceOldBalBadge').innerHTML = '';
}

/* ════ KEYBOARD ════ */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeAddRowModal(); closeLeaveCard(); }
    if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); saveLeaveCard(); }
});
document.getElementById('lceSheetBody').addEventListener('keydown', function(e) {
    if (e.key === 'Tab' && !e.shiftKey) {
        const inputs = [...document.querySelectorAll('#lceSheetBody input, #lceSheetBody select')];
        const idx    = inputs.indexOf(document.activeElement);
        if (idx >= 0 && idx < inputs.length - 1) { e.preventDefault(); inputs[idx + 1].focus(); }
    }
    if (e.key === 'Enter') {
        const row = e.target.closest('tr.data-row');
        if (row?.nextElementSibling?.classList.contains('add-row-row')) { e.preventDefault(); openAddRowModal(); }
    }
});

/* ════ HELPERS ════ */
function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function showLcToast(title, msg, type = 'success') {
    const map = {
        success : { bg:'#dcfce7', c:'#16a34a', p:'M5 13l4 4L19 7' },
        error   : { bg:'#fee2e2', c:'#dc2626', p:'M6 18L18 6M6 6l12 12' },
        info    : { bg:'#dbeafe', c:'#2563eb', p:'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    };
    const s = map[type] || map.info;
    document.getElementById('lcToastTitle').textContent  = title;
    document.getElementById('lcToastMsg').textContent    = msg;
    document.getElementById('lcToastIcon').innerHTML     = `<svg style="width:16px;height:16px;" fill="none" stroke="${s.c}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${s.p}"/></svg>`;
    document.getElementById('lcToastIcon').style.background = s.bg;
    const t = document.getElementById('lcToast');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
}
</script>
@endsection