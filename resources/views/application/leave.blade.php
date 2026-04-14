@extends('layouts.app')
@section('title', 'Application for Leave')
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
        border:1px solid #f0f0f0; box-shadow:0 2px 12px rgba(0,0,0,0.05);
        position:relative; overflow:hidden;
    }
    .bal-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .bal-icon-dark  { background:rgba(255,255,255,0.18); }
    .bal-icon-green { background:#dcfce7; }

    /* ── Tabs ── */
    .tab-btn { padding:10px 4px; font-size:14px; font-weight:500; color:#6b7280; border:none; background:none; border-bottom:2px solid transparent; cursor:pointer; transition:all 0.2s; white-space:nowrap; }
    .tab-btn.active { color:#1a3a1a; border-bottom-color:#2d5a1b; font-weight:700; }
    .tab-btn:hover:not(.active) { color:#374151; }

    /* ── Table ── */
    .data-table { width:100%; font-size:13px; border-collapse:collapse; }
    .data-table thead tr { border-bottom:1px solid #f3f4f6; background:#fafafa; }
    .data-table th { padding:10px 14px; text-align:left; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.04em; white-space:nowrap; }
    .data-table td { padding:12px 14px; border-bottom:1px solid #f9fafb; color:#374151; }
    .data-table tbody .leave-row { cursor:pointer; }
    .data-table tbody .leave-row:hover { background:#f0fdf4; }
    .data-table tbody .leave-row:hover td:nth-child(2) { color:#1a3a1a; }

    /* ── Status Badges ── */
    .status-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; white-space:nowrap; }
    .status-badge::before { content:'●'; font-size:8px; }
    .badge-pending    { background:#fef9c3; color:#854d0e; }
    .badge-approved   { background:#dcfce7; color:#14532d; }
    .badge-rejected   { background:#fee2e2; color:#991b1b; }
    .badge-cancelled  { background:#f3f4f6; color:#6b7280; }
    .badge-received   { background:#dbeafe; color:#1e40af; }
    .badge-on-process { background:#ede9fe; color:#5b21b6; }

    /* ── Breadcrumb ── */
    .breadcrumb { display:flex; align-items:center; gap:8px; font-size:13px; color:#6b7280; margin-bottom:24px; flex-wrap:wrap; }
    .breadcrumb a { color:#6b7280; text-decoration:none; }
    .breadcrumb a:hover { color:#1a3a1a; }
    .breadcrumb .sep { color:#d1d5db; }
    .breadcrumb .current { color:#1a3a1a; font-weight:600; }

    /* ── Toast ── */
    #toast { position:fixed; bottom:24px; right:24px; z-index:300; min-width:280px; background:#fff; border-radius:14px; padding:16px 20px; box-shadow:0 8px 32px rgba(0,0,0,0.15); display:flex; align-items:center; gap:12px; opacity:0; transform:translateY(16px); transition:all 0.3s ease; pointer-events:none; max-width:calc(100vw - 32px); }
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
    .slide-panel { position:fixed; top:0; right:0; bottom:0; z-index:100; width:55vw; min-width:480px; max-width:860px; display:flex; flex-direction:column; pointer-events:none; transform:translateX(100%); transition:transform 0.36s cubic-bezier(0.32,0.72,0,1); }
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

    /* ── Panel Footer ── */
    .panel-footer { flex-shrink:0; padding:16px 28px; border-top:1px solid #f3f4f6; background:#fff; display:flex; align-items:center; justify-content:flex-end; gap:12px; transition:opacity 0.2s, filter 0.2s; }
    .panel-footer-spaced { justify-content:space-between; }
    .panel-footer.cal-active { opacity:0.4; filter:blur(2px); pointer-events:none; }

    /* ── Form Section ── */
    .form-section-card { background:#fff; border-radius:12px; margin:16px 20px; padding:20px 22px 18px; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
    .specify-wrap { animation:fadeIn 0.15s ease; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(-4px)} to{opacity:1;transform:translateY(0)} }
    .section-heading { display:flex; align-items:center; gap:10px; margin-bottom:18px; }
    .section-icon { width:32px; height:32px; border-radius:8px; background:#f0fdf4; display:flex; align-items:center; justify-content:center; color:#2d5a1b; flex-shrink:0; }
    .section-card-title { font-size:14px; font-weight:700; color:#111827; margin:0; }

    /* ── Detail Panel ── */
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
    .btn-pdf { padding:8px 16px; font-size:12px; font-weight:600; border:1.5px solid #e5e7eb; border-radius:8px; color:#374151; background:#fff; cursor:pointer; transition:all 0.15s; display:inline-flex; align-items:center; gap:6px; }
    .btn-pdf:hover { border-color:#2d5a1b; color:#1a3a1a; background:#f0fdf4; }
    .btn-cancel-action { padding:8px 18px; font-size:12px; font-weight:700; border:none; border-radius:8px; color:#fff; background:#dc2626; cursor:pointer; transition:background 0.15s; display:inline-flex; align-items:center; gap:6px; }
    .btn-cancel-action:hover { background:#b91c1c; }

    /* ══ COMPACT CALENDAR PICKER ══ */
    .cal-wrap { position:relative; }
    .cal-trigger { width:100%; background:#f3f4f6; border:1.5px solid transparent; border-radius:10px; padding:10px 14px; font-size:13px; color:#111827; cursor:pointer; display:flex; align-items:center; justify-content:space-between; transition:border-color 0.15s,background 0.15s; outline:none; text-align:left; font-family:inherit; }
    .cal-trigger:focus, .cal-trigger.open { background:#fff; border-color:#2d5a1b; box-shadow:0 0 0 3px rgba(45,90,27,0.08); }
    .cal-trigger-text { flex:1; color:#111827; font-size:13px; }
    .cal-trigger-text.placeholder { color:#9ca3af; }
    .cal-popup {
        position:absolute; top:calc(100% + 6px); left:0; z-index:500;
        width:300px;
        background:#fff; border:1.5px solid #e5e7eb; border-radius:14px;
        box-shadow:0 12px 36px rgba(0,0,0,0.15); padding:14px 16px;
        animation:calFadeIn 0.15s ease;
    }
    @keyframes calFadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
    .cal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }
    .cal-nav { background:none; border:none; cursor:pointer; padding:5px 8px; border-radius:8px; color:#374151; display:flex; align-items:center; transition:background 0.12s; }
    .cal-nav:hover { background:#f3f4f6; }
    .cal-month-label { font-size:13px; font-weight:800; color:#111827; }
    .cal-weekdays { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; margin-bottom:4px; }
    .cal-wd { text-align:center; font-size:9px; font-weight:800; color:#9ca3af; padding:3px 0; text-transform:uppercase; }
    .cal-days { display:grid; grid-template-columns:repeat(7,1fr); gap:3px; }
    .cal-day { aspect-ratio:1; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:500; border-radius:7px; cursor:pointer; transition:all 0.12s; border:1.5px solid transparent; color:#374151; background:none; line-height:1; width:100%; font-family:inherit; }
    .cal-day:hover:not(.cal-disabled):not(.cal-other) { background:#f0fdf4; border-color:#bbf7d0; color:#14532d; }
    .cal-day.cal-selected { background:#1a3a1a !important; color:#fff !important; border-color:#1a3a1a !important; font-weight:700; }
    .cal-day.cal-today { border-color:#2d5a1b; color:#2d5a1b; font-weight:700; }
    .cal-day.cal-today.cal-selected { border-color:#1a3a1a; color:#fff; }
    .cal-day.cal-weekend { color:#d1d5db; cursor:not-allowed; }
    .cal-day.cal-weekend:hover { background:none; border-color:transparent; color:#d1d5db; }
    .cal-day.cal-other { color:#e5e7eb; cursor:default; pointer-events:none; }
    .cal-day.cal-disabled { color:#d1d5db; cursor:not-allowed; background:none; }
    .cal-day.cal-disabled:hover { background:none; border-color:transparent; color:#d1d5db; }
    .cal-footer { margin-top:10px; padding-top:10px; border-top:1px solid #f3f4f6; display:flex; align-items:center; justify-content:space-between; gap:6px; }
    .cal-selected-count { font-size:11px; color:#6b7280; }
    .cal-selected-count strong { color:#1a3a1a; font-weight:800; }
    .cal-clear-btn { font-size:11px; color:#dc2626; background:none; border:none; cursor:pointer; font-weight:700; padding:4px 8px; border-radius:6px; font-family:inherit; }
    .cal-clear-btn:hover { background:#fee2e2; }
    .cal-done-btn { font-size:12px; font-weight:700; color:#fff; background:#1a3a1a; border:none; border-radius:8px; padding:6px 16px; cursor:pointer; font-family:inherit; transition:background 0.12s; }
    .cal-done-btn:hover { background:#2d5a1b; }
    .cal-chips-area { margin-top:8px; display:flex; flex-wrap:wrap; gap:4px; }
    .cal-chip { display:inline-flex; align-items:center; gap:4px; padding:3px 8px; background:#f0fdf4; border:1.5px solid #bbf7d0; border-radius:20px; font-size:11px; color:#14532d; font-weight:700; }
    .cal-chip-x { cursor:pointer; color:#9ca3af; font-size:13px; line-height:1; transition:color 0.1s; display:flex; align-items:center; }
    .cal-chip-x:hover { color:#dc2626; }

    /* ══════════════════════════════════════
       RESPONSIVE STYLES
    ══════════════════════════════════════ */

    /* Mobile: full-width panels */
    @media (max-width: 640px) {
        .slide-panel {
            width: 100vw;
            min-width: unset;
            max-width: 100vw;
        }
        .panel-header { padding: 16px 18px 14px; }
        .panel-header h2 { font-size: 15px; }
        .panel-footer { padding: 12px 16px; gap: 8px; }
        .panel-footer button { padding: 8px 14px !important; font-size: 12px !important; }
        .form-section-card { margin: 12px 12px; padding: 16px 14px 14px; }
        .dp-card { margin: 12px 12px; padding: 16px 14px 14px; }

        /* Stack form grid to single column on mobile */
        .form-section-card .grid.grid-cols-2 { grid-template-columns: 1fr !important; }
        .form-section-card .col-span-2 { grid-column: span 1 !important; }

        /* Stack detail grid to single column */
        .dp-grid { grid-template-columns: 1fr !important; }
        .dp-field.span2 { grid-column: span 1 !important; }

        /* Balance cards: compact on mobile */
        .bal-card-dark,
        .bal-card-light { padding: 18px 16px; border-radius: 14px; }
        .bal-card-dark .text-4xl,
        .bal-card-light .text-4xl { font-size: 1.75rem; }

        /* Tabs: smaller font, tighter gaps */
        .tab-btn { font-size: 12px; padding: 8px 2px; }
        .tab-btn-wrap { gap: 12px !important; }

        /* Header bar: stack on very small screens */
        .tab-header-row { flex-direction: column; align-items: flex-start !important; gap: 10px; padding-bottom: 10px !important; }
        .tab-header-row .pb-3 { padding-bottom: 0 !important; width: 100%; }
        .tab-header-row #applyBtn { width: 100%; justify-content: center; }

        /* Filter bar: wrap tightly */
        .filter-bar { flex-direction: column !important; align-items: stretch !important; gap: 8px !important; padding: 12px !important; }
        .filter-bar .relative input,
        .filter-bar .relative select { width: 100% !important; }
        .filter-bar .relative { width: 100%; }

        /* Table: hide less-important columns */
        .data-table th:nth-child(5),
        .data-table td:nth-child(5),
        .data-table th:nth-child(6),
        .data-table td:nth-child(6) { display: none; }

        /* Cancel modal: full width */
        #cancelModalCard { width: 95vw !important; padding: 20px !important; }

        /* Calendar popup: full width within trigger container */
        .cal-popup { width: calc(100vw - 56px); min-width: 240px; }

        /* Toast: full width at bottom */
        #toast { left: 16px; right: 16px; min-width: unset; bottom: 16px; }
    }

    /* Tablet: slightly narrower panels */
    @media (min-width: 641px) and (max-width: 1024px) {
        .slide-panel {
            width: 80vw;
            min-width: unset;
            max-width: 680px;
        }
        /* Stack form grid on narrow panels */
        .form-section-card .grid.grid-cols-2 { grid-template-columns: 1fr 1fr; }

        /* Filter bar: allow wrapping */
        .filter-bar { flex-wrap: wrap !important; gap: 8px !important; }

        /* Table: hide low-priority columns on tablet */
        .data-table th:nth-child(6),
        .data-table td:nth-child(6) { display: none; }
    }

    /* ── Responsive tab header ── */
    .tab-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
        padding-top: 16px;
        padding-bottom: 0;
        border-bottom: 1px solid #f3f4f6;
        gap: 12px;
    }
    .tab-btn-wrap {
        display: flex;
        align-items: center;
        gap: 24px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        flex-shrink: 1;
        min-width: 0;
    }
    .tab-btn-wrap::-webkit-scrollbar { display: none; }

    /* ── Responsive filter bar ── */
    .filter-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    /* ── Table wrapper: always scrollable ── */
    .table-scroll-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .table-scroll-wrap .data-table {
        min-width: 620px;
    }

    /* ── Responsive panel footer: stack on very narrow ── */
    @media (max-width: 400px) {
        .panel-footer {
            flex-direction: column;
            align-items: stretch;
        }
        .panel-footer button {
            width: 100%;
            justify-content: center;
        }
        .panel-footer-spaced {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">Application</a>
    <span class="sep">›</span>
    <span class="current" id="breadcrumbCurrent">Leave Application</span>
</div>

{{-- Balance Cards --}}
@php
    $vlRem    = $vlBalance->remaining_balance ?? 0;
    $slRem    = $slBalance->remaining_balance ?? 0;
    $totalBal = $vlRem + $slRem;
@endphp
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    <div class="bal-card-dark">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium mb-2" style="color:rgba(255,255,255,0.75);">Total Leave Balance</p>
                <p class="text-4xl font-bold tracking-tight">{{ number_format($totalBal, 2) }}</p>
                <p class="text-xs mt-2" style="color:rgba(255,255,255,0.55);">Vacation + Sick Leave</p>
            </div>
            <div class="bal-icon bal-icon-dark">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>
    <div class="bal-card-light">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-2">Vacation Leave Balance</p>
                <p class="text-4xl font-bold text-gray-800">{{ number_format($vlRem, 2) }}</p>
                @if($vlBalance)
                <div class="flex items-center gap-2 mt-3">
                    <span class="text-xs text-gray-400">Accrued: <strong class="text-gray-600">{{ $vlBalance->total_accrued }}</strong></span>
                    <span class="text-gray-300">·</span>
                    <span class="text-xs text-gray-400">Used: <strong class="text-red-500">{{ $vlBalance->total_used }}</strong></span>
                </div>
                @else
                <p class="text-xs text-gray-400 mt-3">No balance record found</p>
                @endif
            </div>
            <div class="bal-icon bal-icon-green">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
    </div>
    <div class="bal-card-light">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-2">Sick Leave Balance</p>
                <p class="text-4xl font-bold text-gray-800">{{ number_format($slRem, 2) }}</p>
                @if($slBalance)
                <div class="flex items-center gap-2 mt-3">
                    <span class="text-xs text-gray-400">Accrued: <strong class="text-gray-600">{{ $slBalance->total_accrued }}</strong></span>
                    <span class="text-gray-300">·</span>
                    <span class="text-xs text-gray-400">Used: <strong class="text-red-500">{{ $slBalance->total_used }}</strong></span>
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

{{-- Tabs + Table Card --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    {{-- Tab Header (responsive) --}}
    <div class="tab-header-row">
        <div class="tab-btn-wrap">
            <button class="tab-btn active" id="tabLeave"    onclick="switchTab('leave')">Leave Application List</button>
            <button class="tab-btn"        id="tabMonetize" onclick="switchTab('monetize')">Monetization Request List</button>
        </div>
        <div class="pb-3 flex-shrink-0">
            <button id="applyBtn" onclick="openLeavePanel()"
                    class="flex items-center gap-2 px-5 py-2.5 text-sm text-white font-semibold rounded-lg transition"
                    style="background:#1a3a1a;" onmouseover="this.style.background='#2d5a1b'" onmouseout="this.style.background='#1a3a1a'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span id="applyBtnText">Apply for Leave</span>
            </button>
        </div>
    </div>

    {{-- Leave Table --}}
    <div id="panelLeave">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 sm:px-6 py-4" style="border-bottom:1px solid #f9fafb;">
            <div>
                <p class="font-bold text-gray-800 text-sm">Leave Application List</p>
                <p class="text-xs text-gray-400 mt-0.5">Click a row to view full details</p>
            </div>
            <div class="filter-bar">
                <div class="relative flex-1 sm:flex-none">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" placeholder="Search..." id="searchLeave" oninput="filterLeave()"
                           class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-green-700 w-full sm:w-52">
                </div>
                <div class="relative flex-1 sm:flex-none">
                    <select id="filterStatus" onchange="filterLeave()" class="appearance-none pl-3 pr-8 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-green-700 bg-white w-full">
                        <option value="">All Status</option>
                        <option value="PENDING">Pending</option>
                        <option value="RECEIVED">Received</option>
                        <option value="ON-PROCESS">On-Process</option>
                        <option value="APPROVED">Approved</option>
                        <option value="REJECTED">Rejected</option>
                        <option value="CANCELLED">Cancelled</option>
                    </select>
                    <svg class="w-4 h-4 text-gray-400 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="relative flex-1 sm:flex-none">
                    <select id="filterMonth" onchange="filterLeave()" class="appearance-none pl-3 pr-8 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-green-700 bg-white w-full">
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
        </div>
        <div class="table-scroll-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Employee ID</th><th>Name</th><th>Application Date</th>
                        <th>Duration</th><th>Start Date</th><th>End Date</th>
                        <th>Leave Type</th><th>Status</th><th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody id="leaveTbody">
                    @forelse($leaveApps as $app)
                    <tr class="leave-row"
                        data-status="{{ $app->status }}"
                        data-month="{{ $app->application_date->format('m') }}"
                        data-leave-id="{{ $app->leave_id }}"
                        onclick="openDetailPanel({{ $app->leave_id }}, event)">
                        <td class="font-bold font-mono">{{ $employee->formatted_employee_id }}</td>
                        <td class="font-medium">{{ $employee->last_name }}, {{ $employee->first_name }}</td>
                        <td class="text-gray-500">{{ $app->application_date->format('M d, Y') }}</td>
                        <td class="text-gray-500">{{ $app->no_of_days }} day(s)</td>
                        <td class="text-gray-500">{{ $app->start_date ? $app->start_date->format('M d, Y') : '—' }}</td>
                        <td class="text-gray-500">{{ $app->end_date   ? $app->end_date->format('M d, Y')   : '—' }}</td>
                        <td class="text-gray-500 text-xs">{{ $app->leaveType->type_name ?? '—' }}</td>
                        <td>
                            @php
                                $sc = match(strtoupper($app->status)) {
                                    'PENDING'    => 'badge-pending',
                                    'RECEIVED'   => 'badge-received',
                                    'ON-PROCESS' => 'badge-on-process',
                                    'APPROVED'   => 'badge-approved',
                                    'REJECTED'   => 'badge-rejected',
                                    'CANCELLED'  => 'badge-cancelled',
                                    default      => 'badge-pending',
                                };
                                $sl = match(strtoupper($app->status)) {
                                    'ON-PROCESS' => 'On Process',
                                    default      => ucfirst(strtolower($app->status)),
                                };
                            @endphp
                            <span class="status-badge {{ $sc }}">{{ $sl }}</span>
                        </td>
                        <td class="text-right pr-4" onclick="event.stopPropagation()">
                            <div class="action-menu">
                                <button class="action-menu-btn" onclick="toggleMenu(this,event)">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg>
                                </button>
                                <div class="action-dropdown">
                                    <button class="action-item" onclick="viewPdf({{ $app->leave_id }})">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        View / PDF
                                    </button>
                                    @if($app->status === 'PENDING')
                                    <button class="action-item danger" onclick="cancelApp({{ $app->leave_id }})">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Cancel
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-6 py-12 text-center text-gray-400 text-sm">No leave applications found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Monetization Table --}}
    <div id="panelMonetize" class="hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 sm:px-6 py-4" style="border-bottom:1px solid #f9fafb;">
            <div>
                <p class="font-bold text-gray-800 text-sm">Monetization Request List</p>
                <p class="text-xs text-gray-400 mt-0.5">Click a row to view details</p>
            </div>
            <div class="filter-bar">
                <div class="relative flex-1 sm:flex-none">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" placeholder="Search..." id="searchMonetize" oninput="filterMonetize()"
                           class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-green-700 w-full sm:w-52">
                </div>
                <div class="relative flex-1 sm:flex-none">
                    <select id="filterMonetizeStatus" onchange="filterMonetize()" class="appearance-none pl-3 pr-8 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-green-700 bg-white w-full">
                        <option value="">All Status</option>
                        <option value="PENDING">Pending</option>
                        <option value="RECEIVED">Received</option>
                        <option value="ON-PROCESS">On-Process</option>
                        <option value="APPROVED">Approved</option>
                        <option value="REJECTED">Denied</option>
                        <option value="CANCELLED">Cancelled</option>
                    </select>
                    <svg class="w-4 h-4 text-gray-400 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="relative flex-1 sm:flex-none">
                    <select id="filterMonetizeMonth" onchange="filterMonetize()" class="appearance-none pl-3 pr-8 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-green-700 bg-white w-full">
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
        </div>
        <div class="table-scroll-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Employee ID</th><th>Name</th><th>Application Date</th>
                        <th>Leave Type</th><th>Days</th><th>Est. Amount</th>
                        <th>Status</th><th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monetizationApps as $app)
                    @php
                        $dailyRate = $employee && $employee->salary ? round($employee->salary / 22, 2) : 0;
                        $estAmount = $dailyRate * $app->no_of_days;
                        $msc = match(strtoupper($app->status)) {
                            'PENDING'    => 'badge-pending',
                            'RECEIVED'   => 'badge-received',
                            'ON-PROCESS' => 'badge-on-process',
                            'APPROVED'   => 'badge-approved',
                            'REJECTED'   => 'badge-rejected',
                            'CANCELLED'  => 'badge-cancelled',
                            default      => 'badge-pending',
                        };
                        $msl = match(strtoupper($app->status)) {
                            'ON-PROCESS' => 'On Process',
                            default      => ucfirst(strtolower($app->status)),
                        };
                    @endphp
                    <tr class="monetize-row leave-row"
                        data-status="{{ $app->status }}"
                        data-month="{{ $app->application_date->format('m') }}"
                        data-leave-id="{{ $app->leave_id }}"
                        onclick="openDetailPanel({{ $app->leave_id }}, event)">
                        <td class="font-bold font-mono">{{ $employee->formatted_employee_id }}</td>
                        <td class="font-medium">{{ $employee->last_name }}, {{ $employee->first_name }}</td>
                        <td class="text-gray-500">{{ $app->application_date->format('M d, Y') }}</td>
                        <td class="text-xs text-gray-500">{{ $app->leaveType->type_name ?? '—' }}</td>
                        <td class="text-gray-500">{{ $app->no_of_days }}</td>
                        <td class="text-gray-700 font-medium">₱{{ number_format($estAmount, 2) }}</td>
                        <td><span class="status-badge {{ $msc }}">{{ $msl }}</span></td>
                        <td class="text-right pr-4" onclick="event.stopPropagation()">
                            <div class="action-menu">
                                <button class="action-menu-btn" onclick="toggleMenu(this,event)">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg>
                                </button>
                                <div class="action-dropdown">
                                    @if($app->status === 'PENDING')
                                    <button class="action-item danger" onclick="cancelApp({{ $app->leave_id }})">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Cancel
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">No monetization requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Overlay --}}
<div id="panelOverlay" onclick="closeAllPanels()"></div>

{{-- Detail Panel --}}
<div id="detailPanel" class="slide-panel">
    <div class="slide-panel-box">
        <div class="panel-header">
            <div>
                <h2 id="dpTitle">Leave Application Details</h2>
                <p id="dpSubtitle">Loading…</p>
            </div>
            <button class="panel-close" onclick="closeDetailPanel()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="panel-body" id="dpBody"></div>
        <div class="panel-footer panel-footer-spaced" id="dpFooter">
            <button class="btn-pdf" onclick="viewPdfFromPanel()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                View PDF
            </button>
            <div class="flex gap-3" id="dpActionBtns"></div>
        </div>
    </div>
</div>

{{-- Leave Application Panel --}}
<div id="leavePanel" class="slide-panel">
    <div class="slide-panel-box">
        <div class="panel-header">
            <div><h2>Application for Leave</h2><p>CS Form No. 6 — Please fill in all required fields</p></div>
            <button class="panel-close" onclick="closeLeavePanel()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="panel-body">
            <form id="leaveForm">
                @csrf
                <div class="form-section-card">
                    <div class="section-heading">
                        <div class="section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                        <p class="section-card-title">Personal Information</p>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                        <div><label class="field-label">First Name</label><input type="text" class="form-field" value="{{ $employee->first_name }}" disabled></div>
                        <div><label class="field-label">Office / Department</label><input type="text" class="form-field" value="{{ $employee->department->department_name ?? 'OPAG' }}" disabled></div>
                        <div><label class="field-label">Middle Name</label><input type="text" class="form-field" value="{{ $employee->middle_name ?? '' }}" disabled></div>
                        <div><label class="field-label">Date of Filing</label><input type="text" class="form-field" value="{{ now()->format('F d, Y') }}" disabled></div>
                        <div><label class="field-label">Last Name</label><input type="text" class="form-field" value="{{ $employee->last_name }}" disabled></div>
                        <div><label class="field-label">Position</label><input type="text" class="form-field" value="{{ $employee->position->position_name ?? '—' }}" disabled></div>
                        <div class="col-span-2"><label class="field-label">Salary</label><input type="text" class="form-field" value="₱{{ number_format($employee->salary, 2) }}" disabled></div>
                    </div>
                </div>

                <div class="form-section-card" style="margin-bottom:20px;">
                    <div class="section-heading">
                        <div class="section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                        <p class="section-card-title">Details of Application</p>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                        <div>
                            <label class="field-label">A. Type of Leave <span class="req">*</span></label>
                            <select id="f_leave_type_id" name="leave_type_id" class="form-field" onchange="onLeaveTypeChange(this)">
                                <option value="" disabled selected>Select Leave Type</option>
                                @foreach($leaveTypes as $lt)
                                <option value="{{ $lt->leave_type_id }}" data-accrual="{{ $lt->is_accrual_based }}" data-code="{{ $lt->type_code }}">{{ $lt->type_name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-red-400 mt-1 hidden" id="err_leave_type">Please select a leave type.</p>
                        </div>
                        <div>
                            <label class="field-label">C. Number of Working Days <span class="req">*</span></label>
                            <input type="text" id="f_no_days_display" class="form-field" placeholder="Auto-calculated from selected dates" disabled>
                            <p class="text-xs mt-1" id="balanceHint"></p>
                        </div>
                        <div>
                            <label class="field-label">B. Details of Leave</label>
                            <div id="detailsOfLeaveWrapper">
                                <p class="text-xs text-gray-400 mt-1" id="detailsHint">Select a leave type to see available options.</p>
                            </div>
                        </div>
                        <div>
                            <label class="field-label">D. Commutation</label>
                            <select id="f_commutation" name="commutation" class="form-field">
                                <option value="NOT_REQUESTED">Not Requested</option>
                                <option value="REQUESTED">Requested</option>
                            </select>
                        </div>

                        {{-- Compact Calendar Picker --}}
                        <div class="col-span-2">
                            <label class="field-label">Leave Dates <span class="req">*</span></label>
                            <div class="cal-wrap" id="calWrap">
                                <button type="button" class="cal-trigger" id="calTrigger" onclick="toggleCalendar(event)">
                                    <span class="cal-trigger-text placeholder" id="calTriggerText">Click to select leave dates…</span>
                                    <svg class="w-4 h-4 flex-shrink-0" style="color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                <div class="cal-popup" id="calPopup" style="display:none;">
                                    <div class="cal-header">
                                        <button type="button" class="cal-nav" onclick="calNav(-1)"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
                                        <span class="cal-month-label" id="calMonthLabel"></span>
                                        <button type="button" class="cal-nav" onclick="calNav(1)"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
                                    </div>
                                    <div class="cal-weekdays">
                                        <div class="cal-wd">Su</div><div class="cal-wd">Mo</div><div class="cal-wd">Tu</div>
                                        <div class="cal-wd">We</div><div class="cal-wd">Th</div><div class="cal-wd">Fr</div><div class="cal-wd">Sa</div>
                                    </div>
                                    <div class="cal-days" id="calDays"></div>
                                    <div class="cal-footer">
                                        <p class="cal-selected-count"><strong id="calCount">0</strong> date(s)</p>
                                        <div class="flex gap-2 items-center">
                                            <button type="button" class="cal-clear-btn" onclick="calClearAll()">Clear</button>
                                            <button type="button" class="cal-done-btn" onclick="calDone()">Done ✓</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="cal-chips-area" id="calChipsArea"></div>
                                <div id="calHiddenInputs"></div>
                            </div>
                            <p class="text-xs text-red-400 mt-1 hidden" id="err_dates">Please select at least one leave date.</p>
                            <p class="text-xs text-gray-400 mt-1">Weekends are disabled. You can pick non-consecutive dates freely.</p>
                        </div>

                        <div class="col-span-2">
                            <label class="field-label">Reason / Remarks</label>
                            <textarea id="f_reason" name="reason" rows="2" class="form-field" placeholder="Optional remarks..."></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer" id="leavePanelFooter">
            <button onclick="closeLeavePanel()" class="px-6 py-2.5 text-sm font-semibold border-2 border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
            <button onclick="submitLeave()" id="submitLeaveBtn" class="px-8 py-2.5 text-sm font-semibold text-white rounded-xl transition" style="background:#1a3a1a;" onmouseover="this.style.background='#2d5a1b'" onmouseout="this.style.background='#1a3a1a'">Confirm Application</button>
        </div>
    </div>
</div>

{{-- Monetization Panel --}}
<div id="monetizePanel" class="slide-panel">
    <div class="slide-panel-box">
        <div class="panel-header">
            <div><h2>Request Monetization</h2><p>Convert leave days to cash</p></div>
            <button class="panel-close" onclick="closeMonetizePanel()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="panel-body">
            <form id="monetizeForm">
                @csrf
                <div class="form-section-card" style="margin-bottom:20px;">
                    <div class="section-heading">
                        <div class="section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                        <p class="section-card-title">Monetization Details</p>
                    </div>
                    <div class="grid grid-cols-1 gap-y-4">
                        <div>
                            <label class="field-label">Leave Type <span class="req">*</span></label>
                            <select id="fm_leave_type_id" name="leave_type_id" class="form-field" onchange="updateMonetizeBalance(this)">
                                <option value="" disabled selected>Select Leave Type</option>
                                @foreach($leaveTypes->where('is_accrual_based',1) as $lt)
                                <option value="{{ $lt->leave_type_id }}" data-code="{{ $lt->type_code }}">{{ $lt->type_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="monetizeBalanceBox" class="hidden rounded-xl p-4" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                            <p class="text-xs font-semibold text-green-700 mb-1">AVAILABLE BALANCE</p>
                            <p class="text-2xl font-bold text-green-800" id="monetizeBalanceVal">0</p>
                            <p class="text-xs text-green-600 mt-1">days remaining</p>
                        </div>
                        <div>
                            <label class="field-label">Number of Days to Monetize <span class="req">*</span></label>
                            <input type="number" id="fm_no_of_days" name="no_of_days" class="form-field" min="1" max="10" placeholder="Max 10 days" oninput="calcMonetizeAmount()">
                            <p class="text-xs text-gray-400 mt-1">Daily rate: <strong id="dailyRateDisplay">₱{{ number_format($dailyRate, 2) }}</strong> &nbsp;·&nbsp; Salary × Days × 0.0481927</p>
                            <div id="monetizeLimitWarn" class="hidden mt-2 rounded-lg px-3 py-2.5 flex items-start gap-2" style="background:#fff7ed;border:1px solid #fed7aa;">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs" style="color:#9a3412;">Monetization is limited to <strong>10 days</strong>.</p>
                            </div>
                        </div>
                        <div id="monetizeAmountBox" class="rounded-xl p-4 hidden" style="background:#f3f4f6;">
                            <p class="text-xs font-semibold text-gray-500 mb-1">ESTIMATED AMOUNT</p>
                            <p class="text-2xl font-bold text-gray-800" id="monetizeAmountVal">₱0.00</p>
                            <p class="text-xs text-gray-400 mt-1">Subject to approval and final computation</p>
                        </div>
                        <div>
                            <label class="field-label">Reason / Remarks</label>
                            <textarea id="fm_reason" name="reason" rows="2" class="form-field" placeholder="Optional reason..."></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer">
            <button onclick="closeMonetizePanel()" class="px-6 py-2.5 text-sm font-semibold border-2 border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
            <button onclick="submitMonetize()" id="submitMonetizeBtn" class="px-8 py-2.5 text-sm font-semibold text-white rounded-xl transition" style="background:#1a3a1a;" onmouseover="this.style.background='#2d5a1b'" onmouseout="this.style.background='#1a3a1a'">Submit Request</button>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div id="cancelModal" style="position:fixed;inset:0;z-index:200;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,0.45);backdrop-filter:blur(3px);opacity:0;transition:opacity 0.2s;">
    <div id="cancelModalCard" style="background:#fff;border-radius:20px;padding:32px;width:420px;max-width:94vw;box-shadow:0 24px 64px rgba(0,0,0,0.2);transform:scale(0.93);transition:transform 0.25s cubic-bezier(0.34,1.56,0.64,1);">
        <div class="flex items-center gap-4 mb-5">
            <div style="width:52px;height:52px;border-radius:16px;background:#fff7ed;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:26px;height:26px;color:#ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-800" style="font-size:16px;">Cancel Application?</h3>
                <p class="text-sm text-gray-500 mt-0.5">This action cannot be undone.</p>
            </div>
        </div>
        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:14px 16px;margin-bottom:20px;">
            <p class="text-sm font-medium" style="color:#9a3412;">Once cancelled, this leave application will be permanently marked as <strong>Cancelled</strong>.</p>
        </div>
        <div class="flex gap-3 justify-end flex-wrap">
            <button onclick="closeCancelModal()" class="px-6 py-2.5 text-sm font-semibold border-2 border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Keep Application</button>
            <button onclick="confirmCancelApp()" id="confirmCancelBtn" class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl flex items-center gap-2" style="background:#dc2626;" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Yes, Cancel It
            </button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="toast">
    <div id="toastIcon" class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#dcfce7;"></div>
    <div>
        <p class="text-sm font-bold text-gray-800" id="toastTitle"></p>
        <p class="text-xs text-gray-500 mt-0.5" id="toastMsg"></p>
    </div>
</div>

<script>
/* ════════════════════════════════════════════════════════
   SERVER DATA
════════════════════════════════════════════════════════ */
const CREDIT_BALANCES = @json($creditBalancesJson);
const DAILY_RATE      = {{ $dailyRate }};
const CSRF            = "{{ csrf_token() }}";
const LEAVE_URL       = "{{ route('leave.store') }}";
const MONETIZE_URL    = "{{ route('leave.monetize') }}";
const CANCEL_URL      = "{{ url('application/leave') }}";
const PDF_URL         = "{{ url('application/leave') }}";

const LEAVE_DATA = {!! json_encode($leaveApps->keyBy('leave_id')->map(function($a) {
    return [
        'leave_id'         => $a->leave_id,
        'leave_type'       => optional($a->leaveType)->type_name ?? '—',
        'application_date' => $a->application_date ? $a->application_date->format('M d, Y') : '—',
        'start_date'       => $a->start_date ? $a->start_date->format('M d, Y') : '—',
        'end_date'         => $a->end_date   ? $a->end_date->format('M d, Y')   : '—',
        'no_of_days'       => $a->no_of_days,
        'details_of_leave' => $a->details_of_leave ?? '—',
        'commutation'      => $a->commutation ?? '—',
        'reason'           => $a->reason ?? '',
        'status'           => $a->status,
        'is_monetization'  => $a->is_monetization,
        'reject_reason'    => $a->reject_reason ?? '',
    ];
})) !!};

const MONETIZE_DATA = {!! json_encode($monetizationApps->keyBy('leave_id')->map(function($a) use ($employee) {
    $amt = ($employee->salary ?? 0) * $a->no_of_days * 0.0481927;
    return [
        'leave_id'         => $a->leave_id,
        'leave_type'       => optional($a->leaveType)->type_name ?? '—',
        'application_date' => $a->application_date ? $a->application_date->format('M d, Y') : '—',
        'no_of_days'       => $a->no_of_days,
        'est_amount'       => '₱' . number_format($amt, 2),
        'status'           => $a->status,
        'reason'           => $a->reason ?? '',
        'reject_reason'    => $a->reject_reason ?? '',
    ];
})) !!};

const ALL_DATA = {...LEAVE_DATA, ...MONETIZE_DATA};

/* ════════════════════════════════════════════════════════
   STATE
════════════════════════════════════════════════════════ */
let activePanelLeaveId = null;
let pendingCancelId    = null;

/* ════════════════════════════════════════════════════════
   CALENDAR PICKER
════════════════════════════════════════════════════════ */
let calSelectedDates = new Set();
let calYear, calMonth, calIsOpen = false;
const MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];

function calInit() {
    const now = new Date();
    calYear = now.getFullYear(); calMonth = now.getMonth();
    calRender();
}
function calRender() {
    document.getElementById('calMonthLabel').textContent = MONTH_NAMES[calMonth] + ' ' + calYear;
    const grid = document.getElementById('calDays');
    const today = new Date(); today.setHours(0,0,0,0);
    const todayStr = toYMD(today);
    const firstDay = new Date(calYear, calMonth, 1).getDay();
    const daysInMonth = new Date(calYear, calMonth+1, 0).getDate();
    const prevDays = new Date(calYear, calMonth, 0).getDate();
    grid.innerHTML = '';
    for (let i=0; i<firstDay; i++) grid.appendChild(makeBtn(prevDays-firstDay+1+i,'cal-day cal-other'));
    for (let day=1; day<=daysInMonth; day++) {
        const date=new Date(calYear,calMonth,day), dateStr=toYMD(date), dow=date.getDay();
        const isPast=date<today&&dateStr!==todayStr, isWknd=dow===0||dow===6;
        let cls='cal-day';
        if(dateStr===todayStr) cls+=' cal-today';
        if(isWknd)             cls+=' cal-weekend';
        if(isPast)             cls+=' cal-disabled';
        if(calSelectedDates.has(dateStr)) cls+=' cal-selected';
        const btn=makeBtn(day,cls);
        if(!isWknd&&!isPast) btn.onclick=()=>calToggleDate(dateStr,btn);
        grid.appendChild(btn);
    }
    const total=firstDay+daysInMonth, rem=total%7===0?0:7-(total%7);
    for(let i=1;i<=rem;i++) grid.appendChild(makeBtn(i,'cal-day cal-other'));
    document.getElementById('calCount').textContent=calSelectedDates.size;
}
function makeBtn(text,cls){const b=document.createElement('button');b.type='button';b.className=cls;b.textContent=text;return b;}
function calToggleDate(ds, btn) {
    if (calSelectedDates.has(ds)) {
        calSelectedDates.delete(ds);
        btn.classList.remove('cal-selected');
    } else {
        if (isDuplicateLeaveDate(ds)) {
            showToast('Duplicate Date', `${formatDateDisplay(ds)} is already covered by an existing leave application.`, 'error');
            return;
        }
        calSelectedDates.add(ds);
        btn.classList.add('cal-selected');
    }
    document.getElementById('calCount').textContent = calSelectedDates.size;
}
function isDuplicateLeaveDate(dateStr) {
    let found = false;
    document.querySelectorAll('#leaveTbody .leave-row').forEach(row => {
        const status = row.dataset.status;
        if (status === 'CANCELLED' || status === 'REJECTED') return;
        const data = LEAVE_DATA[row.dataset.leaveId];
        if (!data) return;
        const start = data.start_date !== '—' ? new Date(data.start_date) : null;
        const end   = data.end_date   !== '—' ? new Date(data.end_date)   : null;
        if (start && end) {
            const [y,m,d] = dateStr.split('-');
            const picked = new Date(+y, +m-1, +d);
            if (picked >= start && picked <= end) found = true;
        }
    });
    return found;
}
function calNav(dir){calMonth+=dir;if(calMonth>11){calMonth=0;calYear++;}if(calMonth<0){calMonth=11;calYear--;}calRender();}
function calClearAll(){calSelectedDates.clear();calRender();renderCalChips();updateCalDisplay();}
function calDone(){renderCalChips();updateCalDisplay();closeCalendar();updateDaysFromCalendar();}
function renderCalChips(){
    const area=document.getElementById('calChipsArea');area.innerHTML='';
    [...calSelectedDates].sort().forEach(ds=>{
        const c=document.createElement('div');c.className='cal-chip';
        c.innerHTML=`<span>${formatDateDisplay(ds)}</span><span class="cal-chip-x" onclick="calRemoveDate('${ds}')">×</span>`;
        area.appendChild(c);
    });
}
function calRemoveDate(ds){calSelectedDates.delete(ds);renderCalChips();updateCalDisplay();calRender();updateDaysFromCalendar();}
function updateCalDisplay(){
    const t=document.getElementById('calTriggerText'),n=calSelectedDates.size;
    t.textContent=n===0?'Click to select leave dates…':(n===1?'1 date selected':`${n} dates selected`);
    t.classList.toggle('placeholder',n===0);
}
function updateDaysFromCalendar(){
    const n=calSelectedDates.size;
    document.getElementById('f_no_days_display').value=n>0?n+' working day(s)':'';
    const c=document.getElementById('calHiddenInputs');c.innerHTML='';
    const sorted=[...calSelectedDates].sort();
    sorted.forEach(ds=>addHidden(c,'leave_dates[]',ds));
    if(sorted.length>0){addHidden(c,'start_date',sorted[0]);addHidden(c,'end_date',sorted[sorted.length-1]);}
}
function addHidden(p,name,val){const i=document.createElement('input');i.type='hidden';i.name=name;i.value=val;p.appendChild(i);}

function toggleCalendar(e){if(e)e.stopPropagation();calIsOpen?closeCalendar():openCalendar();}
function openCalendar(){
    document.getElementById('calPopup').style.display='block';
    document.getElementById('calTrigger').classList.add('open');
    document.getElementById('leavePanelFooter').classList.add('cal-active');
    calIsOpen=true;
    calRender();
}
function closeCalendar(){
    document.getElementById('calPopup').style.display='none';
    document.getElementById('calTrigger').classList.remove('open');
    document.getElementById('leavePanelFooter').classList.remove('cal-active');
    calIsOpen=false;
}
document.addEventListener('click',e=>{if(!document.getElementById('calWrap')?.contains(e.target))closeCalendar();});
function toYMD(d){return`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;}
function formatDateDisplay(ds){const[y,m,d]=ds.split('-');return new Date(+y,+m-1,+d).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});}

/* ════════════════════════════════════════════════════════
   DETAIL PANEL
════════════════════════════════════════════════════════ */
function openDetailPanel(leaveId,e){
    if(e)e.stopPropagation();
    const d=ALL_DATA[leaveId];if(!d)return;
    activePanelLeaveId=leaveId;
    const isMonetize=!!MONETIZE_DATA[leaveId];
    document.getElementById('dpTitle').textContent=isMonetize?'Monetization Request Details':'Leave Application Details';
    document.getElementById('dpSubtitle').textContent=`${d.leave_type} · Filed: ${d.application_date}`;
    const SC={PENDING:'#fef9c3|#854d0e',RECEIVED:'#dbeafe|#1e40af','ON-PROCESS':'#ede9fe|#5b21b6',APPROVED:'#dcfce7|#14532d',REJECTED:'#fee2e2|#991b1b',CANCELLED:'#f3f4f6|#6b7280'};
    const[sBg,sC]=(SC[d.status]||'#f3f4f6|#6b7280').split('|');
    const sl=d.status==='ON-PROCESS'?'On Process':d.status.charAt(0)+d.status.slice(1).toLowerCase();
    let html=`<div class="dp-card"><div class="dp-section-heading"><div class="dp-section-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div><p class="dp-section-title">${isMonetize?'Monetization':'Application'} Details</p></div><div class="dp-grid">
    <div class="dp-field"><label>Leave Type</label><p>${d.leave_type}</p></div>
    <div class="dp-field"><label>Application Date</label><p>${d.application_date}</p></div>`;
    if(!isMonetize){html+=`<div class="dp-field"><label>Start Date</label><p>${d.start_date}</p></div><div class="dp-field"><label>End Date</label><p>${d.end_date}</p></div><div class="dp-field"><label>Number of Days</label><p>${d.no_of_days} working day(s)</p></div><div class="dp-field"><label>Details of Leave</label><p>${d.details_of_leave}</p></div><div class="dp-field"><label>Commutation</label><p>${d.commutation==='REQUESTED'?'Requested':'Not Requested'}</p></div>`;}
    else{html+=`<div class="dp-field"><label>Days to Monetize</label><p>${d.no_of_days} day(s)</p></div><div class="dp-field"><label>Estimated Amount</label><p style="font-weight:700;color:#15803d;">${d.est_amount}</p></div>`;}
    html+=`<div class="dp-field"><label>Status</label><p><span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:${sBg};color:${sC};">● ${sl}</span></p></div>`;
    if(d.reason)       html+=`<div class="dp-field span2"><label>Reason / Remarks</label><p>${d.reason}</p></div>`;
    if(d.reject_reason)html+=`<div class="dp-field span2"><label>Rejection Reason</label><p style="color:#dc2626;">${d.reject_reason}</p></div>`;
    html+=`</div></div><div style="height:8px;"></div>`;
    document.getElementById('dpBody').innerHTML=html;
    const labels={APPROVED:'Application Approved',REJECTED:'Application Rejected',CANCELLED:'Application Cancelled'};
    document.getElementById('dpActionBtns').innerHTML=d.status==='PENDING'
        ?`<button class="btn-cancel-action" onclick="cancelApp(${leaveId})"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>Cancel Application</button>`
        :`<span style="font-size:12px;color:#6b7280;font-weight:600;">${labels[d.status]||'Status: '+sl}</span>`;
    document.getElementById('detailPanel').classList.add('open');
    document.getElementById('panelOverlay').classList.add('show');
    document.body.style.overflow='hidden';
}
function closeDetailPanel(){document.getElementById('detailPanel').classList.remove('open');hidePanelOverlayIfNoneOpen();document.body.style.overflow='';activePanelLeaveId=null;}
function viewPdfFromPanel(){if(activePanelLeaveId)viewPdf(activePanelLeaveId);}

/* ════════════════════════════════════════════════════════
   PANELS
════════════════════════════════════════════════════════ */
function openLeavePanel(){resetLeaveForm();closeDetailPanel();document.getElementById('leavePanel').classList.add('open');document.getElementById('panelOverlay').classList.add('show');document.body.style.overflow='hidden';calInit();}
function closeLeavePanel(){closeCalendar();document.getElementById('leavePanel').classList.remove('open');hidePanelOverlayIfNoneOpen();document.body.style.overflow='';}
function openMonetizePanel(){closeDetailPanel();document.getElementById('monetizePanel').classList.add('open');document.getElementById('panelOverlay').classList.add('show');document.body.style.overflow='hidden';}
function closeMonetizePanel(){document.getElementById('monetizePanel').classList.remove('open');hidePanelOverlayIfNoneOpen();document.body.style.overflow='';}
function closeAllPanels(){closeCalendar();['leavePanel','monetizePanel','detailPanel'].forEach(id=>document.getElementById(id).classList.remove('open'));document.getElementById('panelOverlay').classList.remove('show');document.body.style.overflow='';activePanelLeaveId=null;}
function hidePanelOverlayIfNoneOpen(){if(!document.querySelector('.slide-panel.open'))document.getElementById('panelOverlay').classList.remove('show');}
function resetLeaveForm(){
    document.getElementById('leaveForm').reset();
    document.getElementById('f_no_days_display').value='';document.getElementById('balanceHint').textContent='';
    clearDetailsWrapper();calSelectedDates.clear();
    document.getElementById('calChipsArea').innerHTML='';document.getElementById('calHiddenInputs').innerHTML='';
    const t=document.getElementById('calTriggerText');t.textContent='Click to select leave dates…';t.classList.add('placeholder');
    closeCalendar();
}

/* ════════════════════════════════════════════════════════
   TAB SWITCH
════════════════════════════════════════════════════════ */
function switchTab(tab){
    const isLeave=tab==='leave';
    document.getElementById('tabLeave').classList.toggle('active',isLeave);
    document.getElementById('tabMonetize').classList.toggle('active',!isLeave);
    document.getElementById('panelLeave').classList.toggle('hidden',!isLeave);
    document.getElementById('panelMonetize').classList.toggle('hidden',isLeave);
    document.getElementById('breadcrumbCurrent').textContent=isLeave?'Leave Application':'Monetization Request List';
    document.getElementById('applyBtnText').textContent=isLeave?'Apply for Leave':'Request Monetization';
    document.getElementById('applyBtn').onclick=isLeave?openLeavePanel:openMonetizePanel;
}

/* ════════════════════════════════════════════════════════
   LEAVE TYPE → DETAILS OF LEAVE
════════════════════════════════════════════════════════ */
const DETAILS_CONFIG = {
    vacation:         {type:'radio',options:[{value:'Within the Philippines',label:'Within the Philippines',specify:true,placeholder:'Specify place/municipality…'},{value:'Abroad',label:'Abroad',specify:true,placeholder:'Specify destination…'}]},
    special_privilege:{type:'radio',options:[{value:'Within the Philippines',label:'Within the Philippines',specify:true,placeholder:'Specify place/municipality…'},{value:'Abroad',label:'Abroad',specify:true,placeholder:'Specify destination…'}]},
    mandatory:        {type:'radio',options:[{value:'Within the Philippines',label:'Within the Philippines',specify:true,placeholder:'Specify place/municipality…'},{value:'Abroad',label:'Abroad',specify:true,placeholder:'Specify destination…'}]},
    sick:             {type:'radio',options:[{value:'In Hospital',label:'In Hospital',specify:true,placeholder:'Specify illness…'},{value:'Out Patient',label:'Out Patient',specify:true,placeholder:'Specify illness…'}]},
    study:            {type:'radio',options:[{value:"Completion of Master's Degree",label:"Completion of Master's Degree",specify:false},{value:'BAR/Board Examination Review',label:'BAR/Board Examination Review',specify:false}]},
    women:            {type:'text',placeholder:'Specify details…'},
    none:             {type:'none'},
};
function resolveDetailsKey(code,name){
    const c=(code||'').toLowerCase(),n=(name||'').toLowerCase();
    if(c.includes('vl')||n.includes('vacation'))return 'vacation';
    if(c.includes('spl')||n.includes('special privilege'))return 'special_privilege';
    if(c.includes('mfl')||n.includes('mandatory')||n.includes('forced'))return 'mandatory';
    if((c==='sl'||c.startsWith('sl_')||c.startsWith('sl-'))||n.includes('sick'))return 'sick';
    if(c.includes('stl')||n.includes('study'))return 'study';
    if(n.includes('women')||c.includes('slbw'))return 'women';
    return 'none';
}
function clearDetailsWrapper(){
    document.getElementById('detailsOfLeaveWrapper').querySelectorAll('.dynamic-detail').forEach(el=>el.remove());
    const h=document.getElementById('detailsHint');
    if(h){h.textContent='Select a leave type to see available options.';h.className='text-xs text-gray-400 mt-1';}
}
function onLeaveTypeChange(sel){
    const opt=sel.options[sel.selectedIndex],ltId=parseInt(sel.value);
    const isAccrual=opt.dataset.accrual==='1',code=opt.dataset.code||'',name=opt.text||'';
    const hint=document.getElementById('balanceHint');
    if(isAccrual&&CREDIT_BALANCES[ltId]){hint.textContent=`Available balance: ${CREDIT_BALANCES[ltId].remaining_balance} days`;hint.className='text-xs text-green-600 mt-1';}
    else if(isAccrual){hint.textContent='No balance record found for this year.';hint.className='text-xs text-red-400 mt-1';}
    else{hint.textContent='This leave type does not require a balance.';hint.className='text-xs text-gray-400 mt-1';}
    clearDetailsWrapper();
    const wrapper=document.getElementById('detailsOfLeaveWrapper'),dh=document.getElementById('detailsHint');
    const cfg=DETAILS_CONFIG[resolveDetailsKey(code,name)];
    if(!cfg||cfg.type==='none'){
        if(dh)dh.textContent='No additional details required for this leave type.';
        const inp=document.createElement('input');inp.type='hidden';inp.name='details_of_leave';inp.value='';inp.className='dynamic-detail';wrapper.appendChild(inp);
    }else if(cfg.type==='text'){
        if(dh)dh.textContent='';
        const inp=document.createElement('input');inp.type='text';inp.name='details_of_leave';inp.className='form-field dynamic-detail';inp.placeholder=cfg.placeholder||'Specify details…';wrapper.appendChild(inp);
    }else if(cfg.type==='radio'){
        if(dh)dh.textContent='';
        const rw=document.createElement('div');rw.className='dynamic-detail flex flex-col gap-1 mt-1';
        cfg.options.forEach((o,i)=>{
            const row=document.createElement('div');row.className='flex flex-col';
            const lbl=document.createElement('label');lbl.className='flex items-center gap-2.5 cursor-pointer text-sm text-gray-700 py-1';
            lbl.innerHTML=`<input type="radio" name="details_of_leave" value="${o.value}" id="detail_${i}" class="w-3.5 h-3.5 accent-green-700 cursor-pointer"><span>${o.label}${o.specify?' <span class="text-gray-400 text-xs">(specify below)</span>':''}</span>`;
            row.appendChild(lbl);
            if(o.specify){
                const sw=document.createElement('div');sw.className='dynamic-detail specify-wrap mt-2 ml-6';sw.dataset.forValue=o.value;sw.style.display='none';
                const si=document.createElement('input');si.type='text';si.name='details_specify';si.className='form-field';si.placeholder=o.placeholder||'Specify…';si.style.fontSize='12px';si.style.padding='7px 10px';
                sw.appendChild(si);row.appendChild(sw);
                lbl.querySelector('input').addEventListener('change',()=>{wrapper.querySelectorAll('.specify-wrap').forEach(w=>{w.style.display='none';w.querySelector('input').value='';});sw.style.display='block';si.focus();});
            }else{
                lbl.querySelector('input').addEventListener('change',()=>{wrapper.querySelectorAll('.specify-wrap').forEach(w=>{w.style.display='none';w.querySelector('input').value='';});});
            }
            rw.appendChild(row);
        });
        wrapper.appendChild(rw);
    }
}

/* ════════════════════════════════════════════════════════
   MONETIZE
════════════════════════════════════════════════════════ */
function updateMonetizeBalance(sel){
    const ltId=parseInt(sel.value),box=document.getElementById('monetizeBalanceBox');
    if(CREDIT_BALANCES[ltId]){document.getElementById('monetizeBalanceVal').textContent=CREDIT_BALANCES[ltId].remaining_balance;box.classList.remove('hidden');}
    else box.classList.add('hidden');
    calcMonetizeAmount();
}
function calcMonetizeAmount(){
    const inp=document.getElementById('fm_no_of_days');let days=parseFloat(inp.value)||0;
    const warn=document.getElementById('monetizeLimitWarn');
    if(days>10){warn.classList.remove('hidden');inp.value=10;days=10;}else warn.classList.add('hidden');
    const box=document.getElementById('monetizeAmountBox');
    if(days>0){const amt={{ $employee->salary ?? 0 }}*days*0.0481927;document.getElementById('monetizeAmountVal').textContent='₱'+amt.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});box.classList.remove('hidden');}
    else box.classList.add('hidden');
}

/* ════════════════════════════════════════════════════════
   SUBMIT LEAVE
════════════════════════════════════════════════════════ */
function submitLeave(){
    const ltId=document.getElementById('f_leave_type_id').value;let ok=true;
    document.getElementById('err_leave_type').classList.toggle('hidden',!!ltId);
    document.getElementById('err_dates').classList.toggle('hidden',calSelectedDates.size>0);
    if(!ltId||calSelectedDates.size===0)ok=false;
    if(!ok)return;
    const btn=document.getElementById('submitLeaveBtn');btn.textContent='Submitting…';btn.disabled=true;
    const sorted=[...calSelectedDates].sort(),body=new FormData();
    body.append('leave_type_id',ltId);
    sorted.forEach(ds=>body.append('leave_dates[]',ds));
    body.append('start_date',sorted[0]);body.append('end_date',sorted[sorted.length-1]);
    body.append('no_of_days',calSelectedDates.size);
    body.append('details_of_leave',(()=>{
        const checked=document.querySelector('input[name="details_of_leave"]:checked');
        if(checked){const sw=document.querySelector(`.specify-wrap[data-for-value="${CSS.escape(checked.value)}"]`);if(sw&&sw.style.display!=='none'){const sv=(sw.querySelector('input')?.value||'').trim();if(sv)return`${checked.value}: ${sv}`;}return checked.value;}
        const inp=document.querySelector('#detailsOfLeaveWrapper [name="details_of_leave"]');return inp?inp.value:'';
    })());
    body.append('commutation',document.getElementById('f_commutation').value);
    body.append('reason',document.getElementById('f_reason').value);
    body.append('_token',CSRF);
    fetch(LEAVE_URL,{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest'},body})
    .then(r=>r.json()).then(data=>{
        if(data.success){closeLeavePanel();showToast('Application Submitted!','Your leave application is now pending approval.','success');setTimeout(()=>location.reload(),1800);}
        else showToast('Error',data.message||'Something went wrong.','error');
    }).catch(()=>showToast('Network Error','Please check your connection.','error'))
    .finally(()=>{btn.textContent='Confirm Application';btn.disabled=false;});
}

/* ════════════════════════════════════════════════════════
   SUBMIT MONETIZE
════════════════════════════════════════════════════════ */
function submitMonetize(){
    const ltId=document.getElementById('fm_leave_type_id').value,days=document.getElementById('fm_no_of_days').value;
    if(!ltId||!days){showToast('Missing Fields','Please select a leave type and number of days.','error');return;}
    const btn=document.getElementById('submitMonetizeBtn');btn.textContent='Submitting…';btn.disabled=true;
    const body=new FormData();body.append('leave_type_id',ltId);body.append('no_of_days',days);
    body.append('reason',document.getElementById('fm_reason').value);body.append('_token',CSRF);
    fetch(MONETIZE_URL,{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest'},body})
    .then(r=>r.json()).then(data=>{
        if(data.success){closeMonetizePanel();showToast('Request Submitted!','Your monetization request is now pending approval.','success');setTimeout(()=>location.reload(),1800);}
        else showToast('Error',data.message||'Something went wrong.','error');
    }).catch(()=>showToast('Network Error','Please check your connection.','error'))
    .finally(()=>{btn.textContent='Submit Request';btn.disabled=false;});
}

/* ════════════════════════════════════════════════════════
   CANCEL MODAL
════════════════════════════════════════════════════════ */
function cancelApp(id){
    pendingCancelId=id;
    const m=document.getElementById('cancelModal'),c=document.getElementById('cancelModalCard');
    m.style.display='flex';c.style.transform='scale(0.93)';
    requestAnimationFrame(()=>requestAnimationFrame(()=>{m.style.opacity='1';c.style.transform='scale(1)';}));
    document.body.style.overflow='hidden';
}
function closeCancelModal(){
    const m=document.getElementById('cancelModal');m.style.opacity='0';document.getElementById('cancelModalCard').style.transform='scale(0.93)';
    setTimeout(()=>{m.style.display='none';},200);document.body.style.overflow='';pendingCancelId=null;
}
function confirmCancelApp(){
    if(!pendingCancelId)return;
    const id=pendingCancelId,btn=document.getElementById('confirmCancelBtn');
    btn.innerHTML='<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Cancelling…';
    btn.disabled=true;closeCancelModal();closeDetailPanel();
    fetch(CANCEL_URL+'/'+id+'/cancel',{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},body:new FormData()})
    .then(r=>r.json()).then(data=>{
        if(data.success){showToast('Application Cancelled','Your leave application has been successfully cancelled.','warning');setTimeout(()=>location.reload(),1800);}
        else showToast('Error',data.message||'Could not cancel application.','error');
    }).catch(()=>showToast('Network Error','Please check your connection.','error'))
    .finally(()=>{btn.innerHTML='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Yes, Cancel It';btn.disabled=false;});
}

/* ════════════════════════════════════════════════════════
   VIEW PDF
════════════════════════════════════════════════════════ */
function viewPdf(id){window.open(PDF_URL+'/'+id+'/pdf','_blank');}

/* ════════════════════════════════════════════════════════
   ACTION MENUS
════════════════════════════════════════════════════════ */
function toggleMenu(btn,e){
    if(e)e.stopPropagation();
    const dd=btn.nextElementSibling,isOpen=dd.classList.contains('open');
    document.querySelectorAll('.action-dropdown.open').forEach(d=>d.classList.remove('open'));
    if(!isOpen){dd.classList.add('open');const rect=btn.getBoundingClientRect();dd.style.top=(rect.bottom+4)+'px';dd.style.left=(rect.right-160)+'px';dd.style.transform=window.innerHeight-rect.bottom<120?`translateY(calc(-100% - ${rect.height+8}px))`:''; }
}
document.addEventListener('click',()=>document.querySelectorAll('.action-dropdown.open').forEach(d=>d.classList.remove('open')));
window.addEventListener('scroll',()=>document.querySelectorAll('.action-dropdown.open').forEach(d=>d.classList.remove('open')),true);

/* ════════════════════════════════════════════════════════
   FILTERS
════════════════════════════════════════════════════════ */
function filterLeave(){
    const q=document.getElementById('searchLeave').value.toLowerCase(),st=document.getElementById('filterStatus').value,mo=document.getElementById('filterMonth').value;
    document.querySelectorAll('#leaveTbody .leave-row').forEach(r=>{r.style.display=((!q||r.textContent.toLowerCase().includes(q))&&(!st||r.dataset.status===st)&&(!mo||r.dataset.month===mo))?'':'none';});
}
function filterMonetize(){
    const q=document.getElementById('searchMonetize').value.toLowerCase(),st=document.getElementById('filterMonetizeStatus').value,mo=document.getElementById('filterMonetizeMonth').value;
    document.querySelectorAll('.monetize-row').forEach(r=>{r.style.display=((!q||r.textContent.toLowerCase().includes(q))&&(!st||r.dataset.status===st)&&(!mo||r.dataset.month===mo))?'':'none';});
}

/* ════════════════════════════════════════════════════════
   TOAST
════════════════════════════════════════════════════════ */
function showToast(title,msg,type='success'){
    const map={success:{bg:'#dcfce7',c:'#16a34a',p:'M5 13l4 4L19 7'},error:{bg:'#fee2e2',c:'#dc2626',p:'M6 18L18 6M6 6l12 12'},warning:{bg:'#fef9c3',c:'#ca8a04',p:'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'}};
    const s=map[type]||map.success;
    document.getElementById('toastTitle').textContent=title;document.getElementById('toastMsg').textContent=msg;
    const icon=document.getElementById('toastIcon');
    icon.innerHTML=`<svg class="w-5 h-5" fill="none" stroke="${s.c}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${s.p}"/></svg>`;
    icon.style.background=s.bg;
    const t=document.getElementById('toast');t.classList.add('show');setTimeout(()=>t.classList.remove('show'),3500);
}

document.addEventListener('keydown',e=>{
    if(e.key==='Escape'){closeDetailPanel();closeLeavePanel();closeMonetizePanel();closeCancelModal();closeCalendar();}
});

@if(session('success'))
    document.addEventListener('DOMContentLoaded',()=>showToast('Success','{{ session("success") }}','success'));
@endif
</script>
@endsection