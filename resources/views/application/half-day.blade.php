@extends('layouts.app')
@section('title', 'Certification for Half Day')
@section('page-title', 'Application')

@section('content')
<style>
    /* ── Balance Cards ── */
    .bal-card-dark {
        background: linear-gradient(135deg,#1a3a1a 0%,#2d5a1b 60%,#3d7a2a 100%);
        border-radius:20px; padding:28px; color:#fff; position:relative; overflow:hidden;
    }
    .bal-card-dark::after {
        content:''; position:absolute; right:-20px; top:-20px;
        width:120px; height:120px; border-radius:50%; background:rgba(255,255,255,0.07);
    }
    .bal-card-light {
        background:#fff; border-radius:20px; padding:28px;
        border:1px solid #f0f0f0; box-shadow:0 2px 12px rgba(0,0,0,0.05); position:relative; overflow:hidden;
    }
    .bal-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .bal-icon-dark  { background:rgba(255,255,255,0.18); }
    .bal-icon-green { background:#dcfce7; }

    /* ── Table ── */
    .data-table { width:100%; font-size:13px; border-collapse:collapse; }
    .data-table thead tr { border-bottom:1px solid #f3f4f6; background:#fafafa; }
    .data-table th { padding:10px 14px; text-align:left; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.04em; white-space:nowrap; }
    .data-table td { padding:12px 14px; border-bottom:1px solid #f9fafb; color:#374151; }
    .data-table tbody .hd-row { cursor:pointer; }
    .data-table tbody .hd-row:hover { background:#f0fdf4; }
    .data-table tbody .hd-row:hover td:nth-child(2) { color:#1a3a1a; }

    /* ── Status Badges ── */
    .status-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; white-space:nowrap; }
    .status-badge::before { content:'●'; font-size:8px; }
    .badge-pending   { background:#fef9c3; color:#854d0e; }
    .badge-approved  { background:#dcfce7; color:#14532d; }
    .badge-rejected  { background:#fee2e2; color:#991b1b; }
    .badge-cancelled { background:#f3f4f6; color:#6b7280; }

    /* ── Period Pills ── */
    .period-pill { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
    .period-am { background:#dbeafe; color:#1e40af; }
    .period-pm { background:#ede9fe; color:#5b21b6; }

    /* ── Breadcrumb ── */
    .breadcrumb { display:flex; align-items:center; gap:8px; font-size:13px; color:#6b7280; margin-bottom:24px; }
    .breadcrumb a { color:#6b7280; text-decoration:none; }
    .breadcrumb a:hover { color:#1a3a1a; }
    .breadcrumb .sep { color:#d1d5db; }
    .breadcrumb .current { color:#1a3a1a; font-weight:600; }

    /* ── Toast ── */
    #toast { position:fixed; bottom:24px; right:24px; z-index:300; min-width:280px; background:#fff; border-radius:14px; padding:16px 20px; box-shadow:0 8px 32px rgba(0,0,0,0.15); display:flex; align-items:center; gap:12px; opacity:0; transform:translateY(16px); transition:all 0.3s ease; pointer-events:none; }
    #toast.show { opacity:1; transform:translateY(0); pointer-events:all; }

    /* ── Action Menu ── */
    .action-menu { position:relative; display:inline-block; }
    .action-menu-btn { background:none; border:none; cursor:pointer; padding:4px 8px; border-radius:6px; color:#9ca3af; }
    .action-menu-btn:hover { background:#f3f4f6; color:#374151; }
    .action-dropdown { position:fixed; z-index:9999; background:#fff; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.1); min-width:160px; display:none; }
    .action-dropdown.open { display:block; }
    .action-item { display:flex; align-items:center; gap:8px; padding:9px 14px; font-size:13px; color:#374151; cursor:pointer; border:none; background:none; width:100%; text-align:left; }
    .action-item:hover { background:#f9fafb; }
    .action-item:first-child { border-radius:10px 10px 0 0; }
    .action-item:last-child  { border-radius:0 0 10px 10px; }
    .action-item.danger { color:#ef4444; }
    .action-item.danger:hover { background:#fee2e2; }

    /* ── Overlay ── */
    #panelOverlay { position:fixed; inset:0; background:rgba(0,0,0,0.25); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px); z-index:90; opacity:0; pointer-events:none; transition:opacity 0.3s ease; }
    #panelOverlay.show { opacity:1; pointer-events:all; }

    /* ── Slide Panel ── */
    .slide-panel { position:fixed; top:0; right:0; bottom:0; z-index:100; width:52vw; min-width:460px; max-width:780px; display:flex; flex-direction:column; pointer-events:none; transform:translateX(100%); transition:transform 0.36s cubic-bezier(0.32,0.72,0,1); }
    .slide-panel.open { pointer-events:all; transform:translateX(0); }
    .slide-panel-box { background:#fff; width:100%; height:100%; display:flex; flex-direction:column; box-shadow:-12px 0 60px rgba(0,0,0,0.22); overflow:hidden; }
    .panel-header { background:linear-gradient(135deg,#1a3a1a 0%,#2d5a1b 100%); padding:22px 28px 20px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
    .panel-header h2 { font-size:18px; font-weight:700; color:#fff; margin:0 0 3px; }
    .panel-header p  { font-size:12px; color:rgba(255,255,255,0.6); margin:0; }
    .panel-close { background:rgba(255,255,255,0.15); border:none; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; color:rgba(255,255,255,0.8); transition:background 0.15s; flex-shrink:0; }
    .panel-close:hover { background:rgba(255,255,255,0.28); color:#fff; }
    .panel-body { flex:1; overflow-y:auto; padding:0; background:#f8f9fa; scrollbar-width:thin; scrollbar-color:#d1d5db transparent; }
    .panel-body::-webkit-scrollbar { width:4px; }
    .panel-body::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:99px; }
    .panel-footer { flex-shrink:0; padding:16px 28px; border-top:1px solid #f3f4f6; background:#fff; display:flex; align-items:center; justify-content:flex-end; gap:12px; }
    .panel-footer-spaced { justify-content:space-between; }

    /* ── Form Cards ── */
    .form-section-card { background:#fff; border-radius:12px; margin:16px 20px; padding:20px 22px 18px; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
    .section-heading { display:flex; align-items:center; gap:10px; margin-bottom:18px; }
    .section-icon { width:32px; height:32px; border-radius:8px; background:#f0fdf4; display:flex; align-items:center; justify-content:center; color:#2d5a1b; flex-shrink:0; }
    .section-card-title { font-size:14px; font-weight:700; color:#111827; margin:0; }

    /* ── Detail Panel Cards ── */
    .dp-card { background:#fff; border-radius:12px; margin:16px 20px; padding:20px 22px 18px; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
    .dp-section-heading { display:flex; align-items:center; gap:10px; margin-bottom:16px; }
    .dp-section-icon { width:32px; height:32px; border-radius:8px; background:#f0fdf4; display:flex; align-items:center; justify-content:center; color:#2d5a1b; flex-shrink:0; }
    .dp-section-title { font-size:14px; font-weight:700; color:#111827; margin:0; }
    .dp-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px 24px; }
    .dp-field label { display:block; font-size:10px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:4px; }
    .dp-field p { font-size:13px; color:#111827; font-weight:500; margin:0; }
    .dp-field.span2 { grid-column:span 2; }

    /* ── Form Elements ── */
    .form-field { width:100%; background:#f3f4f6; border:1.5px solid transparent; border-radius:10px; padding:10px 14px; font-size:13px; color:#111827; transition:border-color 0.15s,background 0.15s; outline:none; }
    .form-field:focus { background:#fff; border-color:#2d5a1b; box-shadow:0 0 0 3px rgba(45,90,27,0.08); }
    .form-field:disabled { color:#6b7280; cursor:not-allowed; background:#f3f4f6; }
    .field-label { font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:5px; display:block; }
    .field-label .req { color:#ef4444; }

    /* ── AM/PM Toggle ── */
    .period-toggle { display:flex; gap:10px; }
    .period-btn { flex:1; padding:10px; border-radius:10px; border:1.5px solid #e5e7eb; background:#f9fafb; font-size:13px; font-weight:700; cursor:pointer; transition:all 0.15s; text-align:center; color:#6b7280; }
    .period-btn:hover  { border-color:#2d5a1b; color:#1a3a1a; background:#f0fdf4; }
    .period-btn.sel-am { border-color:#1e40af; background:#dbeafe; color:#1e40af; }
    .period-btn.sel-pm { border-color:#5b21b6; background:#ede9fe; color:#5b21b6; }

    /* ── Buttons ── */
    .btn-pdf { padding:8px 16px; font-size:12px; font-weight:600; border:1.5px solid #e5e7eb; border-radius:8px; color:#374151; background:#fff; cursor:pointer; transition:all 0.15s; display:inline-flex; align-items:center; gap:6px; }
    .btn-pdf:hover { border-color:#2d5a1b; color:#1a3a1a; background:#f0fdf4; }
    .btn-cancel-action { padding:8px 18px; font-size:12px; font-weight:700; border:none; border-radius:8px; color:#fff; background:#dc2626; cursor:pointer; transition:background 0.15s; display:inline-flex; align-items:center; gap:6px; }
    .btn-cancel-action:hover { background:#b91c1c; }
</style>

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">Application</a>
    <span class="sep">›</span>
    <span class="current">Certification for Half Day</span>
</div>

{{-- Balance Cards --}}
@php
    $vlBal = null; $slBal = null;
    foreach ($leaveTypes as $lt) {
        $cb = $creditBalances[$lt->leave_type_id] ?? null;
        if (str_contains(strtolower($lt->type_name), 'vacation')) $vlBal = $cb;
        if (str_contains(strtolower($lt->type_name), 'sick'))     $slBal = $cb;
    }
@endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
    <div class="bal-card-dark">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium mb-2" style="color:rgba(255,255,255,0.75);">Vacation Leave Balance</p>
                <p class="text-4xl font-bold tracking-tight">{{ number_format($vlBal->remaining_balance ?? 0, 2) }}</p>
                @if($vlBal)
                <div class="flex items-center gap-2 mt-3">
                    <span class="text-xs" style="color:rgba(255,255,255,0.65);">Accrued: <strong>{{ $vlBal->total_accrued }}</strong></span>
                    <span style="color:rgba(255,255,255,0.3)">·</span>
                    <span class="text-xs" style="color:rgba(255,255,255,0.65);">Used: <strong>{{ $vlBal->total_used }}</strong></span>
                </div>
                @else
                <p class="text-xs mt-3" style="color:rgba(255,255,255,0.5);">No balance record found</p>
                @endif
            </div>
            <div class="bal-icon bal-icon-dark">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>
    <div class="bal-card-light">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-2">Sick Leave Balance</p>
                <p class="text-4xl font-bold text-gray-800">{{ number_format($slBal->remaining_balance ?? 0, 2) }}</p>
                @if($slBal)
                <div class="flex items-center gap-2 mt-3">
                    <span class="text-xs text-gray-400">Accrued: <strong class="text-gray-600">{{ $slBal->total_accrued }}</strong></span>
                    <span class="text-gray-300">·</span>
                    <span class="text-xs text-gray-400">Used: <strong class="text-red-500">{{ $slBal->total_used }}</strong></span>
                </div>
                @else
                <p class="text-xs text-gray-400 mt-3">No balance record found</p>
                @endif
            </div>
            <div class="bal-icon bal-icon-green">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    <div class="flex items-center justify-between px-6 pt-4 pb-3" style="border-bottom:1px solid #f3f4f6;">
        <div>
            <p class="font-bold text-gray-800 text-sm">Half Day Certification List</p>
            <p class="text-xs text-gray-400 mt-0.5">Each half day deducts 0.5 credits from the selected leave type</p>
        </div>
        <button onclick="openApplyPanel()"
                class="flex items-center gap-2 px-5 py-2.5 text-sm text-white font-semibold rounded-lg transition"
                style="background:#1a3a1a;" onmouseover="this.style.background='#2d5a1b'" onmouseout="this.style.background='#1a3a1a'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            File Certification
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 px-6 py-4" style="border-bottom:1px solid #f9fafb;">
        <div class="relative">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            <input type="text" placeholder="Search..." id="searchHd" oninput="filterHd()"
                   class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-green-700 w-48">
        </div>
        <div class="relative">
            <select id="filterStatus" onchange="filterHd()" class="appearance-none pl-3 pr-8 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-green-700 bg-white">
                <option value="">All Status</option>
                <option value="PENDING">Pending</option>
                <option value="APPROVED">Approved</option>
                <option value="REJECTED">Rejected</option>
                <option value="CANCELLED">Cancelled</option>
            </select>
            <svg class="w-4 h-4 text-gray-400 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
        <div class="relative">
            <select id="filterMonth" onchange="filterHd()" class="appearance-none pl-3 pr-8 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-green-700 bg-white">
                <option value="">All Months</option>
                @foreach(range(1,12) as $m)
                <option value="{{ str_pad($m,2,'0',STR_PAD_LEFT) }}" {{ now()->month == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                </option>
                @endforeach
            </select>
            <svg class="w-4 h-4 text-gray-400 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Employee ID</th><th>Name</th><th>Application Date</th>
                    <th>Date of Absence</th><th>Period</th><th>Leave Type</th>
                    <th>Status</th><th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody id="hdTbody">
                @forelse($halfDays as $hd)
                <tr class="hd-row"
                    data-status="{{ $hd->status }}"
                    data-month="{{ \Carbon\Carbon::parse($hd->date_of_absence)->format('m') }}"
                    data-hd-id="{{ $hd->half_day_id }}"
                    onclick="openDetailPanel({{ $hd->half_day_id }}, event)">
                    <td class="font-bold font-mono">{{ $employee->formatted_employee_id }}</td>
                    <td class="font-medium">{{ $employee->last_name }}, {{ $employee->first_name }}</td>
                    <td class="text-gray-500">{{ \Carbon\Carbon::parse($hd->application_date)->format('M d, Y') }}</td>
                    <td class="text-gray-700 font-medium">{{ \Carbon\Carbon::parse($hd->date_of_absence)->format('M d, Y') }}</td>
                    <td>
                        <span class="period-pill {{ $hd->time_period === 'AM' ? 'period-am' : 'period-pm' }}">
                            {{ $hd->time_period }}
                        </span>
                    </td>
                    <td class="text-gray-500 text-xs">{{ $hd->leaveType->type_name ?? '—' }}</td>
                    <td>
                        @php
                            $sc = match(strtoupper($hd->status)) {
                                'APPROVED'  => 'badge-approved',
                                'REJECTED'  => 'badge-rejected',
                                'CANCELLED' => 'badge-cancelled',
                                default     => 'badge-pending',
                            };
                        @endphp
                        <span class="status-badge {{ $sc }}">{{ ucfirst(strtolower($hd->status)) }}</span>
                    </td>
                    <td class="text-right pr-4" onclick="event.stopPropagation()">
                        <div class="action-menu">
                            <button class="action-menu-btn" onclick="toggleMenu(this,event)">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg>
                            </button>
                            <div class="action-dropdown">
                                <button class="action-item" onclick="viewPdf({{ $hd->half_day_id }})">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    View / PDF
                                </button>
                                @if($hd->status === 'PENDING')
                                <button class="action-item danger" onclick="cancelHd({{ $hd->half_day_id }})">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Cancel
                                </button>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">No half day certifications found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Overlay --}}
<div id="panelOverlay" onclick="closeAllPanels()"></div>

{{-- ════ DETAIL PANEL ════ --}}
<div id="detailPanel" class="slide-panel">
    <div class="slide-panel-box">
        <div class="panel-header">
            <div>
                <h2>Half Day Certification Details</h2>
                <p id="dpSubtitle">Loading…</p>
            </div>
            <button class="panel-close" onclick="closeDetailPanel()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="panel-body" id="dpBody"></div>
        <div class="panel-footer panel-footer-spaced">
            <button class="btn-pdf" onclick="viewPdfFromPanel()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                View PDF
            </button>
            <div id="dpActionBtns"></div>
        </div>
    </div>
</div>

{{-- ════ APPLY PANEL ════ --}}
<div id="applyPanel" class="slide-panel">
    <div class="slide-panel-box">
        <div class="panel-header">
            <div>
                <h2>File Certification for Half Day</h2>
                <p>Deducts 0.5 leave credits upon approval</p>
            </div>
            <button class="panel-close" onclick="closeApplyPanel()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="panel-body">
            <form id="applyForm">
                @csrf
                {{-- Personal Info --}}
                <div class="form-section-card">
                    <div class="section-heading">
                        <div class="section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                        <p class="section-card-title">Personal Information</p>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                        <div><label class="field-label">First Name</label><input type="text" class="form-field" value="{{ $employee->first_name }}" disabled></div>
                        <div><label class="field-label">Office / Department</label><input type="text" class="form-field" value="{{ $employee->department->department_name ?? 'OPAG' }}" disabled></div>
                        <div><label class="field-label">Last Name</label><input type="text" class="form-field" value="{{ $employee->last_name }}" disabled></div>
                        <div><label class="field-label">Position</label><input type="text" class="form-field" value="{{ $employee->position->position_name ?? '—' }}" disabled></div>
                        <div><label class="field-label">Date of Filing</label><input type="text" class="form-field" value="{{ now()->format('F d, Y') }}" disabled></div>
                        <div><label class="field-label">Salary</label><input type="text" class="form-field" value="₱{{ number_format($employee->salary, 2) }}" disabled></div>
                    </div>
                </div>

                {{-- Certification Details --}}
                <div class="form-section-card" style="margin-bottom:20px;">
                    <div class="section-heading">
                        <div class="section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                        <p class="section-card-title">Certification Details</p>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                        <div>
                            <label class="field-label">Leave Type <span class="req">*</span></label>
                            <select id="f_leave_type_id" name="leave_type_id" class="form-field" onchange="selectLeaveType(this)">
                                <option value="" disabled selected>Select Leave Type</option>
                                @foreach($leaveTypes as $lt)
                                <option value="{{ $lt->leave_type_id }}"
                                        data-balance-id="{{ $creditBalances[$lt->leave_type_id]->credit_balance_id ?? '' }}"
                                        data-remaining="{{ $creditBalances[$lt->leave_type_id]->remaining_balance ?? 0 }}">
                                    {{ $lt->type_name }}
                                </option>
                                @endforeach
                            </select>
                            <input type="hidden" id="f_credit_balance_id" name="credit_balance_id">
                            <p class="text-xs text-red-400 mt-1 hidden" id="err_leave_type">Please select a leave type.</p>

                            <div id="balanceDisplay" class="hidden mt-2 rounded-xl p-3" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                                <p class="text-xs font-semibold text-green-700">Available Balance</p>
                                <p class="text-xl font-bold text-green-800 mt-0.5"><span id="balanceVal">0</span> <span class="text-xs font-normal text-green-600">days</span></p>
                                <p class="text-xs text-green-600 mt-0.5">0.5 days will be deducted upon approval</p>
                            </div>
                            <div id="insufficientWarn" class="hidden mt-2 p-3 rounded-xl text-xs font-semibold text-red-600" style="background:#fef2f2;border:1px solid #fecaca;">
                                ⚠ Insufficient balance. You need at least 0.5 days.
                            </div>
                        </div>

                        <div>
                            <label class="field-label">Date of Absence <span class="req">*</span></label>
                            <input type="date" id="f_date_of_absence" name="date_of_absence" class="form-field"
                                   min="{{ now()->toDateString() }}">
                            <p class="text-xs text-red-400 mt-1 hidden" id="err_date">Please select a date.</p>
                        </div>

                        <div class="col-span-2">
                            <label class="field-label">Time Period <span class="req">*</span></label>
                            <div class="period-toggle">
                                <button type="button" class="period-btn" id="btnAM" onclick="selectPeriod('AM')">
                                    🌅 AM (Morning)
                                </button>
                                <button type="button" class="period-btn" id="btnPM" onclick="selectPeriod('PM')">
                                    🌆 PM (Afternoon)
                                </button>
                            </div>
                            <input type="hidden" id="f_time_period" name="time_period">
                            <p class="text-xs text-red-400 mt-1 hidden" id="err_period">Please select a time period.</p>
                        </div>

                        <div class="col-span-2">
                            <label class="field-label">Reason / Remarks</label>
                            <textarea id="f_reason" name="reason" rows="2" class="form-field" placeholder="Optional reason for half day absence…"></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer">
            <button onclick="closeApplyPanel()" class="px-6 py-2.5 text-sm font-semibold border-2 border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
            <button onclick="submitHd()" id="submitBtn"
                    class="px-8 py-2.5 text-sm font-semibold text-white rounded-xl transition"
                    style="background:#1a3a1a;" onmouseover="this.style.background='#2d5a1b'" onmouseout="this.style.background='#1a3a1a'">
                Submit Certification
            </button>
        </div>
    </div>
</div>

{{-- ════ CANCEL MODAL ════ --}}
<div id="cancelModal" style="position:fixed;inset:0;z-index:200;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,0.45);backdrop-filter:blur(3px);opacity:0;transition:opacity 0.2s;">
    <div id="cancelModalCard" style="background:#fff;border-radius:20px;padding:32px;width:420px;max-width:94vw;box-shadow:0 24px 64px rgba(0,0,0,0.2);transform:scale(0.93);transition:transform 0.25s cubic-bezier(0.34,1.56,0.64,1);">
        <div class="flex items-center gap-4 mb-5">
            <div style="width:52px;height:52px;border-radius:16px;background:#fff7ed;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:26px;height:26px;color:#ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-800" style="font-size:16px;">Cancel Certification?</h3>
                <p class="text-sm text-gray-500 mt-0.5">This action cannot be undone.</p>
            </div>
        </div>
        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:14px 16px;margin-bottom:20px;">
            <p class="text-sm font-medium" style="color:#9a3412;">Once cancelled, this certification will be permanently marked as <strong>Cancelled</strong>. No leave credits will be affected.</p>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="closeCancelModal()" class="px-6 py-2.5 text-sm font-semibold border-2 border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Keep It</button>
            <button onclick="confirmCancel()" id="confirmCancelBtn"
                    class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl flex items-center gap-2"
                    style="background:#dc2626;" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Yes, Cancel It
            </button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="toast">
    <div id="toastIcon" class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"></div>
    <div>
        <p class="text-sm font-bold text-gray-800" id="toastTitle"></p>
        <p class="text-xs text-gray-500 mt-0.5" id="toastMsg"></p>
    </div>
</div>

<script>
/* ════════════════════════════════════════════════════════
   SERVER DATA
════════════════════════════════════════════════════════ */
const CSRF       = "{{ csrf_token() }}";
const STORE_URL  = "{{ route('halfday.store') }}";
const CANCEL_URL = "{{ url('application/halfday') }}";
const PDF_URL    = "{{ url('application/halfday') }}";

const HD_DATA = {!! json_encode($halfDays->keyBy('half_day_id')->map(function($h) {
    return [
        'half_day_id'      => $h->half_day_id,
        'leave_type'       => optional($h->leaveType)->type_name ?? '—',
        'application_date' => \Carbon\Carbon::parse($h->application_date)->format('M d, Y'),
        'date_of_absence'  => \Carbon\Carbon::parse($h->date_of_absence)->format('M d, Y'),
        'time_period'      => $h->time_period,
        'reason'           => $h->reason ?? '',
        'status'           => $h->status,
        'approved_date'    => $h->approved_date
            ? \Carbon\Carbon::parse($h->approved_date)->format('M d, Y') : '—',
    ];
})) !!};

/* ════════════════════════════════════════════════════════
   STATE
════════════════════════════════════════════════════════ */
let activePanelHdId = null;
let pendingCancelId = null;

/* ════════════════════════════════════════════════════════
   LEAVE TYPE SELECT
════════════════════════════════════════════════════════ */
function selectLeaveType(sel) {
    const opt       = sel.options[sel.selectedIndex];
    const remaining = parseFloat(opt.dataset.remaining) || 0;
    const balanceId = opt.dataset.balanceId || '';

    document.getElementById('f_credit_balance_id').value = balanceId;
    document.getElementById('err_leave_type').classList.add('hidden');

    const display = document.getElementById('balanceDisplay');
    const warn    = document.getElementById('insufficientWarn');

    if (balanceId) {
        document.getElementById('balanceVal').textContent = remaining;
        display.classList.remove('hidden');
        const isInsufficient = remaining < 0.5;
        warn.classList.toggle('hidden', !isInsufficient);
        document.getElementById('submitBtn').disabled      = isInsufficient;
        document.getElementById('submitBtn').style.opacity = isInsufficient ? '0.5' : '1';
    } else {
        display.classList.add('hidden');
        warn.classList.add('hidden');
    }
}

/* ════════════════════════════════════════════════════════
   AM / PM TOGGLE
════════════════════════════════════════════════════════ */
function selectPeriod(period) {
    document.getElementById('f_time_period').value = period;
    document.getElementById('err_period').classList.add('hidden');
    document.getElementById('btnAM').className = 'period-btn' + (period === 'AM' ? ' sel-am' : '');
    document.getElementById('btnPM').className = 'period-btn' + (period === 'PM' ? ' sel-pm' : '');
}

/* ════════════════════════════════════════════════════════
   PANELS
════════════════════════════════════════════════════════ */
function openApplyPanel() {
    resetApplyForm();
    document.getElementById('applyPanel').classList.add('open');
    document.getElementById('panelOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeApplyPanel() {
    document.getElementById('applyPanel').classList.remove('open');
    hidePanelOverlayIfNoneOpen();
    document.body.style.overflow = '';
}
function closeAllPanels() {
    ['detailPanel','applyPanel'].forEach(id => document.getElementById(id).classList.remove('open'));
    document.getElementById('panelOverlay').classList.remove('show');
    document.body.style.overflow = '';
    activePanelHdId = null;
}
function hidePanelOverlayIfNoneOpen() {
    if (!document.querySelector('.slide-panel.open')) document.getElementById('panelOverlay').classList.remove('show');
}
function resetApplyForm() {
    document.getElementById('applyForm').reset();
    document.getElementById('f_credit_balance_id').value = '';
    document.getElementById('f_time_period').value       = '';
    document.getElementById('btnAM').className = 'period-btn';
    document.getElementById('btnPM').className = 'period-btn';
    document.getElementById('balanceDisplay').classList.add('hidden');
    document.getElementById('insufficientWarn').classList.add('hidden');
    document.getElementById('submitBtn').disabled      = false;
    document.getElementById('submitBtn').style.opacity = '1';
    ['err_leave_type','err_date','err_period'].forEach(id => document.getElementById(id).classList.add('hidden'));
}

/* ════════════════════════════════════════════════════════
   DETAIL PANEL
════════════════════════════════════════════════════════ */
function openDetailPanel(hdId, e) {
    if (e) e.stopPropagation();
    const d = HD_DATA[hdId]; if (!d) return;
    activePanelHdId = hdId;

    document.getElementById('dpSubtitle').textContent = `${d.leave_type} · ${d.time_period} · ${d.date_of_absence}`;

    const SC = { PENDING:'#fef9c3|#854d0e', APPROVED:'#dcfce7|#14532d', REJECTED:'#fee2e2|#991b1b', CANCELLED:'#f3f4f6|#6b7280' };
    const [sBg,sC] = (SC[d.status] || '#f3f4f6|#6b7280').split('|');
    const sl = d.status.charAt(0) + d.status.slice(1).toLowerCase();
    const [pBg,pC] = (d.time_period === 'AM' ? '#dbeafe|#1e40af' : '#ede9fe|#5b21b6').split('|');

    let html = `<div class="dp-card">
        <div class="dp-section-heading">
            <div class="dp-section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            <p class="dp-section-title">Certification Details</p>
        </div>
        <div class="dp-grid">
            <div class="dp-field"><label>Leave Type</label><p>${d.leave_type}</p></div>
            <div class="dp-field"><label>Application Date</label><p>${d.application_date}</p></div>
            <div class="dp-field"><label>Date of Absence</label><p style="font-weight:700;">${d.date_of_absence}</p></div>
            <div class="dp-field"><label>Time Period</label><p>
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:${pBg};color:${pC};">● ${d.time_period}</span>
            </p></div>
            <div class="dp-field"><label>Credits to Deduct</label><p>0.5 days upon approval</p></div>
            <div class="dp-field"><label>Status</label><p>
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:${sBg};color:${sC};">● ${sl}</span>
            </p></div>`;

    if (d.status === 'APPROVED') {
        html += `<div class="dp-field"><label>Approved Date</label><p>${d.approved_date}</p></div>`;
    }
    if (d.reason) {
        html += `<div class="dp-field span2"><label>Reason / Remarks</label><p>${d.reason}</p></div>`;
    }

    html += `</div></div><div style="height:8px;"></div>`;
    document.getElementById('dpBody').innerHTML = html;

    const labels = { APPROVED:'Certification Approved', REJECTED:'Certification Rejected', CANCELLED:'Certification Cancelled' };
    document.getElementById('dpActionBtns').innerHTML = d.status === 'PENDING'
        ? `<button class="btn-cancel-action" onclick="cancelHd(${hdId})">
               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
               Cancel Certification
           </button>`
        : `<span style="font-size:12px;color:#6b7280;font-weight:600;">${labels[d.status] || 'Status: '+sl}</span>`;

    document.getElementById('detailPanel').classList.add('open');
    document.getElementById('panelOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeDetailPanel() {
    document.getElementById('detailPanel').classList.remove('open');
    hidePanelOverlayIfNoneOpen();
    document.body.style.overflow = '';
    activePanelHdId = null;
}
function viewPdfFromPanel() { if (activePanelHdId) viewPdf(activePanelHdId); }

/* ════════════════════════════════════════════════════════
   SUBMIT
════════════════════════════════════════════════════════ */
function submitHd() {
    const ltId    = document.getElementById('f_leave_type_id').value;
    const cbId    = document.getElementById('f_credit_balance_id').value;
    const absDate = document.getElementById('f_date_of_absence').value;
    const period  = document.getElementById('f_time_period').value;

    document.getElementById('err_leave_type').classList.toggle('hidden', !!ltId);
    document.getElementById('err_date').classList.toggle('hidden',       !!absDate);
    document.getElementById('err_period').classList.toggle('hidden',     !!period);

    if (!ltId || !cbId || !absDate || !period) return;

    const btn = document.getElementById('submitBtn');
    btn.textContent = 'Submitting…'; btn.disabled = true;

    const body = new FormData();
    body.append('leave_type_id',     ltId);
    body.append('credit_balance_id', cbId);
    body.append('date_of_absence',   absDate);
    body.append('time_period',       period);
    body.append('reason',            document.getElementById('f_reason').value);
    body.append('_token',            CSRF);

    fetch(STORE_URL, { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeApplyPanel();
            showToast('Certification Submitted!', 'Your half day certification is now pending approval.', 'success');
            setTimeout(() => location.reload(), 1800);
        } else {
            showToast('Error', data.message || 'Something went wrong.', 'error');
        }
    })
    .catch(() => showToast('Network Error', 'Please check your connection.', 'error'))
    .finally(() => { btn.textContent = 'Submit Certification'; btn.disabled = false; });
}

/* ════════════════════════════════════════════════════════
   CANCEL MODAL
════════════════════════════════════════════════════════ */
function cancelHd(id) {
    pendingCancelId = id;
    const m = document.getElementById('cancelModal'), c = document.getElementById('cancelModalCard');
    m.style.display='flex'; c.style.transform='scale(0.93)';
    requestAnimationFrame(() => requestAnimationFrame(() => { m.style.opacity='1'; c.style.transform='scale(1)'; }));
    document.body.style.overflow = 'hidden';
}
function closeCancelModal() {
    const m = document.getElementById('cancelModal'); m.style.opacity='0';
    document.getElementById('cancelModalCard').style.transform='scale(0.93)';
    setTimeout(() => { m.style.display='none'; }, 200);
    document.body.style.overflow=''; pendingCancelId=null;
}
function confirmCancel() {
    if (!pendingCancelId) return;
    const id=pendingCancelId, btn=document.getElementById('confirmCancelBtn');
    btn.innerHTML='<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Cancelling…';
    btn.disabled=true;
    closeCancelModal(); closeDetailPanel();
    fetch(`${CANCEL_URL}/${id}/cancel`, {
        method:'POST', headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF}, body:new FormData(),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { showToast('Certification Cancelled','Your half day certification has been cancelled.','warning'); setTimeout(()=>location.reload(),1800); }
        else showToast('Error', data.message||'Could not cancel.','error');
    })
    .catch(() => showToast('Network Error','Please check your connection.','error'))
    .finally(() => {
        btn.innerHTML='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Yes, Cancel It';
        btn.disabled=false;
    });
}

/* ════════════════════════════════════════════════════════
   VIEW PDF → opens halfday-pdf.blade.php in a new tab
   Route: GET /application/halfday/{id}/pdf
════════════════════════════════════════════════════════ */
function viewPdf(id) {
    window.open(PDF_URL + '/' + id + '/pdf', '_blank');
}

/* ════════════════════════════════════════════════════════
   ACTION MENUS
════════════════════════════════════════════════════════ */
function toggleMenu(btn, e) {
    if (e) e.stopPropagation();
    const dd=btn.nextElementSibling, isOpen=dd.classList.contains('open');
    document.querySelectorAll('.action-dropdown.open').forEach(d => d.classList.remove('open'));
    if (!isOpen) {
        dd.classList.add('open');
        const rect=btn.getBoundingClientRect();
        dd.style.top=(rect.bottom+4)+'px'; dd.style.left=(rect.right-160)+'px';
        dd.style.transform=window.innerHeight-rect.bottom<120?`translateY(calc(-100% - ${rect.height+8}px))`:'';
    }
}
document.addEventListener('click', () => document.querySelectorAll('.action-dropdown.open').forEach(d=>d.classList.remove('open')));
window.addEventListener('scroll', () => document.querySelectorAll('.action-dropdown.open').forEach(d=>d.classList.remove('open')), true);

/* ════════════════════════════════════════════════════════
   FILTER
════════════════════════════════════════════════════════ */
function filterHd() {
    const q=document.getElementById('searchHd').value.toLowerCase();
    const st=document.getElementById('filterStatus').value;
    const mo=document.getElementById('filterMonth').value;
    document.querySelectorAll('#hdTbody .hd-row').forEach(r => {
        r.style.display=((!q||r.textContent.toLowerCase().includes(q))&&(!st||r.dataset.status===st)&&(!mo||r.dataset.month===mo))?'':'none';
    });
}

/* ════════════════════════════════════════════════════════
   TOAST
════════════════════════════════════════════════════════ */
function showToast(title, msg, type='success') {
    const map={success:{bg:'#dcfce7',c:'#16a34a',p:'M5 13l4 4L19 7'},error:{bg:'#fee2e2',c:'#dc2626',p:'M6 18L18 6M6 6l12 12'},warning:{bg:'#fef9c3',c:'#ca8a04',p:'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'}};
    const s=map[type]||map.success;
    document.getElementById('toastTitle').textContent=title;
    document.getElementById('toastMsg').textContent=msg;
    const icon=document.getElementById('toastIcon');
    icon.innerHTML=`<svg class="w-5 h-5" fill="none" stroke="${s.c}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${s.p}"/></svg>`;
    icon.style.background=s.bg;
    const t=document.getElementById('toast'); t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),3500);
}

document.addEventListener('keydown', e => {
    if (e.key==='Escape') { closeDetailPanel(); closeApplyPanel(); closeCancelModal(); }
});

@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast('Success','{{ session("success") }}','success'));
@endif
</script>
@endsection