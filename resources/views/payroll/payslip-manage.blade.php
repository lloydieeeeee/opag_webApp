@extends('layouts.app')
@section('title', 'Payslip Management')
@section('page-title', 'Payslip Management')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');

*, *::before, *::after { box-sizing: border-box; }
body, input, select, button, textarea { font-family: 'Plus Jakarta Sans', sans-serif; }

.pm-page { display: flex; flex-direction: column; min-height: calc(100vh - 120px); gap: 0; }

.breadcrumb {
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; color: #9ca3af; margin-bottom: 20px; flex-wrap: wrap;
}
.breadcrumb a { color: #9ca3af; text-decoration: none; transition: color .15s; }
.breadcrumb a:hover { color: #1a3a1a; }
.breadcrumb .sep { color: #e5e7eb; }
.breadcrumb .current { color: #1a3a1a; font-weight: 700; }

.main-card {
    background: #fff; border-radius: 20px;
    border: 1px solid #e9ecef; box-shadow: 0 2px 20px rgba(0,0,0,.07);
    overflow: hidden; flex: 1;
}

.card-topbar {
    padding: 18px 26px 15px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; flex-wrap: wrap;
    border-bottom: 1px solid #f0f2f0;
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
.card-topbar-sub { font-size: 11px; color: #9ca3af; margin: 2px 0 0; }

.controls-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

.period-select-wrap { position: relative; min-width: 220px; }
.period-select {
    width: 100%; padding: 10px 38px 10px 14px;
    font-size: 13px; font-weight: 600;
    border: 1.5px solid #e9ecef; border-radius: 11px;
    color: #111827; background: #fff;
    appearance: none; -webkit-appearance: none; cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%239ca3af' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 12px center;
    transition: all .15s;
}
.period-select:focus { outline: none; border-color: #2d5a1b; box-shadow: 0 0 0 3px rgba(45,90,27,.09); }

.search-wrap { position: relative; min-width: 220px; }
.search-wrap svg {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    width: 15px; height: 15px; color: #9ca3af; pointer-events: none;
}
.search-input {
    width: 100%; padding: 10px 14px 10px 36px;
    font-size: 13px; font-weight: 500;
    border: 1.5px solid #e9ecef; border-radius: 11px;
    color: #111827; background: #fff; outline: none;
    transition: all .15s;
}
.search-input:focus { border-color: #2d5a1b; box-shadow: 0 0 0 3px rgba(45,90,27,.09); }
.search-input::placeholder { color: #9ca3af; }

.btn-primary {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 18px; font-size: 13px; font-weight: 700;
    color: #fff; background: linear-gradient(135deg, #1a3a1a, #2d5a1b);
    border: none; border-radius: 11px; cursor: pointer;
    transition: all .2s; box-shadow: 0 3px 10px rgba(26,58,26,.28);
    text-decoration: none; white-space: nowrap;
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(26,58,26,.38); color: #fff; }
.btn-primary svg { width: 15px; height: 15px; flex-shrink: 0; }

.btn-secondary {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 18px; font-size: 13px; font-weight: 600;
    color: #374151; background: #fff;
    border: 1.5px solid #e5e7eb; border-radius: 11px; cursor: pointer;
    transition: all .2s; text-decoration: none; white-space: nowrap;
}
.btn-secondary:hover { background: #f9fafb; border-color: #d1d5db; color: #374151; }
.btn-secondary svg { width: 15px; height: 15px; flex-shrink: 0; }

.stats-bar {
    display: flex; gap: 12px; padding: 12px 26px;
    background: #f9fafb; border-bottom: 1px solid #f0f2f0;
    flex-wrap: wrap; align-items: center;
}
.stat-chip {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 14px; background: #fff; border: 1px solid #e9ecef;
    border-radius: 10px; font-size: 12px;
}
.stat-chip-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.stat-chip-label { color: #6b7280; font-weight: 500; }
.stat-chip-value { color: #111827; font-weight: 700; font-family: 'JetBrains Mono', monospace; }

.table-wrap { overflow-x: auto; }
table.pm-table { width: 100%; border-collapse: collapse; min-width: 900px; }
table.pm-table thead th {
    padding: 10px 14px; font-size: 10.5px; font-weight: 700;
    color: #6b7280; text-align: left; border-bottom: 1px solid #f0f2f0;
    background: #f9fafb; white-space: nowrap; text-transform: uppercase;
    letter-spacing: .4px;
}
table.pm-table thead th.num { text-align: right; }
table.pm-table tbody tr {
    border-bottom: 1px solid #f7f8f7;
    transition: background .12s;
    cursor: pointer;
}
table.pm-table tbody tr:hover { background: #f0fdf4; }
table.pm-table tbody tr.hidden-row { display: none; }
table.pm-table td { padding: 11px 14px; font-size: 12.5px; color: #111827; white-space: nowrap; }
table.pm-table td.num {
    text-align: right; font-family: 'JetBrains Mono', monospace;
    font-size: 12px; font-weight: 600;
}
table.pm-table td.muted { color: #9ca3af; font-size: 11px; }

.emp-badge { display: inline-flex; align-items: center; gap: 8px; }
.emp-avatar {
    width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
    background: linear-gradient(135deg, #1a3a1a, #2d5a1b);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800; color: #fff; letter-spacing: -.3px;
}
.emp-name { font-weight: 700; font-size: 12.5px; color: #111827; }
.emp-id { font-size: 10px; color: #9ca3af; margin-top: 1px; }

.net-chip {
    display: inline-block; padding: 4px 10px; border-radius: 8px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 11.5px; font-weight: 700;
    background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;
}

.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 9px; border-radius: 20px;
    font-size: 10px; font-weight: 700; text-transform: uppercase;
}
.status-badge.draft     { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.status-badge.finalized { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }

.tbl-btns { display: flex; gap: 6px; align-items: center; }
.btn-tbl {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 11px; font-size: 10.5px; font-weight: 700;
    border-radius: 8px; cursor: pointer; transition: all .15s;
    border: 1.5px solid transparent; white-space: nowrap;
    text-decoration: none;
}
.btn-tbl-pdf  { color: #065f46; background: #f0fdf4; border-color: #a7f3d0; }
.btn-tbl-pdf:hover  { background: #dcfce7; }
.btn-tbl svg { width: 11px; height: 11px; }

.no-records { text-align: center; padding: 80px 20px; color: #9ca3af; }
.no-records-icon {
    width: 64px; height: 64px; border-radius: 16px; background: #f3f4f6;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px; color: #9ca3af;
}
.no-records-icon svg { width: 32px; height: 32px; }
.no-records h3 { font-size: 18px; font-weight: 700; color: #6b7280; margin: 0 0 8px; }
.no-records p  { font-size: 13px; color: #9ca3af; margin: 0; }

/* ══ OVERLAY ══ */
#pmOverlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.3);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 40; opacity: 0; pointer-events: none;
    transition: opacity .3s ease;
}
#pmOverlay.show { opacity: 1; pointer-events: all; }

/* ══ SLIDE-IN PANEL — maximized ══ */
#payslipPanel {
    position: fixed; top: 0; right: 0; bottom: 0; z-index: 50;
    width: 72vw; min-width: 700px; max-width: 1100px;
    display: flex; flex-direction: column;
    pointer-events: none;
    transform: translateX(100%);
    transition: transform .36s cubic-bezier(.32,.72,0,1);
}
#payslipPanel.open { pointer-events: all; transform: translateX(0); }

.pp-box {
    background: #fff; width: 100%; height: 100%;
    display: flex; flex-direction: column;
    box-shadow: -16px 0 70px rgba(0,0,0,.25);
    overflow: hidden;
}

/* Panel Header */
.pp-header {
    background: linear-gradient(135deg, #1a3a1a 0%, #2d5a1b 100%);
    flex-shrink: 0;
}
.pp-header-top {
    display: flex; align-items: flex-start; justify-content: space-between;
    padding: 20px 28px 0;
}
.pp-avatar {
    width: 50px; height: 50px; border-radius: 14px; flex-shrink: 0;
    background: rgba(255,255,255,.18);
    display: flex; align-items: center; justify-content: center;
    font-size: 19px; font-weight: 800; color: #fff;
    margin-right: 16px; letter-spacing: -.5px;
}
.pp-header-info { flex: 1; min-width: 0; }
.pp-header-info h2 {
    font-size: 18px; font-weight: 800; color: #fff;
    margin: 0 0 2px; letter-spacing: -.02em;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.pp-header-info p { font-size: 12px; color: rgba(255,255,255,.65); margin: 0; }
.pp-close {
    background: rgba(255,255,255,.15); border: none;
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: rgba(255,255,255,.8);
    transition: background .15s; flex-shrink: 0; margin-top: 2px;
}
.pp-close:hover { background: rgba(255,255,255,.28); color: #fff; }
.pp-close svg { width: 16px; height: 16px; }

/* Edit notice banner inside header */
.pp-edit-notice {
    display: none; align-items: center; justify-content: space-between;
    padding: 9px 28px; background: rgba(245,158,11,.18);
    border-top: 1px solid rgba(245,158,11,.35); gap: 10px; flex-wrap: wrap;
    margin-top: 10px;
}
.pp-edit-notice.visible { display: flex; }
.edit-mode-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; background: #f59e0b; color: #fff;
    font-size: 10px; font-weight: 800; border-radius: 20px;
    text-transform: uppercase; letter-spacing: .5px;
}
.edit-mode-badge svg { width: 10px; height: 10px; }
.edit-mode-hint { font-size: 11px; color: rgba(255,255,255,.8); font-weight: 500; }
.pp-edit-actions { display: flex; gap: 8px; }

/* Tabs */
.pp-tabs {
    display: flex; padding: 12px 28px 0; gap: 2px;
    border-top: 1px solid rgba(255,255,255,.1);
    margin-top: 14px; overflow-x: auto; scrollbar-width: none;
}
.pp-tabs::-webkit-scrollbar { display: none; }
.pp-tab {
    padding: 9px 18px; font-size: 12px; font-weight: 600;
    color: rgba(255,255,255,.55); background: none; border: none;
    border-bottom: 2px solid transparent; cursor: pointer;
    white-space: nowrap; transition: color .15s, border-color .15s;
    display: flex; align-items: center; gap: 6px;
}
.pp-tab:hover { color: rgba(255,255,255,.85); }
.pp-tab.active { color: #fff; border-bottom-color: #86efac; }
.pp-tab svg { width: 13px; height: 13px; }

/* Panel Body */
.pp-body {
    flex: 1; overflow-y: auto; background: #f4f6f3;
    scrollbar-width: thin; scrollbar-color: #d1d5db transparent;
}
.pp-body::-webkit-scrollbar { width: 5px; }
.pp-body::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }

.pp-pane { display: none !important; }
.pp-pane.active { display: block !important; }

/* ══════════════════════════════════════════
   PREVIEW PANE — WIDE, inline-editable
══════════════════════════════════════════ */
.preview-pane-wrap {
    padding: 24px 28px;
    display: flex;
    gap: 24px;
    align-items: flex-start;
}

/* Left: the payslip card — wider */
.slip-doc-card {
    background: #fff;
    border-radius: 16px;
    border: 1.5px solid #e2e8f0;
    flex: 1 1 0;
    min-width: 0;
    box-shadow: 0 4px 28px rgba(0,0,0,.09);
    overflow: hidden;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

/* Right: live totals sidebar */
.slip-sidebar {
    width: 200px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
    position: sticky;
    top: 24px;
}
.sbar-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 14px 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
}
.sbar-card-title {
    font-size: 9px; font-weight: 800; color: #94a3b8;
    text-transform: uppercase; letter-spacing: 1px;
    margin-bottom: 10px;
}
.sbar-row {
    display: flex; justify-content: space-between;
    align-items: center; margin-bottom: 6px;
    font-size: 11px;
}
.sbar-row:last-child { margin-bottom: 0; }
.sbar-label { color: #64748b; font-weight: 500; }
.sbar-val {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px; font-weight: 700; color: #0f172a;
}
.sbar-val.green { color: #16a34a; }
.sbar-val.red   { color: #dc2626; }
.sbar-net-card {
    background: linear-gradient(135deg, #fef9c3, #fef08a);
    border: 1.5px solid #eab308;
    border-radius: 12px; padding: 14px 16px;
}
.sbar-net-label {
    font-size: 9px; font-weight: 800; color: #713f12;
    text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;
}
.sbar-net-val {
    font-size: 16px; font-weight: 800; color: #713f12;
    font-family: 'JetBrains Mono', monospace;
}
.sbar-edit-btn {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    width: 100%; padding: 10px; font-size: 12px; font-weight: 700;
    color: #fff; background: linear-gradient(135deg, #1a3a1a, #2d5a1b);
    border: none; border-radius: 10px; cursor: pointer;
    transition: all .2s; box-shadow: 0 2px 8px rgba(26,58,26,.25);
}
.sbar-edit-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(26,58,26,.35); }
.sbar-edit-btn svg { width: 13px; height: 13px; }
.sbar-save-btn {
    display: none; align-items: center; justify-content: center; gap: 6px;
    width: 100%; padding: 10px; font-size: 12px; font-weight: 700;
    color: #fff; background: linear-gradient(135deg, #059669, #10b981);
    border: none; border-radius: 10px; cursor: pointer;
    transition: all .2s; box-shadow: 0 2px 8px rgba(5,150,105,.25);
}
.sbar-save-btn:hover { transform: translateY(-1px); }
.sbar-save-btn svg { width: 13px; height: 13px; }
.sbar-cancel-btn {
    display: none; align-items: center; justify-content: center; gap: 6px;
    width: 100%; padding: 9px; font-size: 12px; font-weight: 600;
    color: #6b7280; background: #f3f4f6; border: 1.5px solid #e5e7eb;
    border-radius: 10px; cursor: pointer; transition: all .15s;
}
.sbar-cancel-btn:hover { background: #e5e7eb; }

/* ── Payslip header band ── */
.slip-header-band {
    background: linear-gradient(135deg, #1a3a1a 0%, #2d5a1b 100%);
    padding: 18px 24px 16px;
    text-align: center;
}
.slip-header-band .sh-gov {
    font-size: 9px; color: rgba(255,255,255,.55);
    letter-spacing: .8px; text-transform: uppercase; margin-bottom: 4px;
}
.slip-header-band .sh-dept {
    font-size: 14px; font-weight: 800; color: #fff;
    letter-spacing: -.2px; line-height: 1.3;
}
.slip-header-band .sh-office {
    font-size: 10px; color: rgba(255,255,255,.6); margin-top: 3px;
}
.slip-header-band .sh-badge {
    display: inline-block; margin-top: 10px;
    background: rgba(255,255,255,.14);
    border: 1px solid rgba(255,255,255,.28);
    color: #fff; font-size: 8.5px; font-weight: 800;
    letter-spacing: 2.5px; text-transform: uppercase;
    padding: 4px 16px; border-radius: 4px;
}

/* Period bar */
.slip-period-bar {
    background: #f0fdf4; border-bottom: 1px solid #dcfce7;
    padding: 8px 22px;
    display: flex; align-items: center; justify-content: space-between;
}
.slip-period-bar .spb-label {
    font-size: 8.5px; font-weight: 700; color: #166534;
    text-transform: uppercase; letter-spacing: .6px;
}
.slip-period-bar .spb-val {
    font-size: 11px; font-weight: 800; color: #14532d;
    font-family: 'JetBrains Mono', monospace;
}

/* Employee strip */
.slip-emp-strip {
    padding: 14px 22px 12px;
    border-bottom: 1.5px solid #f1f5f9;
    display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px;
}
.slip-emp-strip .see-full { grid-column: 1 / -1; }
.slip-emp-field { display: flex; flex-direction: column; gap: 2px; }
.slip-emp-field .sef-label {
    font-size: 8px; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .5px;
}
.slip-emp-field .sef-val {
    font-size: 12px; font-weight: 800; color: #0f172a;
}

/* Section blocks */
.slip-section {
    padding: 11px 22px;
    border-bottom: 1px solid #f1f5f9;
}
.slip-section:last-of-type { border-bottom: none; }
.slip-section-title {
    font-size: 8px; font-weight: 800; color: #94a3b8;
    text-transform: uppercase; letter-spacing: 1px;
    margin-bottom: 8px; padding-bottom: 6px;
    border-bottom: 1px dashed #e2e8f0;
    display: flex; align-items: center; gap: 6px;
}
.slip-section-title .sst-dot {
    width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0;
}

/* Lines table */
.slip-lines-table { width: 100%; border-collapse: collapse; }
.slip-lines-table tr { border-bottom: 1px solid #f8fafc; }
.slip-lines-table tr:last-child { border-bottom: none; }
.slip-lines-table td { padding: 4px 0; vertical-align: middle; }
.slip-lines-table .slt-label {
    font-size: 10.5px; color: #475569; padding-left: 4px; width: 65%;
}
.slip-lines-table .slt-amount {
    font-size: 11px; font-weight: 600; text-align: right;
    font-family: 'JetBrains Mono', monospace; color: #0f172a;
}
.slip-lines-table .slt-amount.zero { color: #cbd5e1; font-weight: 400; }
.slip-lines-table .slt-label.primary {
    font-size: 12px; font-weight: 700; color: #0f172a; padding-left: 0;
}
.slip-lines-table .slt-amount.primary {
    font-size: 12px; font-weight: 800; color: #0f172a;
}
.slip-lines-table .slt-label.custom-ded { color: #92400e; font-weight: 700; padding-left: 4px; }
.slip-lines-table .slt-amount.custom-ded { color: #92400e; font-weight: 700; }

/* ── INLINE EDIT INPUTS on preview ── */
.slip-editable {
    background: transparent;
    border: none; outline: none;
    font: inherit; color: inherit;
    text-align: inherit;
    width: 100%; padding: 0;
    cursor: default;
    transition: background .15s, border .15s;
}
/* When edit mode is active */
.preview-edit-active .slip-editable {
    background: #fffbeb;
    border: 1.5px solid #f59e0b;
    border-radius: 5px;
    padding: 2px 6px;
    cursor: text;
}
.preview-edit-active .slip-editable:focus {
    background: #fef3c7;
    border-color: #d97706;
    box-shadow: 0 0 0 2px rgba(217,119,6,.15);
    outline: none;
}
.preview-edit-active .slt-amount .slip-editable {
    text-align: right;
}
/* custom ded label editable */
.slip-ded-label-input {
    background: transparent; border: none; outline: none;
    font: inherit; color: inherit; width: 100%; padding: 0; cursor: default;
    transition: background .15s, border .15s;
}
.preview-edit-active .slip-ded-label-input {
    background: #fffbeb; border: 1.5px solid #f59e0b;
    border-radius: 5px; padding: 2px 6px; cursor: text;
}
.preview-edit-active .slip-ded-label-input:focus {
    background: #fef3c7; border-color: #d97706;
    box-shadow: 0 0 0 2px rgba(217,119,6,.15); outline: none;
}

/* Totals band */
.slip-totals-band {
    background: #f8fafc; border-top: 1.5px solid #e2e8f0;
    padding: 10px 22px;
}
.slip-totals-row {
    display: flex; justify-content: space-between;
    align-items: center; padding: 3px 0;
}
.slip-totals-row .str-label { font-size: 10.5px; color: #64748b; font-weight: 500; }
.slip-totals-row .str-val {
    font-size: 11.5px; font-weight: 700; color: #0f172a;
    font-family: 'JetBrains Mono', monospace;
}
.slip-totals-row .str-val.green { color: #166534; }
.slip-totals-row .str-val.red   { color: #b91c1c; }

/* Net pay box */
.slip-net-band {
    margin: 10px 18px 16px;
    border-radius: 12px;
    background: linear-gradient(135deg, #fef9c3 0%, #fef08a 100%);
    border: 1.5px solid #eab308;
    padding: 14px 18px;
    display: flex; align-items: center; justify-content: space-between;
}
.slip-net-band .snb-label {
    font-size: 10px; font-weight: 800; color: #713f12;
    text-transform: uppercase; letter-spacing: .8px;
}
.slip-net-band .snb-val {
    font-size: 20px; font-weight: 800; color: #713f12;
    font-family: 'JetBrains Mono', monospace;
}

/* Signatory */
.slip-sig-band {
    text-align: center; padding: 10px 22px 16px;
    border-top: 1px dashed #e2e8f0; background: #fafafa;
}
.slip-sig-band .ssb-line {
    width: 130px; height: 1px; background: #cbd5e1;
    margin: 0 auto 7px;
}
.slip-sig-band .ssb-name {
    font-size: 10.5px; font-weight: 800; color: #1e293b;
    text-transform: uppercase; letter-spacing: .4px;
}
.slip-sig-band .ssb-title { font-size: 9.5px; color: #94a3b8; margin-top: 2px; }

/* ══ Panel Footer ══ */
.pp-footer {
    flex-shrink: 0; padding: 14px 28px;
    background: #fff; border-top: 1px solid #f3f4f6;
    display: flex; align-items: center; justify-content: space-between;
    gap: 10px; flex-wrap: wrap;
}
.pp-footer-left { display: flex; align-items: center; gap: 8px; }
.pp-footer-right { display: flex; gap: 8px; }
.pp-period-badge {
    font-size: 11px; font-weight: 600; color: #1a3a1a;
    background: #f0fdf4; padding: 4px 10px; border-radius: 7px;
    border: 1px solid #bbf7d0;
}

.btn-pp-close {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 9px 16px; font-size: 12px; font-weight: 600;
    color: #6b7280; background: #fff; border: 1.5px solid #e5e7eb;
    border-radius: 9px; cursor: pointer; transition: all .15s;
}
.btn-pp-close:hover { background: #f9fafb; }

.btn-pp-pdf {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 9px 16px; font-size: 12px; font-weight: 600;
    color: #065f46; background: #f0fdf4; border: 1.5px solid #a7f3d0;
    border-radius: 9px; cursor: pointer; transition: all .15s;
    text-decoration: none;
}
.btn-pp-pdf:hover { background: #dcfce7; }
.btn-pp-pdf svg { width: 13px; height: 13px; }

/* ── Signatory Modal ── */
.sig-modal-overlay {
    position: fixed; inset: 0; z-index: 1100;
    background: rgba(0,0,0,.45); backdrop-filter: blur(3px);
    display: flex; align-items: center; justify-content: center;
    padding: 20px; opacity: 0; pointer-events: none; transition: opacity .2s;
}
.sig-modal-overlay.open { opacity: 1; pointer-events: all; }
.sig-modal {
    background: #fff; border-radius: 18px;
    box-shadow: 0 24px 64px rgba(0,0,0,.2);
    width: 100%; max-width: 440px;
    transform: translateY(20px) scale(.97);
    transition: transform .25s cubic-bezier(.34,1.56,.64,1); overflow: hidden;
}
.sig-modal-overlay.open .sig-modal { transform: translateY(0) scale(1); }
.sig-modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 22px 14px;
    background: linear-gradient(135deg, #fafffe, #f6faf6);
    border-bottom: 1px solid #f0f2f0;
}
.sig-modal-title { font-size: 14px; font-weight: 800; color: #111827; margin: 0; }
.sig-modal-sub   { font-size: 11px; color: #9ca3af; margin: 2px 0 0; }
.sig-modal-body  { padding: 22px; }
.sig-field-group { margin-bottom: 16px; }
.sig-field-label {
    display: block; font-size: 10.5px; font-weight: 700; color: #374151;
    text-transform: uppercase; letter-spacing: .4px; margin-bottom: 6px;
}
.sig-field-input {
    width: 100%; padding: 10px 12px;
    font-size: 13px; font-weight: 600; font-family: 'Plus Jakarta Sans', sans-serif;
    border: 1.5px solid #e5e7eb; border-radius: 10px;
    color: #111827; background: #fff; outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.sig-field-input:focus { border-color: #2d5a1b; box-shadow: 0 0 0 3px rgba(45,90,27,.09); }
.sig-field-hint  { font-size: 10px; color: #9ca3af; margin-top: 4px; }
.sig-modal-footer {
    padding: 14px 22px; border-top: 1px solid #f0f2f0;
    display: flex; gap: 8px; justify-content: flex-end; background: #fafffe;
}
.btn-sig-save {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; font-size: 12px; font-weight: 700;
    color: #fff; background: linear-gradient(135deg, #1a3a1a, #2d5a1b);
    border: none; border-radius: 9px; cursor: pointer;
    transition: all .2s; box-shadow: 0 2px 8px rgba(26,58,26,.25);
}
.btn-sig-save:hover { transform: translateY(-1px); }
.btn-sig-save:disabled { opacity: .6; cursor: not-allowed; transform: none; }
.btn-sig-cancel {
    padding: 9px 16px; font-size: 12px; font-weight: 600;
    color: #6b7280; background: #fff;
    border: 1.5px solid #e5e7eb; border-radius: 9px; cursor: pointer; transition: all .15s;
}
.btn-sig-cancel:hover { background: #f9fafb; }
.sig-note {
    font-size: 11px; color: #6b7280; padding: 10px 12px;
    background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;
    margin-bottom: 16px; line-height: 1.5;
}
.modal-close-dark {
    background: #f3f4f6; border: 1.5px solid #e5e7eb;
    width: 30px; height: 30px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: #6b7280; transition: all .15s;
}
.modal-close-dark:hover { background: #fee2e2; border-color: #fca5a5; color: #ef4444; }

/* Toast */
#toast-container {
    position: fixed; bottom: 24px; right: 24px; z-index: 9999;
    display: flex; flex-direction: column; gap: 8px;
}
.toast {
    display: flex; align-items: center; gap: 10px;
    padding: 13px 18px; border-radius: 12px;
    font-size: 13px; font-weight: 600;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    border: 1.5px solid transparent;
    transform: translateY(20px); opacity: 0;
    transition: all .3s cubic-bezier(.34,1.56,.64,1);
    min-width: 240px;
}
.toast.show    { transform: translateY(0); opacity: 1; }
.toast.success { background: #d1fae5; border-color: #10b981; color: #065f46; }
.toast.error   { background: #fee2e2; border-color: #ef4444; color: #991b1b; }
.toast svg { width: 16px; height: 16px; flex-shrink: 0; }

@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 900px) {
    #payslipPanel { width: 96vw; min-width: 0; }
    .preview-pane-wrap { flex-direction: column; }
    .slip-sidebar { width: 100%; position: static; flex-direction: row; flex-wrap: wrap; }
}
@media (max-width: 700px) {
    .card-topbar { flex-direction: column; align-items: stretch; }
    .controls-row { flex-direction: column; align-items: stretch; }
    .period-select-wrap, .search-wrap { min-width: 0; }
}
</style>

<div class="breadcrumb">
    <a href="{{ route('payroll.index') }}">Payroll</a>
    <span class="sep">›</span>
    <span class="current">Payslip Management</span>
</div>

@php
    $currentPeriod = $periods->firstWhere('period_id', $selectedPeriodId);
@endphp

<div class="pm-page">
    <div class="main-card">

        <div class="card-topbar">
            <div class="card-topbar-left">
                <div class="card-topbar-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="card-topbar-title">Payslip Management</p>
                    <p class="card-topbar-sub">View, edit, and export employee payslips by period</p>
                </div>
            </div>

            <div class="controls-row">
                <div class="period-select-wrap">
                    <select class="period-select" id="periodSelect"
                        onchange="window.location.href='{{ route('payroll.manage') }}?period_id='+this.value">
                        <option value="">— Select Period —</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->period_id }}"
                                {{ $selectedPeriodId == $p->period_id ? 'selected' : '' }}>
                                {{ $p->period_label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($records->isNotEmpty())
                <div class="search-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="text" class="search-input" id="searchInput"
                        placeholder="Search employee..." oninput="filterTable(this.value)">
                </div>

                <a href="{{ route('payroll.payslip-all-pdf', $selectedPeriodId) }}"
                    target="_blank" class="btn-primary" onclick="event.stopPropagation()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export All PDF
                </a>

                <a href="{{ route('payroll.pdf', $selectedPeriodId) }}"
                    target="_blank" class="btn-secondary" onclick="event.stopPropagation()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Payroll PDF
                </a>

                <button type="button" class="btn-secondary" onclick="event.stopPropagation(); openSigModal()"
                    style="border-color:#bbf7d0;color:#1a3a1a;background:#f0fdf4;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Signatory
                </button>
                @endif
            </div>
        </div>

        @if($records->isNotEmpty())
        <div class="stats-bar">
            <div class="stat-chip">
                <div class="stat-chip-dot" style="background:#3b82f6;"></div>
                <span class="stat-chip-label">Employees</span>
                <span class="stat-chip-value" id="statCount">{{ $records->count() }}</span>
            </div>
            <div class="stat-chip">
                <div class="stat-chip-dot" style="background:#10b981;"></div>
                <span class="stat-chip-label">Total Gross</span>
                <span class="stat-chip-value">₱{{ number_format($records->sum('gross_salary'), 2) }}</span>
            </div>
            <div class="stat-chip">
                <div class="stat-chip-dot" style="background:#f59e0b;"></div>
                <span class="stat-chip-label">Total Net</span>
                <span class="stat-chip-value">₱{{ number_format($records->sum('net_pay'), 2) }}</span>
            </div>
            <div class="stat-chip">
                <div class="stat-chip-dot" style="background:#ef4444;"></div>
                <span class="stat-chip-label">Total Deductions</span>
                <span class="stat-chip-value">₱{{ number_format($records->sum('total_deductions'), 2) }}</span>
            </div>
            @if($currentPeriod)
            <div style="margin-left:auto;">
                <span class="status-badge {{ strtolower($currentPeriod->status) }}">
                    {{ $currentPeriod->status }}
                </span>
            </div>
            @endif
        </div>

        <div class="table-wrap">
            <table class="pm-table" id="pmTable">
                <thead>
                    <tr>
                        <th style="width:28px;">#</th>
                        <th>Employee</th>
                        <th>Position</th>
                        <th class="num">Gross Salary</th>
                        <th class="num">Total Deductions</th>
                        <th class="num">Net Pay</th>
                        <th class="num">GSIS EE</th>
                        <th class="num">Pag-Ibig</th>
                        <th class="num">PhilHealth</th>
                        <th class="num">W/Tax</th>
                        <th class="num">PERA</th>
                        <th style="width:80px;">PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $i => $r)
                    @php
                        $lastName  = strtoupper($r->employee->last_name ?? '—');
                        $firstName = $r->employee->first_name ?? '';
                        $fullName  = $lastName.', '.$firstName;
                        $initials  = substr($lastName,0,1).substr($firstName,0,1);
                        $posCode   = $r->designation ?? optional($r->employee->position)->position_code ?? 'N/A';
                        $rid       = $r->payroll_id;
                    @endphp
                    <tr data-name="{{ strtolower($fullName) }}"
                        data-id="{{ $r->employee_id }}"
                        data-rid="{{ $rid }}"
                        onclick="openPayslipPanel({{ $rid }})">
                        <td class="muted">{{ $i + 1 }}</td>
                        <td>
                            <div class="emp-badge">
                                <div class="emp-avatar">{{ $initials }}</div>
                                <div>
                                    <div class="emp-name">{{ $fullName }}</div>
                                    <div class="emp-id">ID: {{ $r->employee_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="muted">{{ $posCode }}</td>
                        <td class="num">{{ number_format($r->gross_salary, 2) }}</td>
                        <td class="num" style="color:#ef4444;">{{ number_format($r->total_deductions, 2) }}</td>
                        <td><span class="net-chip">₱{{ number_format($r->net_pay, 2) }}</span></td>
                        <td class="num">{{ $r->gsis_ee > 0 ? number_format($r->gsis_ee, 2) : '—' }}</td>
                        <td class="num">{{ ($r->pagibig_govt ?? 0) > 0 ? number_format($r->pagibig_govt, 2) : '—' }}</td>
                        <td class="num">{{ ($r->philhealth_ee ?? 0) > 0 ? number_format($r->philhealth_ee, 2) : '—' }}</td>
                        <td class="num">{{ ($r->withholding_tax ?? 0) > 0 ? number_format($r->withholding_tax, 2) : '—' }}</td>
                        <td class="num">{{ ($r->allowance_pera ?? 0) > 0 ? number_format($r->allowance_pera, 2) : '—' }}</td>
                        <td onclick="event.stopPropagation()">
                            <div class="tbl-btns">
                                <a href="{{ route('payroll.payslip-all-pdf', $r->period_id) }}?emp_id={{ $r->employee_id }}"
                                    target="_blank" class="btn-tbl btn-tbl-pdf">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="no-records">
            <div class="no-records-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3>No Records Found</h3>
            <p>Select a payroll period from the dropdown above.</p>
        </div>
        @endif

    </div>
</div>

<div id="pmOverlay" onclick="closePayslipPanel()"></div>

{{-- SLIDE-IN PAYSLIP PANEL --}}
<div id="payslipPanel">
    <div class="pp-box">

        <div class="pp-header">
            <div class="pp-header-top">
                <div class="pp-avatar" id="ppAvatar">—</div>
                <div class="pp-header-info">
                    <h2 id="ppName">Loading…</h2>
                    <p id="ppMeta"></p>
                </div>
                <button class="pp-close" onclick="closePayslipPanel()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="pp-edit-notice" id="ppEditNotice">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="edit-mode-badge">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Mode
                    </span>
                    <span class="edit-mode-hint">Click any value on the payslip to edit. Only this employee will be updated.</span>
                </div>
            </div>

            <div class="pp-tabs">
                <button class="pp-tab active" onclick="switchPpTab('preview')" id="pptab-preview">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Payslip Preview
                </button>
            </div>
        </div>

        <div class="pp-body" id="ppBody">
            <div class="pp-pane active" id="ppPane-preview">
                <div class="preview-pane-wrap" id="previewPaneWrap">

                    {{-- Left: payslip document --}}
                    <div class="slip-doc-card" id="slipDocCard">
                        <div style="text-align:center;padding:60px 20px;color:#9ca3af;font-size:12px;">
                            Select an employee to preview their payslip.
                        </div>
                    </div>

                    {{-- Right: sidebar --}}
                    <div class="slip-sidebar" id="slipSidebar" style="display:none;">

                        <div class="sbar-card">
                            <div class="sbar-card-title">Live Totals</div>
                            <div class="sbar-row">
                                <span class="sbar-label">Gross</span>
                                <span class="sbar-val" id="sb_gross">—</span>
                            </div>
                            <div class="sbar-row">
                                <span class="sbar-label">Allowances</span>
                                <span class="sbar-val green" id="sb_alw">—</span>
                            </div>
                            <div class="sbar-row">
                                <span class="sbar-label">Deductions</span>
                                <span class="sbar-val red" id="sb_ded">—</span>
                            </div>
                        </div>

                        <div class="sbar-net-card">
                            <div class="sbar-net-label">Net Pay</div>
                            <div class="sbar-net-val" id="sb_net">—</div>
                        </div>

                        <button class="sbar-edit-btn" id="sbarEditBtn" onclick="enablePreviewEdit()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Payslip
                        </button>

                        <button class="sbar-save-btn" id="sbarSaveBtn" onclick="savePreviewEdit()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Changes
                        </button>

                        <button class="sbar-cancel-btn" id="sbarCancelBtn" onclick="cancelPreviewEdit()">
                            Cancel
                        </button>

                        <a id="sbarPdfLink" href="#" target="_blank" class="btn-pp-pdf" style="justify-content:center;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            View PDF
                        </a>

                    </div>

                </div>
            </div>
        </div>

        <div class="pp-footer">
            <div class="pp-footer-left">
                <span class="pp-period-badge" id="ppPeriodBadge">—</span>
                <span id="ppEmpId" style="font-size:11px;color:#9ca3af;"></span>
            </div>
            <div class="pp-footer-right">
                <button class="btn-pp-close" onclick="closePayslipPanel()">Close</button>
            </div>
        </div>

    </div>
</div>

{{-- SIGNATORY MODAL --}}
<div class="sig-modal-overlay" id="sigModal">
    <div class="sig-modal">
        <div class="sig-modal-header">
            <div>
                <p class="sig-modal-title">Signatory Settings</p>
                <p class="sig-modal-sub" id="sigModalPeriodLabel">Period: —</p>
            </div>
            <button class="modal-close-dark" onclick="closeSigModal()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="sig-modal-body">
            <p class="sig-note">
                These details appear on the <strong>payslip PDF</strong> and <strong>payroll PDF</strong>
                as the Payroll Clerk / Signatory. Changes apply to all payslips in the selected period.
            </p>
            <div class="sig-field-group">
                <label class="sig-field-label">Payroll Clerk Name</label>
                <input type="text" class="sig-field-input" id="sig_clerk_name"
                    placeholder="e.g. MELINDA R. BARCELONA"
                    value="{{ optional($currentPeriod)->sig_clerk_name ?? '' }}">
                <p class="sig-field-hint">Full name in UPPERCASE as it should appear on documents.</p>
            </div>
            <div class="sig-field-group">
                <label class="sig-field-label">Clerk Title / Role</label>
                <input type="text" class="sig-field-input" id="sig_clerk_title"
                    placeholder="e.g. AO V / Payroll Clerk"
                    value="{{ optional($currentPeriod)->sig_clerk_title ?? 'AO V / Payroll Clerk' }}">
                <p class="sig-field-hint">Position title shown below the name.</p>
            </div>
        </div>
        <div class="sig-modal-footer">
            <button class="btn-sig-cancel" onclick="closeSigModal()">Cancel</button>
            <button class="btn-sig-save" id="sigSaveBtn" onclick="saveSignatory()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Signatory
            </button>
        </div>
    </div>
</div>

<div id="toast-container"></div>

<script>
const CSRF               = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const SELECTED_PERIOD_ID = {{ $selectedPeriodId ?? 'null' }};
const RECORD_UPDATE_URL  = '{{ url('payroll/record') }}';
const SIGNATORY_URL_BASE = '{{ url('payroll/period') }}/';
const PERIOD_IS_FINALIZED = {{ (optional($currentPeriod)->status === 'FINALIZED') ? 'true' : 'false' }};

const RECORDS = {!! \Illuminate\Support\Js::from(
    $records->mapWithKeys(function($r) use ($currentPeriod) {
        $empName     = strtoupper($r->employee->last_name ?? '—') . ', ' . ($r->employee->first_name ?? '');
        $posCode     = $r->designation ?? optional($r->employee->position)->position_code ?? 'N/A';
        $periodLabel = optional($r->period)->period_label ?? optional($currentPeriod)->period_label ?? '—';
        $sigName     = strtoupper(optional($currentPeriod)->sig_clerk_name ?? 'MELINDA R. BARCELONA');
        $sigTitle    = optional($currentPeriod)->sig_clerk_title ?? 'AO V / Payroll Clerk';

        return [(string)$r->payroll_id => [
            'record_id'             => (int)$r->payroll_id,
            'employee_id'           => (string)$r->employee_id,
            'period_id'             => (int)$r->period_id,
            'emp_name'              => $empName,
            'position'              => $posCode,
            'period_label'          => $periodLabel,
            'sig_name'              => $sigName,
            'sig_clerk_title'       => $sigTitle,
            'gross_salary'          => (float)$r->gross_salary,
            'gsis_ee'               => (float)($r->gsis_ee ?? 0),
            'gsis_govt'             => (float)($r->gsis_govt ?? 0),
            'gsis_ec'               => (float)($r->gsis_ec ?? 0),
            'gsis_policy'           => (float)($r->gsis_policy ?? 0),
            'gsis_emergency'        => (float)($r->gsis_emergency ?? 0),
            'gsis_real_estate'      => (float)($r->gsis_real_estate ?? 0),
            'gsis_mpl'              => (float)($r->gsis_mpl ?? 0),
            'gsis_mpl_lite'         => (float)($r->gsis_mpl_lite ?? 0),
            'gsis_gfal'             => (float)($r->gsis_gfal ?? 0),
            'gsis_computer'         => (float)($r->gsis_computer ?? 0),
            'gsis_conso'            => (float)($r->gsis_conso ?? 0),
            'pagibig_ee'            => (float)($r->pagibig_ee ?? 0),
            'pagibig_govt'          => (float)($r->pagibig_govt ?? 0),
            'pagibig_mpl'           => (float)($r->pagibig_mpl ?? 0),
            'pagibig_calamity'      => (float)($r->pagibig_calamity ?? 0),
            'philhealth_ee'         => (float)($r->philhealth_ee ?? 0),
            'philhealth_govt'       => (float)($r->philhealth_govt ?? 0),
            'withholding_tax'       => (float)($r->withholding_tax ?? 0),
            'loan_dbp'              => (float)($r->loan_dbp ?? 0),
            'loan_lbp'              => (float)($r->loan_lbp ?? 0),
            'loan_cngwmpc'          => (float)($r->loan_cngwmpc ?? 0),
            'loan_paracle'          => (float)($r->loan_paracle ?? 0),
            'overpayment'           => (float)($r->overpayment ?? 0),
            'other_deduction'       => (float)($r->other_deduction ?? 0),
            'other_deduction_label' => $r->other_deduction_label ?? '',
            'allowance_pera'        => (float)($r->allowance_pera ?? 0),
            'allowance_rata'        => (float)($r->allowance_rata ?? 0),
            'allowance_ta'          => (float)($r->allowance_ta ?? 0),
            'allowance_other'       => (float)($r->allowance_other ?? 0),
            'total_deductions'      => (float)($r->total_deductions ?? 0),
            'total_allowances'      => (float)($r->total_allowances ?? 0),
            'net_pay'               => (float)($r->net_pay ?? 0),
        ]];
    })->toArray()
) !!};

const DEDUCTION_FIELDS = [
    'gsis_ee','gsis_ec','gsis_policy','gsis_emergency','gsis_real_estate',
    'gsis_mpl','gsis_mpl_lite','gsis_gfal','gsis_computer','gsis_conso',
    'pagibig_govt','pagibig_mpl','pagibig_calamity',
    'philhealth_ee',
    'withholding_tax','loan_dbp','loan_lbp','loan_cngwmpc','loan_paracle',
    'overpayment','other_deduction',
];
const ALLOWANCE_FIELDS = ['allowance_pera','allowance_rata','allowance_ta','allowance_other'];
const NUMERIC_FIELDS   = ['gross_salary', ...DEDUCTION_FIELDS, ...ALLOWANCE_FIELDS];

let currentRecordId = null;
let editModeActive  = false;

// ── Format helpers ─────────────────────────────────────────────────────────
function fmt(v) {
    return parseFloat(v || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2, maximumFractionDigits: 2
    });
}

// ── Get live values from editable inputs in the slip card ─────────────────
function getLiveData() {
    const base = { ...RECORDS[currentRecordId] };
    const card = document.getElementById('slipDocCard');
    if (!card) return base;
    card.querySelectorAll('.slip-editable[data-field]').forEach(el => {
        const f = el.dataset.field;
        const raw = el.value.replace(/,/g, '').trim();
        const num = parseFloat(raw);
        if (!isNaN(num)) base[f] = num;
    });
    const labelEl = card.querySelector('.slip-ded-label-input');
    if (labelEl) base.other_deduction_label = labelEl.value.trim();

    // Recompute totals
    const ded = DEDUCTION_FIELDS.reduce((s, f) => s + (base[f] || 0), 0);
    const alw = ALLOWANCE_FIELDS.reduce((s, f) => s + (base[f] || 0), 0);
    base.total_deductions = ded;
    base.total_allowances = alw;
    base.net_pay = (base.gross_salary || 0) - ded + alw;
    return base;
}

// ── Update sidebar live totals ─────────────────────────────────────────────
function updateSidebar(r) {
    document.getElementById('sb_gross').textContent = fmt(r.gross_salary);
    document.getElementById('sb_alw').textContent   = fmt(r.total_allowances);
    document.getElementById('sb_ded').textContent   = fmt(r.total_deductions);
    document.getElementById('sb_net').textContent   = '₱' + fmt(r.net_pay);

    // Also update net pay display in the slip card
    const netEl = document.getElementById('slip_net_pay_display');
    if (netEl) netEl.textContent = '₱' + fmt(r.net_pay);
    const totalAlwEl = document.getElementById('slip_total_alw_display');
    if (totalAlwEl) totalAlwEl.textContent = '+ ₱' + fmt(r.total_allowances);
    const totalDedEl = document.getElementById('slip_total_ded_display');
    if (totalDedEl) totalDedEl.textContent = '− ₱' + fmt(r.total_deductions);
}

// ── Build payslip HTML (with inline editable inputs) ──────────────────────
function buildPayslipHTML(r, isEditing) {
    const gross    = r.gross_salary     || 0;
    const pera     = r.allowance_pera   || 0;
    const rata     = r.allowance_rata   || 0;
    const ta       = r.allowance_ta     || 0;
    const otherAlw = r.allowance_other  || 0;
    const ded      = r.total_deductions || 0;
    const net      = r.net_pay          || 0;
    const totalAlw = pera + rata + ta + otherAlw;
    const otherDed = r.other_deduction  || 0;
    const otherLbl = r.other_deduction_label || 'Custom Deduction';
    const sigTitle = r.sig_clerk_title  || 'AO V / Payroll Clerk';

    // Editable number input
    function numInput(field, value) {
        const v = value > 0 ? fmt(value) : (value === 0 ? '0.00' : '—');
        if (!isEditing) {
            return `<span class="slip-editable" style="display:inline-block;min-width:60px;">${value <= 0 ? '—' : fmt(value)}</span>`;
        }
        return `<input type="text" class="slip-editable" data-field="${field}" value="${value <= 0 ? '0.00' : fmt(value)}"
            oninput="onSlipInput()" style="min-width:80px;max-width:110px;">`;
    }

    // Row helper
    function row(label, field, amount, customClass = '') {
        const isZero = !amount || amount <= 0;
        const lblCls = customClass ? `slt-label ${customClass}` : 'slt-label';
        const amtCls = customClass ? `slt-amount ${customClass}` : (isZero && !isEditing ? 'slt-amount zero' : 'slt-amount');

        if (!isEditing) {
            return `<tr>
                <td class="${lblCls}">${label}</td>
                <td class="${amtCls}">${isZero ? '—' : fmt(amount)}</td>
            </tr>`;
        }
        return `<tr>
            <td class="${lblCls}">${label}</td>
            <td class="${amtCls}">${numInput(field, amount)}</td>
        </tr>`;
    }

    // Primary bold row
    function primaryRow(label, field, amount) {
        if (!isEditing) {
            return `<tr>
                <td class="slt-label primary">${label}</td>
                <td class="slt-amount primary">${fmt(amount)}</td>
            </tr>`;
        }
        return `<tr>
            <td class="slt-label primary">${label}</td>
            <td class="slt-amount primary">${numInput(field, amount)}</td>
        </tr>`;
    }

    const editClass = isEditing ? ' preview-edit-active' : '';

    return `<div class="${editClass}">
    <div class="slip-header-band">
        <div class="sh-gov">Republic of the Philippines</div>
        <div class="sh-dept">Province of Camarines Norte · DAET</div>
        <div class="sh-office">Office of the Provincial Agriculturist</div>
        <div class="sh-badge">Pay Slip</div>
    </div>

    <div class="slip-period-bar">
        <span class="spb-label">Payroll Period</span>
        <span class="spb-val">${r.period_label}</span>
    </div>

    <div class="slip-emp-strip">
        <div class="slip-emp-field see-full">
            <span class="sef-label">Employee Name</span>
            <span class="sef-val">${r.emp_name}</span>
        </div>
        <div class="slip-emp-field">
            <span class="sef-label">Employee ID</span>
            <span class="sef-val">${r.employee_id}</span>
        </div>
        <div class="slip-emp-field">
            <span class="sef-label">Position / Designation</span>
            <span class="sef-val">${r.position}</span>
        </div>
    </div>

    <div class="slip-section">
        <div class="slip-section-title">
            <span class="sst-dot" style="background:#16a34a;"></span>
            Earnings &amp; Allowances
        </div>
        <table class="slip-lines-table">
            ${primaryRow('Gross Salary', 'gross_salary', gross)}
            ${row('PERA', 'allowance_pera', pera)}
            ${row('RATA', 'allowance_rata', rata)}
            ${row('Transportation Allowance (TA)', 'allowance_ta', ta)}
            ${row('Other Allowance', 'allowance_other', otherAlw)}
        </table>
    </div>

    <div class="slip-section">
        <div class="slip-section-title">
            <span class="sst-dot" style="background:#2563eb;"></span>
            GSIS Deductions
        </div>
        <table class="slip-lines-table">
            ${row('Employee Share (9%)', 'gsis_ee', r.gsis_ee)}
            ${row('Emergency Contingency Fund (ECF)', 'gsis_ec', r.gsis_ec)}
            ${row('Policy Loan', 'gsis_policy', r.gsis_policy)}
            ${row('Emergency Loan', 'gsis_emergency', r.gsis_emergency)}
            ${row('Real Estate Loan', 'gsis_real_estate', r.gsis_real_estate)}
            ${row('Multi-Purpose Loan (MPL)', 'gsis_mpl', r.gsis_mpl)}
            ${row('MPL Lite', 'gsis_mpl_lite', r.gsis_mpl_lite)}
            ${row('GFAL', 'gsis_gfal', r.gsis_gfal)}
            ${row('Computer Loan', 'gsis_computer', r.gsis_computer)}
            ${row('Consolidated Loan', 'gsis_conso', r.gsis_conso)}
        </table>
    </div>

    <div class="slip-section">
        <div class="slip-section-title">
            <span class="sst-dot" style="background:#ea580c;"></span>
            Pag-Ibig Deductions
        </div>
        <table class="slip-lines-table">
            ${row('Employee Share (₱200)', 'pagibig_govt', r.pagibig_govt)}
            ${row('Multi-Purpose Loan (MPL)', 'pagibig_mpl', r.pagibig_mpl)}
            ${row('Calamity Loan', 'pagibig_calamity', r.pagibig_calamity)}
        </table>
    </div>

    <div class="slip-section">
        <div class="slip-section-title">
            <span class="sst-dot" style="background:#dc2626;"></span>
            PhilHealth &amp; Other Deductions
        </div>
        <table class="slip-lines-table">
            ${row('PhilHealth Employee Share (2.5%)', 'philhealth_ee', r.philhealth_ee)}
            ${row('Withholding Tax', 'withholding_tax', r.withholding_tax)}
            ${row('DBP Loan', 'loan_dbp', r.loan_dbp)}
            ${row('LBP Loan', 'loan_lbp', r.loan_lbp)}
            ${row('CNGWMPC', 'loan_cngwmpc', r.loan_cngwmpc)}
            ${row('PARACLE', 'loan_paracle', r.loan_paracle)}
            ${row('Overpayment', 'overpayment', r.overpayment)}
            <tr>
                <td class="slt-label custom-ded">
                    ${isEditing
                        ? `<input type="text" class="slip-ded-label-input" value="${otherLbl}" placeholder="Custom Deduction">`
                        : otherDed > 0 ? otherLbl : 'Custom Deduction'
                    }
                </td>
                <td class="slt-amount ${otherDed > 0 ? 'custom-ded' : 'zero'}">
                    ${isEditing ? numInput('other_deduction', otherDed) : (otherDed > 0 ? fmt(otherDed) : '—')}
                </td>
            </tr>
        </table>
    </div>

    <div class="slip-totals-band">
        <div class="slip-totals-row">
            <span class="str-label">Total Allowances</span>
            <span class="str-val green" id="slip_total_alw_display">+ ₱${fmt(totalAlw)}</span>
        </div>
        <div class="slip-totals-row">
            <span class="str-label">Total Deductions</span>
            <span class="str-val red" id="slip_total_ded_display">− ₱${fmt(ded)}</span>
        </div>
    </div>

    <div class="slip-net-band">
        <span class="snb-label">Net Pay</span>
        <span class="snb-val" id="slip_net_pay_display">₱${fmt(net)}</span>
    </div>

    <div class="slip-sig-band">
        <div class="ssb-line"></div>
        <div class="ssb-name">${r.sig_name || '—'}</div>
        <div class="ssb-title">${sigTitle}</div>
    </div>
    </div>`;
}

// ── Oninput handler: recalculate live ─────────────────────────────────────
function onSlipInput() {
    const live = getLiveData();
    updateSidebar(live);
}

// ── Enable edit mode on preview ───────────────────────────────────────────
function enablePreviewEdit() {
    if (!currentRecordId || PERIOD_IS_FINALIZED) return;
    editModeActive = true;

    const r = RECORDS[currentRecordId];
    document.getElementById('slipDocCard').innerHTML = buildPayslipHTML(r, true);

    document.getElementById('ppEditNotice').classList.add('visible');
    document.getElementById('sbarEditBtn').style.display   = 'none';
    document.getElementById('sbarSaveBtn').style.display   = 'flex';
    document.getElementById('sbarCancelBtn').style.display = 'flex';
    updateSidebar(r);
}

// ── Cancel edit ───────────────────────────────────────────────────────────
function cancelPreviewEdit() {
    if (!currentRecordId) return;
    editModeActive = false;
    const r = RECORDS[currentRecordId];
    document.getElementById('slipDocCard').innerHTML = buildPayslipHTML(r, false);
    document.getElementById('ppEditNotice').classList.remove('visible');
    document.getElementById('sbarEditBtn').style.display   = '';
    document.getElementById('sbarSaveBtn').style.display   = 'none';
    document.getElementById('sbarCancelBtn').style.display = 'none';
    updateSidebar(r);
}

// ── Save edits via PATCH ──────────────────────────────────────────────────
async function savePreviewEdit() {
    if (!currentRecordId) return;

    const btn = document.getElementById('sbarSaveBtn');
    btn.disabled = true;
    btn.innerHTML = `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;animation:spin .7s linear infinite;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Saving…`;

    const live = getLiveData();
    const payload = {};
    NUMERIC_FIELDS.forEach(f => { payload[f] = live[f] || 0; });
    payload.other_deduction_label = live.other_deduction_label || '';

    const url = `${RECORD_UPDATE_URL}/${currentRecordId}`;

    try {
        const res = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (!res.ok) {
            if (res.status === 403) throw new Error('This payroll period is finalized and cannot be edited.');
            if (res.status === 422 && data.errors) throw new Error(Object.values(data.errors).flat()[0] ?? 'Validation error.');
            throw new Error(data.message ?? data.error ?? 'Save failed.');
        }

        const saved = data.record ?? data;
        // Update ONLY this record in our local store
        Object.assign(RECORDS[currentRecordId], saved);

        editModeActive = false;
        const r = RECORDS[currentRecordId];
        document.getElementById('slipDocCard').innerHTML = buildPayslipHTML(r, false);
        document.getElementById('ppEditNotice').classList.remove('visible');
        document.getElementById('sbarEditBtn').style.display   = '';
        document.getElementById('sbarSaveBtn').style.display   = 'none';
        document.getElementById('sbarCancelBtn').style.display = 'none';
        updateSidebar(r);

        updateTableRow(currentRecordId, r);
        showToast('Payslip saved successfully!', 'success');

    } catch (err) {
        showToast(err.message ?? 'An error occurred.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Save Changes`;
    }
}

// ── Tab switching (only preview tab now) ──────────────────────────────────
function switchPpTab(tab) {
    document.querySelectorAll('.pp-tab').forEach(b => b.classList.remove('active'));
    const btn = document.getElementById('pptab-' + tab);
    if (btn) btn.classList.add('active');
    document.querySelectorAll('.pp-pane').forEach(p => p.classList.remove('active'));
    const pane = document.getElementById('ppPane-' + tab);
    if (pane) pane.classList.add('active');
}

// ── Open Panel ────────────────────────────────────────────────────────────
function openPayslipPanel(recordId) {
    const key = String(recordId);
    const r   = RECORDS[key];

    if (!r) { showToast('Record not found.', 'error'); return; }

    // If we were editing another record, cancel that edit
    if (currentRecordId && currentRecordId !== key && editModeActive) {
        cancelPreviewEdit();
    }

    currentRecordId = key;
    editModeActive  = false;

    // Header
    const parts    = r.emp_name.split(',');
    const lastInit = (parts[0] || '').trim()[0] || '';
    const firstInit= (parts[1] || '').trim()[0] || '';
    document.getElementById('ppAvatar').textContent      = (lastInit + firstInit).toUpperCase() || r.emp_name[0];
    document.getElementById('ppName').textContent        = r.emp_name;
    document.getElementById('ppMeta').textContent        = r.position + ' · ' + r.period_label;
    document.getElementById('ppPeriodBadge').textContent = r.period_label;
    document.getElementById('ppEmpId').textContent       = 'ID: ' + r.employee_id;
    document.getElementById('sbarPdfLink').href          = `/payroll/${r.period_id}/payslip-all-pdf?emp_id=${r.employee_id}`;

    // Build preview (non-edit mode)
    document.getElementById('slipDocCard').innerHTML = buildPayslipHTML(r, false);

    // Sidebar
    document.getElementById('slipSidebar').style.display = '';
    updateSidebar(r);
    document.getElementById('ppEditNotice').classList.remove('visible');
    document.getElementById('sbarSaveBtn').style.display   = 'none';
    document.getElementById('sbarCancelBtn').style.display = 'none';
    document.getElementById('sbarEditBtn').style.display   = '';

    // Edit button: locked if finalized
    const editBtn = document.getElementById('sbarEditBtn');
    if (PERIOD_IS_FINALIZED) {
        editBtn.disabled = true;
        editBtn.style.opacity = '0.45';
        editBtn.innerHTML = `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> Finalized`;
    } else {
        editBtn.disabled = false;
        editBtn.style.opacity = '';
        editBtn.innerHTML = `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Edit Payslip`;
    }

    // Open
    document.getElementById('payslipPanel').classList.add('open');
    document.getElementById('pmOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

// ── Close Panel ───────────────────────────────────────────────────────────
function closePayslipPanel() {
    if (editModeActive) cancelPreviewEdit();
    document.getElementById('payslipPanel').classList.remove('open');
    document.getElementById('pmOverlay').classList.remove('show');
    document.body.style.overflow = '';
    currentRecordId = null;
    editModeActive  = false;
}

// ── Update table row after save ───────────────────────────────────────────
function updateTableRow(recordId, r) {
    document.querySelectorAll('#pmTable tbody tr').forEach(row => {
        if (String(row.dataset.rid) !== String(recordId)) return;
        const cells = row.querySelectorAll('td');
        if (cells[3])  cells[3].textContent = fmt(r.gross_salary);
        if (cells[4])  cells[4].textContent = fmt(r.total_deductions);
        if (cells[5])  cells[5].innerHTML   = `<span class="net-chip">₱${fmt(r.net_pay)}</span>`;
        if (cells[6])  cells[6].textContent = (r.gsis_ee||0) > 0           ? fmt(r.gsis_ee)         : '—';
        if (cells[7])  cells[7].textContent = (r.pagibig_govt||0) > 0      ? fmt(r.pagibig_govt)    : '—';
        if (cells[8])  cells[8].textContent = (r.philhealth_ee||0) > 0     ? fmt(r.philhealth_ee)   : '—';
        if (cells[9])  cells[9].textContent = (r.withholding_tax||0) > 0   ? fmt(r.withholding_tax) : '—';
        if (cells[10]) cells[10].textContent= (r.allowance_pera||0) > 0    ? fmt(r.allowance_pera)  : '—';
    });
}

// ── Search filter ─────────────────────────────────────────────────────────
function filterTable(q) {
    const query = q.toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('#pmTable tbody tr').forEach(row => {
        const match = !query || (row.dataset.name||'').includes(query) || (row.dataset.id||'').includes(query);
        row.classList.toggle('hidden-row', !match);
        if (match) visible++;
    });
    const el = document.getElementById('statCount');
    if (el) el.textContent = visible;
}

// ── Toast ─────────────────────────────────────────────────────────────────
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const icon = type === 'success'
        ? `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`
        : `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = icon + message;
    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3500);
}

// ── Keyboard shortcuts ────────────────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    if (document.getElementById('sigModal').classList.contains('open')) closeSigModal();
    else closePayslipPanel();
});

// ══════════════════════════════════════════════════════════
// SIGNATORY MODAL
// ══════════════════════════════════════════════════════════
function openSigModal() {
    if (!SELECTED_PERIOD_ID) { showToast('Please select a period first.', 'error'); return; }
    const periodSel   = document.getElementById('periodSelect');
    const periodLabel = periodSel ? periodSel.options[periodSel.selectedIndex]?.text : '—';
    document.getElementById('sigModalPeriodLabel').textContent = 'Period: ' + periodLabel;
    document.getElementById('sigModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeSigModal() {
    document.getElementById('sigModal').classList.remove('open');
    document.body.style.overflow = '';
}
document.getElementById('sigModal').addEventListener('click', function(e) {
    if (e.target === this) closeSigModal();
});

async function saveSignatory() {
    const btn      = document.getElementById('sigSaveBtn');
    const nameVal  = document.getElementById('sig_clerk_name').value.trim();
    const titleVal = document.getElementById('sig_clerk_title').value.trim();
    if (!SELECTED_PERIOD_ID) { showToast('No period selected.', 'error'); return; }
    btn.disabled = true; btn.textContent = '…Saving';
    try {
        const res = await fetch(SIGNATORY_URL_BASE + SELECTED_PERIOD_ID + '/signatory', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ sig_clerk_name: nameVal, sig_clerk_title: titleVal }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message ?? data.error ?? 'Save failed');

        Object.values(RECORDS).forEach(r => {
            if (r.period_id === SELECTED_PERIOD_ID) {
                r.sig_name        = nameVal.toUpperCase() || r.sig_name;
                r.sig_clerk_title = titleVal || 'AO V / Payroll Clerk';
            }
        });
        if (currentRecordId) {
            const r = RECORDS[currentRecordId];
            document.getElementById('slipDocCard').innerHTML = buildPayslipHTML(r, false);
        }
        closeSigModal();
        showToast('Signatory updated successfully!', 'success');
    } catch (err) {
        showToast(err.message ?? 'An error occurred.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Save Signatory`;
    }
}
</script>

@endsection