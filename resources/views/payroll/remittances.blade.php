@extends('layouts.app')
@section('title','Remittances')
@section('page-title','Remittances')
@section('content')
<style>
*,*::before,*::after{box-sizing:border-box;}
.breadcrumb{display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:14px;flex-wrap:wrap;}
.breadcrumb a{color:#6b7280;text-decoration:none;}.breadcrumb a:hover{color:#1a3a1a;}
.breadcrumb .sep{color:#d1d5db;}.breadcrumb .current{color:#1a3a1a;font-weight:600;}
.period-bar{display:flex;align-items:center;gap:10px;margin-bottom:18px;flex-wrap:wrap;}
.period-select{appearance:none;-webkit-appearance:none;padding:8px 32px 8px 12px;font-size:13px;font-weight:600;border:1.5px solid #e5e7eb;border-radius:8px;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%239ca3af' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") no-repeat right 10px center;color:#374151;outline:none;cursor:pointer;}

/* Stat Cards */
.stats-row{display:grid;grid-template-columns:1.2fr 1fr 1fr;gap:14px;margin-bottom:22px;}
.stat-card{background:#fff;border-radius:14px;border:1px solid #f0f0f0;padding:20px 22px;display:flex;align-items:center;justify-content:space-between;gap:16px;box-shadow:0 1px 4px rgba(0,0,0,.05);}
.stat-card.dark{background:#1a3a1a;}
.stat-card-label{font-size:12px;font-weight:600;color:#6b7280;margin-bottom:8px;}
.stat-card.dark .stat-card-label{color:rgba(255,255,255,.6);}
.stat-card-value{font-size:28px;font-weight:800;color:#1f2937;letter-spacing:-1px;line-height:1;}
.stat-card.dark .stat-card-value{color:#fff;}
.stat-card-sub{font-size:11px;color:#9ca3af;margin-top:6px;display:flex;align-items:center;gap:4px;}
.stat-card.dark .stat-card-sub{color:rgba(255,255,255,.4);}
.stat-card-sub svg{width:12px;height:12px;}
.stat-icon-wrap{width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.stat-icon-wrap.green{background:#dcfce7;}
.stat-icon-wrap.dark-green{background:rgba(255,255,255,.12);}

/* Tabs */
.rem-tabs-wrap{background:#fff;border-radius:14px;border:1px solid #f0f0f0;box-shadow:0 1px 4px rgba(0,0,0,.05);overflow:hidden;}
.rem-tabs-header{display:flex;align-items:center;border-bottom:1.5px solid #f0f0f0;padding:0 6px;overflow-x:auto;scrollbar-width:none;}
.rem-tabs-header::-webkit-scrollbar{display:none;}
.rem-tab{display:flex;align-items:center;gap:8px;padding:14px 20px;font-size:13px;font-weight:600;color:#6b7280;cursor:pointer;border-bottom:2.5px solid transparent;margin-bottom:-1.5px;white-space:nowrap;transition:color .15s,border-color .15s;background:none;border-top:none;border-left:none;border-right:none;outline:none;}
.rem-tab:hover{color:#1a3a1a;}
.rem-tab.active{color:#1a3a1a;border-bottom-color:#1a3a1a;}
.rem-tab-badge{display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 6px;border-radius:20px;font-size:10px;font-weight:700;background:#f3f4f6;color:#6b7280;}
.rem-tab.active .rem-tab-badge{background:#1a3a1a;color:#fff;}
.rem-tab-panel{display:none;}
.rem-tab-panel.active{display:block;}

/* Panel Toolbar */
.panel-toolbar{padding:14px 16px;border-bottom:1px solid #f9fafb;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;}
.panel-toolbar-title{font-size:13px;font-weight:700;color:#1f2937;margin:0 0 2px;}
.panel-toolbar-sub{font-size:11px;color:#9ca3af;margin:0;}
.panel-toolbar-right{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.search-wrap{position:relative;}
.search-wrap svg{position:absolute;left:9px;top:50%;transform:translateY(-50%);pointer-events:none;color:#9ca3af;}
.search-wrap input{padding:7px 10px 7px 30px;font-size:12px;border:1.5px solid #e5e7eb;border-radius:8px;outline:none;width:200px;transition:border-color .15s;background:#fff;color:#374151;}
.search-wrap input:focus{border-color:#1a3a1a;}
.btn-pdf{display:inline-flex;align-items:center;gap:5px;padding:7px 13px;font-size:11px;font-weight:600;color:#374151;background:#fff;border:1.5px solid #e5e7eb;border-radius:7px;cursor:pointer;text-decoration:none;transition:all .15s;}
.btn-pdf:hover{border-color:#1a3a1a;color:#1a3a1a;}

/* Data Table */
.tsa{overflow-x:auto;}
.data-table{width:100%;border-collapse:collapse;font-size:12.5px;}
.data-table thead tr{background:#fafafa;border-bottom:1px solid #f3f4f6;}
.data-table th{padding:9px 16px;text-align:left;font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap;}
.data-table th.r{text-align:right;}
.data-table td{padding:11px 16px;border-bottom:1px solid #f9fafb;color:#374151;white-space:nowrap;}
.data-table td.r{text-align:right;}
.data-table tbody tr{cursor:pointer;transition:background .12s;}
.data-table tbody tr:hover{background:#f0fdf4;}
.emp-name{font-weight:700;color:#111827;font-size:13px;}
.emp-id{font-size:10px;color:#9ca3af;font-family:monospace;margin-top:1px;}
.amt-red{color:#dc2626;font-weight:700;}
.amt-green{color:#15803d;font-weight:700;}
.amt-blue{color:#1e40af;font-weight:600;}
.amt-purple{color:#5b21b6;font-weight:600;}
.amt-teal{color:#047857;font-weight:600;}
.tfoot-row td{font-weight:700;background:#f9fafb;border-top:2px solid #e5e7eb;border-bottom:none;}

/* Overlay */
#overlay{position:fixed;inset:0;z-index:90;background:rgba(0,0,0,.3);backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);opacity:0;pointer-events:none;transition:opacity .3s;}
#overlay.show{opacity:1;pointer-events:all;}

/* Detail Panel */
#detailPanel{position:fixed;top:0;right:0;bottom:0;z-index:100;width:58vw;min-width:380px;max-width:900px;display:flex;flex-direction:column;pointer-events:none;transform:translateX(100%);transition:transform .36s cubic-bezier(.32,.72,0,1);}
#detailPanel.open{pointer-events:all;transform:translateX(0);}
.detail-box{background:#fff;width:100%;height:100%;display:flex;flex-direction:column;box-shadow:-12px 0 60px rgba(0,0,0,.22);overflow:hidden;}
.detail-header{background:linear-gradient(135deg,#1a3a1a 0%,#2d5a1b 100%);padding:20px 24px 18px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;}
.detail-header h2{font-size:16px;font-weight:700;color:#fff;margin:0 0 3px;}
.detail-header p{font-size:11px;color:rgba(255,255,255,.6);margin:0;}
.detail-close{background:rgba(255,255,255,.15);border:none;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;color:rgba(255,255,255,.8);transition:background .15s;flex-shrink:0;}
.detail-close:hover{background:rgba(255,255,255,.28);color:#fff;}
.detail-body{flex:1;overflow-y:auto;background:#f8f9fa;scrollbar-width:thin;scrollbar-color:#d1d5db transparent;}
.detail-body::-webkit-scrollbar{width:4px;}
.detail-body::-webkit-scrollbar-thumb{background:#d1d5db;border-radius:99px;}
.detail-footer{flex-shrink:0;padding:14px 20px;border-top:1px solid #f3f4f6;background:#fff;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;}

/* DP Cards */
.dp-card{background:#fff;border-radius:12px;margin:14px 16px;padding:18px 18px 16px;box-shadow:0 1px 4px rgba(0,0,0,.06);}
.dp-section-heading{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
.dp-section-icon{width:30px;height:30px;border-radius:8px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;color:#2d5a1b;flex-shrink:0;}
.dp-section-title{font-size:13px;font-weight:700;color:#111827;margin:0;}
.dp-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px 20px;}
.dp-field label{display:block;font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;}
.dp-field p{font-size:13px;color:#111827;font-weight:500;margin:0;}
.dp-mini-table{width:100%;border-collapse:collapse;font-size:12px;}
.dp-mini-table th{padding:6px 10px;font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:left;border-bottom:1px solid #f3f4f6;background:#fafafa;}
.dp-mini-table th.r{text-align:right;}
.dp-mini-table td{padding:7px 10px;border-bottom:1px solid #f9fafb;color:#374151;}
.dp-mini-table td.r{text-align:right;font-weight:600;}
.dp-mini-table tr:last-child td{border-bottom:none;}
.dp-mini-table .subtotal td{background:#f9fafb;font-weight:700;border-top:1.5px solid #e5e7eb;}
.dp-donut-wrap{display:flex;align-items:center;gap:20px;flex-wrap:wrap;}
.dp-donut-canvas{width:160px!important;height:160px!important;flex-shrink:0;}
.dp-legend{display:flex;flex-direction:column;gap:7px;flex:1;min-width:120px;}
.dp-legend-item{display:flex;align-items:center;gap:8px;font-size:11px;color:#374151;}
.dp-legend-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;}
.dp-legend-val{margin-left:auto;font-weight:700;font-size:11px;color:#111827;}
.dp-chart-wrap{position:relative;height:180px;}
.net-display{text-align:center;padding:18px 0 10px;}
.net-display .net-label{font-size:11px;font-weight:700;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;}
.net-display .net-amount{font-size:32px;font-weight:800;color:#4ade80;letter-spacing:-1px;}
.net-display .net-period{font-size:11px;color:rgba(255,255,255,.4);margin-top:4px;}
.dp-pills{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:4px;}
.dp-pill{flex:1;min-width:100px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:10px 14px;}
.dp-pill .pill-lbl{font-size:10px;font-weight:700;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;}
.dp-pill .pill-val{font-size:15px;font-weight:700;color:#fff;}
.dp-pill .pill-val.red{color:#f87171;}
.dp-pill .pill-val.green{color:#86efac;}

#toast{position:fixed;bottom:20px;right:20px;z-index:300;min-width:220px;background:#fff;border-radius:14px;padding:13px 16px;box-shadow:0 8px 32px rgba(0,0,0,.15);display:flex;align-items:center;gap:11px;opacity:0;transform:translateY(14px);transition:all .3s;pointer-events:none;}
#toast.show{opacity:1;transform:translateY(0);}

@media(max-width:900px){.stats-row{grid-template-columns:1fr 1fr;}.stats-row .stat-card:first-child{grid-column:span 2;}}
@media(max-width:600px){.stats-row{grid-template-columns:1fr;}.stats-row .stat-card:first-child{grid-column:span 1;}#detailPanel{width:100%;min-width:0;}}
@media(max-width:767px){#detailPanel{top:auto;height:90vh;border-radius:18px 18px 0 0;transform:translateY(100%);}#detailPanel.open{transform:translateY(0);}.dp-grid{grid-template-columns:1fr;}.dp-donut-wrap{flex-direction:column;}.detail-footer{flex-direction:column;}}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

<div class="breadcrumb">
    <a href="{{ route('payroll.index') }}">Payroll</a>
    <span class="sep">›</span>
    <span class="current">Remittances</span>
</div>

<div class="period-bar">
    <label style="font-size:12px;font-weight:700;color:#6b7280;">Period:</label>
    <form method="GET" style="display:contents;">
        <select name="period_id" class="period-select" onchange="this.form.submit()">
            @foreach($periods as $p)
            <option value="{{ $p->period_id }}" {{ $p->period_id == optional($selectedPeriod)->period_id ? 'selected' : '' }}>
                {{ $p->period_label }}
            </option>
            @endforeach
        </select>
    </form>
</div>

@if($selectedPeriod)
@php
    $empCount   = $records->count();
    $totGross   = $records->sum('gross_salary');
    $totNet     = $records->sum('net_pay');
    $totDed     = $records->sum('total_deductions');
    $totGsisEe  = $records->sum('gsis_ee');
    $totPhic    = $records->sum('philhealth_ee');
    $totPagibig = $records->sum('pagibig_ee');
    $totWtax    = $records->sum('withholding_tax');
    $totPera    = $records->sum('allowance_pera');
@endphp

{{-- Stat Cards --}}
<div class="stats-row">
    <div class="stat-card dark">
        <div>
            <div class="stat-card-label">Total Number of Employees</div>
            <div class="stat-card-value">{{ $empCount }}</div>
            <div class="stat-card-sub">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Active — {{ $selectedPeriod->period_label }}
            </div>
        </div>
        <div class="stat-icon-wrap dark-green">
            <svg style="width:22px;height:22px;color:rgba(255,255,255,.7);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-card-label">Total Gross Salary</div>
            <div class="stat-card-value">₱{{ number_format($totGross, 0) }}</div>
            <div class="stat-card-sub">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                {{ $selectedPeriod->period_label }}
            </div>
        </div>
        <div class="stat-icon-wrap green">
            <svg style="width:22px;height:22px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-card-label">Total Net Pay</div>
            <div class="stat-card-value">₱{{ number_format($totNet, 0) }}</div>
            <div class="stat-card-sub">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                After all deductions
            </div>
        </div>
        <div class="stat-icon-wrap green">
            <svg style="width:22px;height:22px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="rem-tabs-wrap">
    <div class="rem-tabs-header">
        <button class="rem-tab active" onclick="switchTab('all',this)">
            All Employees <span class="rem-tab-badge">{{ $empCount }}</span>
        </button>
        <button class="rem-tab" onclick="switchTab('gsis',this)">GSIS</button>
        <button class="rem-tab" onclick="switchTab('pagibig',this)">PAG-IBIG</button>
        <button class="rem-tab" onclick="switchTab('philhealth',this)">PhilHealth</button>
        <button class="rem-tab" onclick="switchTab('taxloans',this)">Tax &amp; Loans</button>
        <button class="rem-tab" onclick="switchTab('allowances',this)">Allowances</button>
    </div>

    {{-- ALL --}}
    <div class="rem-tab-panel active" id="panel-all">
        <div class="panel-toolbar">
            <div><p class="panel-toolbar-title">Employee Breakdown — {{ $selectedPeriod->period_label }}</p><p class="panel-toolbar-sub">Click any row to view full payroll details</p></div>
            <div class="panel-toolbar-right">
                <div class="search-wrap">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" placeholder="Search employee…" oninput="filterTable('tbl-all',this.value)">
                </div>
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-all">
            <thead><tr>
                <th>Employee</th><th class="r">Gross</th><th class="r">GSIS EE</th><th class="r">PhilHealth</th><th class="r">Pag-Ibig</th><th class="r">W/Tax</th><th class="r">Loans</th><th class="r">PERA</th><th class="r">Total Ded.</th><th class="r">Net Pay</th>
            </tr></thead>
            <tbody>
            @foreach($records as $r)
            @php $rLoans=($r->gsis_mpl??0)+($r->gsis_mpl_lite??0)+($r->gsis_policy??0)+($r->gsis_emergency??0)+($r->gsis_real_estate??0)+($r->gsis_computer??0)+($r->gsis_gfal??0)+($r->pagibig_mpl??0)+($r->pagibig_calamity??0)+($r->loan_dbp??0)+($r->loan_lbp??0)+($r->loan_cngwmpc??0)+($r->loan_paracle??0)+($r->overpayment??0); @endphp
            <tr onclick="openPanel({{ $r->payroll_id }})">
                <td><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->formatted_employee_id ?? $r->employee->employee_id ?? '' }}</div></td>
                <td class="r">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r amt-blue">{{ number_format($r->gsis_ee,2) }}</td>
                <td class="r amt-teal">{{ number_format($r->philhealth_ee,2) }}</td>
                <td class="r amt-purple">{{ number_format($r->pagibig_ee,2) }}</td>
                <td class="r">{{ number_format($r->withholding_tax,2) }}</td>
                <td class="r" style="color:#92400e;">{{ number_format($rLoans,2) }}</td>
                <td class="r amt-green">{{ number_format($r->allowance_pera??0,2) }}</td>
                <td class="r amt-red">{{ number_format($r->total_deductions,2) }}</td>
                <td class="r amt-green">{{ number_format($r->net_pay,2) }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row">
                <td>{{ $empCount }} Employees</td><td class="r">₱{{ number_format($totGross,2) }}</td><td class="r">₱{{ number_format($totGsisEe,2) }}</td><td class="r">₱{{ number_format($totPhic,2) }}</td><td class="r">₱{{ number_format($totPagibig,2) }}</td><td class="r">₱{{ number_format($totWtax,2) }}</td><td class="r">—</td><td class="r">₱{{ number_format($totPera,2) }}</td><td class="r amt-red">₱{{ number_format($totDed,2) }}</td><td class="r amt-green">₱{{ number_format($totNet,2) }}</td>
            </tr></tfoot>
        </table></div>
    </div>

    {{-- GSIS --}}
    <div class="rem-tab-panel" id="panel-gsis">
        <div class="panel-toolbar">
            <div><p class="panel-toolbar-title">GSIS — Government Service Insurance System</p><p class="panel-toolbar-sub">EE contributions, Government share, EC Fund, and all GSIS loans</p></div>
            <div class="panel-toolbar-right">
                <div class="search-wrap"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search…" oninput="filterTable('tbl-gsis',this.value)"></div>
                @if($selectedPeriod)<a href="{{ route('payroll.remittance.pdf', [$selectedPeriod->period_id,'gsis']) }}" target="_blank" class="btn-pdf">📄 Export PDF</a>@endif
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-gsis">
            <thead><tr>
                <th>Employee</th><th class="r">Gross</th><th class="r">EE Share (9%)</th><th class="r">Gov't (12%)</th><th class="r">EC</th><th class="r">MPL</th><th class="r">MPL Lite</th><th class="r">Policy</th><th class="r">Emergency</th><th class="r">Real Estate</th><th class="r">Computer</th><th class="r">GFAL</th><th class="r">Total</th>
            </tr></thead>
            <tbody>
            @foreach($records as $r)
            @php $gsisLoans=($r->gsis_mpl??0)+($r->gsis_mpl_lite??0)+($r->gsis_policy??0)+($r->gsis_emergency??0)+($r->gsis_real_estate??0)+($r->gsis_computer??0)+($r->gsis_gfal??0);$gsisTotal=$r->gsis_ee+($r->gsis_ec??0)+$gsisLoans; @endphp
            <tr onclick="openPanel({{ $r->payroll_id }})">
                <td><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->formatted_employee_id ?? $r->employee->employee_id ?? '' }}</div></td>
                <td class="r">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r amt-blue">{{ number_format($r->gsis_ee,2) }}</td>
                <td class="r" style="color:#6b7280;">{{ number_format($r->gsis_govt??0,2) }}</td>
                <td class="r">{{ number_format($r->gsis_ec??0,2) }}</td>
                <td class="r">{{ number_format($r->gsis_mpl??0,2) }}</td>
                <td class="r">{{ number_format($r->gsis_mpl_lite??0,2) }}</td>
                <td class="r">{{ number_format($r->gsis_policy??0,2) }}</td>
                <td class="r">{{ number_format($r->gsis_emergency??0,2) }}</td>
                <td class="r">{{ number_format($r->gsis_real_estate??0,2) }}</td>
                <td class="r">{{ number_format($r->gsis_computer??0,2) }}</td>
                <td class="r">{{ number_format($r->gsis_gfal??0,2) }}</td>
                <td class="r amt-red">{{ number_format($gsisTotal,2) }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row">
                <td>{{ $empCount }} Employees</td><td class="r">₱{{ number_format($totGross,2) }}</td><td class="r">₱{{ number_format($gsis['ee'],2) }}</td><td class="r">₱{{ number_format($gsis['govt'],2) }}</td><td class="r">₱{{ number_format($gsis['ec'],2) }}</td><td class="r">₱{{ number_format($gsis['mpl'],2) }}</td><td class="r">₱{{ number_format($gsis['mpl_lite'],2) }}</td><td class="r">₱{{ number_format($gsis['policy'],2) }}</td><td class="r">₱{{ number_format($gsis['emergency'],2) }}</td><td class="r">₱{{ number_format($gsis['real_estate'],2) }}</td><td class="r">₱{{ number_format($gsis['computer'],2) }}</td><td class="r">₱{{ number_format($gsis['gfal'],2) }}</td><td class="r amt-red">₱{{ number_format(array_sum($gsis),2) }}</td>
            </tr></tfoot>
        </table></div>
    </div>

    {{-- PAG-IBIG --}}
    <div class="rem-tab-panel" id="panel-pagibig">
        <div class="panel-toolbar">
            <div><p class="panel-toolbar-title">PAG-IBIG — Home Development Mutual Fund (HDMF)</p><p class="panel-toolbar-sub">EE contributions, Government share, MPL, and Calamity loans</p></div>
            <div class="panel-toolbar-right">
                <div class="search-wrap"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search…" oninput="filterTable('tbl-pagibig',this.value)"></div>
                @if($selectedPeriod)<a href="{{ route('payroll.remittance.pdf', [$selectedPeriod->period_id,'pagibig']) }}" target="_blank" class="btn-pdf">📄 Export PDF</a>@endif
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-pagibig">
            <thead><tr>
                <th>Employee</th><th class="r">Gross</th><th class="r">EE Share</th><th class="r">Gov't Share</th><th class="r">MPL</th><th class="r">Calamity Loan</th><th class="r">Total</th>
            </tr></thead>
            <tbody>
            @foreach($records as $r)
            @php $piTotal=$r->pagibig_ee+($r->pagibig_govt??0)+($r->pagibig_mpl??0)+($r->pagibig_calamity??0); @endphp
            <tr onclick="openPanel({{ $r->payroll_id }})">
                <td><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->formatted_employee_id ?? $r->employee->employee_id ?? '' }}</div></td>
                <td class="r">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r amt-purple">{{ number_format($r->pagibig_ee,2) }}</td>
                <td class="r" style="color:#6b7280;">{{ number_format($r->pagibig_govt??0,2) }}</td>
                <td class="r">{{ number_format($r->pagibig_mpl??0,2) }}</td>
                <td class="r">{{ number_format($r->pagibig_calamity??0,2) }}</td>
                <td class="r amt-red">{{ number_format($piTotal,2) }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row">
                <td>{{ $empCount }} Employees</td><td class="r">₱{{ number_format($totGross,2) }}</td><td class="r">₱{{ number_format($pagibig['ee'],2) }}</td><td class="r">₱{{ number_format($pagibig['govt'],2) }}</td><td class="r">₱{{ number_format($pagibig['mpl'],2) }}</td><td class="r">₱{{ number_format($pagibig['calamity'],2) }}</td><td class="r amt-red">₱{{ number_format(array_sum($pagibig),2) }}</td>
            </tr></tfoot>
        </table></div>
    </div>

    {{-- PHILHEALTH --}}
    <div class="rem-tab-panel" id="panel-philhealth">
        <div class="panel-toolbar">
            <div><p class="panel-toolbar-title">PhilHealth — Philippine Health Insurance Corporation (PHIC)</p><p class="panel-toolbar-sub">EE and Government share at 5% each of monthly basic salary</p></div>
            <div class="panel-toolbar-right">
                <div class="search-wrap"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search…" oninput="filterTable('tbl-philhealth',this.value)"></div>
                @if($selectedPeriod)<a href="{{ route('payroll.remittance.pdf', [$selectedPeriod->period_id,'philhealth']) }}" target="_blank" class="btn-pdf">📄 Export PDF</a>@endif
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-philhealth">
            <thead><tr>
                <th>Employee</th><th class="r">Gross Salary</th><th class="r">Monthly Premium</th><th class="r">EE Share (5%)</th><th class="r">Gov't Share (5%)</th><th class="r">Total Contribution</th>
            </tr></thead>
            <tbody>
            @foreach($records as $r)
            <tr onclick="openPanel({{ $r->payroll_id }})">
                <td><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->formatted_employee_id ?? $r->employee->employee_id ?? '' }}</div></td>
                <td class="r">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r">{{ number_format($r->philhealth_ee+($r->philhealth_govt??0),2) }}</td>
                <td class="r amt-teal">{{ number_format($r->philhealth_ee,2) }}</td>
                <td class="r" style="color:#6b7280;">{{ number_format($r->philhealth_govt??0,2) }}</td>
                <td class="r amt-red">{{ number_format($r->philhealth_ee+($r->philhealth_govt??0),2) }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row">
                <td>{{ $empCount }} Employees</td><td class="r">₱{{ number_format($totGross,2) }}</td><td class="r">₱{{ number_format($philhealth['ee']+$philhealth['govt'],2) }}</td><td class="r">₱{{ number_format($philhealth['ee'],2) }}</td><td class="r">₱{{ number_format($philhealth['govt'],2) }}</td><td class="r amt-red">₱{{ number_format(array_sum($philhealth),2) }}</td>
            </tr></tfoot>
        </table></div>
    </div>

    {{-- TAX & LOANS --}}
    <div class="rem-tab-panel" id="panel-taxloans">
        <div class="panel-toolbar">
            <div><p class="panel-toolbar-title">Withholding Tax &amp; Bank Loans</p><p class="panel-toolbar-sub">BIR withholding tax, DBP, LBP, CNGWMPC, and PARACLE</p></div>
            <div class="panel-toolbar-right">
                <div class="search-wrap"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search…" oninput="filterTable('tbl-taxloans',this.value)"></div>
                @if($selectedPeriod)<a href="{{ route('payroll.remittance.pdf', [$selectedPeriod->period_id,'wtax']) }}" target="_blank" class="btn-pdf">📄 Export PDF</a>@endif
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-taxloans">
            <thead><tr>
                <th>Employee</th><th class="r">Gross</th><th class="r">W/Tax</th><th class="r">DBP</th><th class="r">LBP</th><th class="r">CNGWMPC</th><th class="r">PARACLE</th><th class="r">Overpayment</th><th class="r">Total</th>
            </tr></thead>
            <tbody>
            @foreach($records as $r)
            @php $taxTotal=$r->withholding_tax+($r->loan_dbp??0)+($r->loan_lbp??0)+($r->loan_cngwmpc??0)+($r->loan_paracle??0)+($r->overpayment??0); @endphp
            <tr onclick="openPanel({{ $r->payroll_id }})">
                <td><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->formatted_employee_id ?? $r->employee->employee_id ?? '' }}</div></td>
                <td class="r">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r">{{ number_format($r->withholding_tax,2) }}</td>
                <td class="r">{{ number_format($r->loan_dbp??0,2) }}</td>
                <td class="r">{{ number_format($r->loan_lbp??0,2) }}</td>
                <td class="r">{{ number_format($r->loan_cngwmpc??0,2) }}</td>
                <td class="r">{{ number_format($r->loan_paracle??0,2) }}</td>
                <td class="r">{{ number_format($r->overpayment??0,2) }}</td>
                <td class="r amt-red">{{ number_format($taxTotal,2) }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row">
                <td>{{ $empCount }} Employees</td><td class="r">₱{{ number_format($totGross,2) }}</td><td class="r">₱{{ number_format($wtax,2) }}</td><td class="r">₱{{ number_format($loans['dbp'],2) }}</td><td class="r">₱{{ number_format($loans['lbp'],2) }}</td><td class="r">₱{{ number_format($loans['cngwmpc'],2) }}</td><td class="r">₱{{ number_format($loans['paracle'],2) }}</td><td class="r">—</td><td class="r amt-red">₱{{ number_format($wtax+array_sum($loans),2) }}</td>
            </tr></tfoot>
        </table></div>
    </div>

    {{-- ALLOWANCES --}}
    <div class="rem-tab-panel" id="panel-allowances">
        <div class="panel-toolbar">
            <div><p class="panel-toolbar-title">Allowances</p><p class="panel-toolbar-sub">PERA, RATA, and other allowances per employee</p></div>
            <div class="panel-toolbar-right">
                <div class="search-wrap"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg><input type="text" placeholder="Search…" oninput="filterTable('tbl-allowances',this.value)"></div>
            </div>
        </div>
        <div class="tsa"><table class="data-table" id="tbl-allowances">
            <thead><tr>
                <th>Employee</th><th class="r">Gross Salary</th><th class="r">PERA</th><th class="r">RATA</th><th class="r">Other</th><th class="r">Total Allowances</th>
            </tr></thead>
            <tbody>
            @foreach($records as $r)
            @php $totAllow=($r->allowance_pera??0)+($r->allowance_rata??0)+($r->allowance_other??0); @endphp
            <tr onclick="openPanel({{ $r->payroll_id }})">
                <td><div class="emp-name">{{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}</div><div class="emp-id">{{ $r->employee->formatted_employee_id ?? $r->employee->employee_id ?? '' }}</div></td>
                <td class="r">{{ number_format($r->gross_salary,2) }}</td>
                <td class="r amt-green">{{ number_format($r->allowance_pera??0,2) }}</td>
                <td class="r amt-green">{{ number_format($r->allowance_rata??0,2) }}</td>
                <td class="r amt-green">{{ number_format($r->allowance_other??0,2) }}</td>
                <td class="r amt-green" style="font-size:13px;">{{ number_format($totAllow,2) }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr class="tfoot-row">
                <td>{{ $empCount }} Employees</td><td class="r">₱{{ number_format($totGross,2) }}</td><td class="r">₱{{ number_format($totPera,2) }}</td><td class="r">₱{{ number_format($records->sum('allowance_rata'),2) }}</td><td class="r">₱{{ number_format($records->sum('allowance_other'),2) }}</td><td class="r amt-green">₱{{ number_format($records->sum('total_allowances'),2) }}</td>
            </tr></tfoot>
        </table></div>
    </div>

</div>

<script>
const PAYROLL_DATA = {!! json_encode($records->keyBy('payroll_id')->map(function($r) {
    $loans_total = ($r->gsis_mpl??0)+($r->gsis_mpl_lite??0)+($r->gsis_policy??0)+($r->gsis_emergency??0)+($r->gsis_real_estate??0)+($r->gsis_computer??0)+($r->gsis_gfal??0)+($r->pagibig_mpl??0)+($r->pagibig_calamity??0)+($r->loan_dbp??0)+($r->loan_lbp??0)+($r->loan_cngwmpc??0)+($r->loan_paracle??0)+($r->overpayment??0);
    return ['payroll_id'=>$r->payroll_id,'employee_id'=>$r->employee->formatted_employee_id??$r->employee->employee_id??'—','last_name'=>$r->employee->last_name??'—','first_name'=>$r->employee->first_name??'','position'=>optional($r->employee->position)->position_name??'—','department'=>optional($r->employee->department)->department_name??'—','designation'=>$r->designation??'—','period'=>optional($r->period)->period_label??'—','gross_salary'=>(float)$r->gross_salary,'allowance_pera'=>(float)($r->allowance_pera??0),'allowance_rata'=>(float)($r->allowance_rata??0),'allowance_other'=>(float)($r->allowance_other??0),'total_allowances'=>(float)($r->total_allowances??0),'gsis_ee'=>(float)$r->gsis_ee,'gsis_govt'=>(float)($r->gsis_govt??0),'gsis_ec'=>(float)($r->gsis_ec??0),'gsis_mpl'=>(float)($r->gsis_mpl??0),'gsis_mpl_lite'=>(float)($r->gsis_mpl_lite??0),'gsis_policy'=>(float)($r->gsis_policy??0),'gsis_emergency'=>(float)($r->gsis_emergency??0),'gsis_real_estate'=>(float)($r->gsis_real_estate??0),'gsis_computer'=>(float)($r->gsis_computer??0),'gsis_gfal'=>(float)($r->gsis_gfal??0),'pagibig_ee'=>(float)$r->pagibig_ee,'pagibig_govt'=>(float)($r->pagibig_govt??0),'pagibig_mpl'=>(float)($r->pagibig_mpl??0),'pagibig_calamity'=>(float)($r->pagibig_calamity??0),'philhealth_ee'=>(float)$r->philhealth_ee,'philhealth_govt'=>(float)($r->philhealth_govt??0),'withholding_tax'=>(float)$r->withholding_tax,'loan_dbp'=>(float)($r->loan_dbp??0),'loan_lbp'=>(float)($r->loan_lbp??0),'loan_cngwmpc'=>(float)($r->loan_cngwmpc??0),'loan_paracle'=>(float)($r->loan_paracle??0),'overpayment'=>(float)($r->overpayment??0),'total_deductions'=>(float)$r->total_deductions,'net_pay'=>(float)$r->net_pay,'loans_total'=>(float)$loans_total];
})) !!};
</script>

@else
<div style="text-align:center;padding:60px;color:#9ca3af;">Select a payroll period to view remittances.</div>
@endif

<div id="overlay" onclick="closePanel()"></div>
<div id="detailPanel">
    <div class="detail-box">
        <div class="detail-header">
            <div style="min-width:0;"><h2 id="dpTitle">Payroll Details</h2><p id="dpSubtitle">Loading…</p></div>
            <button class="detail-close" onclick="closePanel()"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="detail-body" id="dpBody"></div>
        <div class="detail-footer">
            <span id="dpStatus" style="font-size:11px;color:#9ca3af;"></span>
            <button onclick="closePanel()" style="padding:8px 18px;font-size:12px;font-weight:600;border:1.5px solid #e5e7eb;border-radius:8px;color:#6b7280;background:#fff;cursor:pointer;">Close</button>
        </div>
    </div>
</div>
<div id="toast"><div id="toastIcon" style="width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"></div><div><p style="font-size:13px;font-weight:700;color:#1f2937;margin:0;" id="toastTitle"></p><p style="font-size:11px;color:#6b7280;margin:2px 0 0;" id="toastMsg"></p></div></div>

<script>
function switchTab(name,el){
    document.querySelectorAll('.rem-tab').forEach(t=>t.classList.remove('active'));
    document.querySelectorAll('.rem-tab-panel').forEach(p=>p.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('panel-'+name).classList.add('active');
}
function filterTable(id,q){const sq=q.toLowerCase();document.querySelectorAll('#'+id+' tbody tr').forEach(r=>{r.style.display=r.textContent.toLowerCase().includes(sq)?'':'none';});}
let activeCharts=[];
function destroyCharts(){activeCharts.forEach(c=>{try{c.destroy();}catch(e){}});activeCharts=[];}
function fmt(n){return '₱'+parseFloat(n||0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});}
function pct(part,total){if(!total)return '0.0%';return ((part/total)*100).toFixed(1)+'%';}

function openPanel(id){
    const d=PAYROLL_DATA[id];if(!d)return;
    destroyCharts();
    document.getElementById('dpTitle').textContent=d.last_name+', '+d.first_name;
    document.getElementById('dpSubtitle').textContent=d.position+' · '+d.department;
    document.getElementById('dpStatus').textContent='Period: '+d.period;
    const gsisLoans=d.gsis_mpl+d.gsis_mpl_lite+d.gsis_policy+d.gsis_emergency+d.gsis_real_estate+d.gsis_computer+d.gsis_gfal;
    const pagibigLoans=d.pagibig_mpl+d.pagibig_calamity;
    const otherLoans=d.loan_dbp+d.loan_lbp+d.loan_cngwmpc+d.loan_paracle+d.overpayment;
    const gsisTotal=d.gsis_ee+d.gsis_ec+gsisLoans;
    const pagibigTotal=d.pagibig_ee+pagibigLoans;
    document.getElementById('dpBody').innerHTML=`
    <div class="dp-card" style="background:linear-gradient(135deg,#1a3a1a 0%,#2d5a1b 100%);margin-top:14px;">
        <div class="net-display"><div class="net-label">Net Pay</div><div class="net-amount">${fmt(d.net_pay)}</div><div class="net-period">${d.period}</div></div>
        <div class="dp-pills">
            <div class="dp-pill"><div class="pill-lbl">Gross Salary</div><div class="pill-val">${fmt(d.gross_salary)}</div></div>
            <div class="dp-pill"><div class="pill-lbl">Total Deductions</div><div class="pill-val red">${fmt(d.total_deductions)}</div></div>
            <div class="dp-pill"><div class="pill-lbl">Allowances</div><div class="pill-val green">${fmt(d.total_allowances)}</div></div>
        </div>
    </div>
    <div class="dp-card">
        <div class="dp-section-heading"><div class="dp-section-icon"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div><p class="dp-section-title">Employee Information</p></div>
        <div class="dp-grid">
            <div class="dp-field"><label>Full Name</label><p>${d.last_name}, ${d.first_name}</p></div>
            <div class="dp-field"><label>Employee ID</label><p style="font-family:monospace;">${d.employee_id}</p></div>
            <div class="dp-field"><label>Position</label><p>${d.position}</p></div>
            <div class="dp-field"><label>Department</label><p>${d.department}</p></div>
        </div>
    </div>
    <div class="dp-card">
        <div class="dp-section-heading"><div class="dp-section-icon"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg></div><p class="dp-section-title">Deduction Breakdown</p></div>
        <div class="dp-donut-wrap">
            <canvas id="donutChart" class="dp-donut-canvas"></canvas>
            <div class="dp-legend">
                <div class="dp-legend-item"><div class="dp-legend-dot" style="background:#1a3a1a;"></div><span>GSIS</span><span class="dp-legend-val">${fmt(gsisTotal)}</span></div>
                <div class="dp-legend-item"><div class="dp-legend-dot" style="background:#1e40af;"></div><span>Pag-Ibig</span><span class="dp-legend-val">${fmt(pagibigTotal)}</span></div>
                <div class="dp-legend-item"><div class="dp-legend-dot" style="background:#047857;"></div><span>PhilHealth</span><span class="dp-legend-val">${fmt(d.philhealth_ee)}</span></div>
                <div class="dp-legend-item"><div class="dp-legend-dot" style="background:#7c3aed;"></div><span>W/Tax</span><span class="dp-legend-val">${fmt(d.withholding_tax)}</span></div>
                <div class="dp-legend-item"><div class="dp-legend-dot" style="background:#b45309;"></div><span>Bank Loans</span><span class="dp-legend-val">${fmt(otherLoans)}</span></div>
            </div>
        </div>
    </div>
    <div class="dp-card">
        <div class="dp-section-heading"><div class="dp-section-icon"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div><p class="dp-section-title">Pay Components</p></div>
        <div class="dp-chart-wrap"><canvas id="barChart"></canvas></div>
    </div>
    <div class="dp-card">
        <div class="dp-section-heading"><div class="dp-section-icon" style="background:#e8f4fd;"><svg style="width:14px;height:14px;color:#1a3a1a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div><p class="dp-section-title">GSIS</p></div>
        <table class="dp-mini-table">
            <thead><tr><th>Item</th><th class="r">Amount</th><th class="r">% of Gross</th></tr></thead>
            <tbody>
                <tr><td>EE Share (9%)</td><td class="r">${fmt(d.gsis_ee)}</td><td class="r">${pct(d.gsis_ee,d.gross_salary)}</td></tr>
                <tr><td style="color:#6b7280;">Gov't Share (12%)</td><td class="r" style="color:#6b7280;">${fmt(d.gsis_govt)}</td><td class="r" style="color:#6b7280;">${pct(d.gsis_govt,d.gross_salary)}</td></tr>
                <tr><td>EC Fund</td><td class="r">${fmt(d.gsis_ec)}</td><td class="r">—</td></tr>
                ${d.gsis_mpl?`<tr><td>MPL</td><td class="r">${fmt(d.gsis_mpl)}</td><td class="r">${pct(d.gsis_mpl,d.gross_salary)}</td></tr>`:''}
                ${d.gsis_mpl_lite?`<tr><td>MPL Lite</td><td class="r">${fmt(d.gsis_mpl_lite)}</td><td class="r">${pct(d.gsis_mpl_lite,d.gross_salary)}</td></tr>`:''}
                ${d.gsis_policy?`<tr><td>Policy Loan</td><td class="r">${fmt(d.gsis_policy)}</td><td class="r">${pct(d.gsis_policy,d.gross_salary)}</td></tr>`:''}
                ${d.gsis_emergency?`<tr><td>Emergency Loan</td><td class="r">${fmt(d.gsis_emergency)}</td><td class="r">${pct(d.gsis_emergency,d.gross_salary)}</td></tr>`:''}
                ${d.gsis_real_estate?`<tr><td>Real Estate</td><td class="r">${fmt(d.gsis_real_estate)}</td><td class="r">${pct(d.gsis_real_estate,d.gross_salary)}</td></tr>`:''}
                ${d.gsis_computer?`<tr><td>Computer Loan</td><td class="r">${fmt(d.gsis_computer)}</td><td class="r">${pct(d.gsis_computer,d.gross_salary)}</td></tr>`:''}
                ${d.gsis_gfal?`<tr><td>GFAL</td><td class="r">${fmt(d.gsis_gfal)}</td><td class="r">${pct(d.gsis_gfal,d.gross_salary)}</td></tr>`:''}
                <tr class="subtotal"><td>Total GSIS</td><td class="r">${fmt(gsisTotal)}</td><td class="r">${pct(gsisTotal,d.gross_salary)}</td></tr>
            </tbody>
        </table>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin:0 16px 14px;">
        <div class="dp-card" style="margin:0;">
            <div class="dp-section-heading"><div class="dp-section-icon" style="background:#ede9fe;"><svg style="width:14px;height:14px;color:#5b21b6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></div><p class="dp-section-title">Pag-Ibig</p></div>
            <table class="dp-mini-table"><thead><tr><th>Item</th><th class="r">Amount</th></tr></thead><tbody>
                <tr><td>EE Share</td><td class="r">${fmt(d.pagibig_ee)}</td></tr>
                <tr><td style="color:#6b7280;">Gov't Share</td><td class="r" style="color:#6b7280;">${fmt(d.pagibig_govt)}</td></tr>
                ${d.pagibig_mpl?`<tr><td>MPL</td><td class="r">${fmt(d.pagibig_mpl)}</td></tr>`:''}
                ${d.pagibig_calamity?`<tr><td>Calamity Loan</td><td class="r">${fmt(d.pagibig_calamity)}</td></tr>`:''}
                <tr class="subtotal"><td>Total</td><td class="r">${fmt(pagibigTotal)}</td></tr>
            </tbody></table>
        </div>
        <div class="dp-card" style="margin:0;">
            <div class="dp-section-heading"><div class="dp-section-icon" style="background:#d1fae5;"><svg style="width:14px;height:14px;color:#047857;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div><p class="dp-section-title">PhilHealth</p></div>
            <table class="dp-mini-table"><thead><tr><th>Item</th><th class="r">Amount</th></tr></thead><tbody>
                <tr><td>EE Share (5%)</td><td class="r">${fmt(d.philhealth_ee)}</td></tr>
                <tr><td style="color:#6b7280;">Gov't Share (5%)</td><td class="r" style="color:#6b7280;">${fmt(d.philhealth_govt)}</td></tr>
                <tr class="subtotal"><td>Total Premium</td><td class="r">${fmt(d.philhealth_ee+d.philhealth_govt)}</td></tr>
            </tbody></table>
        </div>
    </div>
    <div class="dp-card">
        <div class="dp-section-heading"><div class="dp-section-icon" style="background:#fef3c7;"><svg style="width:14px;height:14px;color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><p class="dp-section-title">Tax &amp; Bank Loans</p></div>
        <table class="dp-mini-table"><thead><tr><th>Item</th><th class="r">Amount</th></tr></thead><tbody>
            <tr><td>Withholding Tax</td><td class="r">${fmt(d.withholding_tax)}</td></tr>
            ${d.loan_dbp?`<tr><td>DBP Loan</td><td class="r">${fmt(d.loan_dbp)}</td></tr>`:''}
            ${d.loan_lbp?`<tr><td>LBP Loan</td><td class="r">${fmt(d.loan_lbp)}</td></tr>`:''}
            ${d.loan_cngwmpc?`<tr><td>CNGWMPC</td><td class="r">${fmt(d.loan_cngwmpc)}</td></tr>`:''}
            ${d.loan_paracle?`<tr><td>PARACLE</td><td class="r">${fmt(d.loan_paracle)}</td></tr>`:''}
            ${d.overpayment?`<tr><td>Overpayment</td><td class="r">${fmt(d.overpayment)}</td></tr>`:''}
            <tr class="subtotal"><td>Total</td><td class="r">${fmt(d.withholding_tax+otherLoans)}</td></tr>
        </tbody></table>
    </div>
    <div class="dp-card" style="margin-bottom:20px;">
        <div class="dp-section-heading"><div class="dp-section-icon"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div><p class="dp-section-title">Pay Summary</p></div>
        <table class="dp-mini-table"><tbody>
            <tr><td style="color:#6b7280;">Gross Salary</td><td class="r">${fmt(d.gross_salary)}</td></tr>
            <tr><td style="color:#6b7280;">+ Total Allowances</td><td class="r" style="color:#15803d;">${fmt(d.total_allowances)}</td></tr>
            <tr><td style="color:#6b7280;">− Total Deductions</td><td class="r" style="color:#dc2626;">${fmt(d.total_deductions)}</td></tr>
            <tr class="subtotal"><td style="font-size:13px;">NET PAY</td><td class="r" style="font-size:14px;color:#15803d;">${fmt(d.net_pay)}</td></tr>
        </tbody></table>
    </div>`;

    const donutValues=[gsisTotal,pagibigTotal,d.philhealth_ee,d.withholding_tax,otherLoans].map(v=>Math.max(v,0));
    const hasData=donutValues.some(v=>v>0);
    const donut=new Chart(document.getElementById('donutChart').getContext('2d'),{type:'doughnut',data:{labels:['GSIS','Pag-Ibig','PhilHealth','W/Tax','Bank Loans'],datasets:[{data:hasData?donutValues:[1],backgroundColor:hasData?['#1a3a1a','#1e40af','#047857','#7c3aed','#b45309']:['#f3f4f6'],borderWidth:2,borderColor:'#fff',hoverOffset:6}]},options:{responsive:false,cutout:'68%',plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>' '+ctx.label+': '+fmt(ctx.raw)}}}}});
    activeCharts.push(donut);
    const bar=new Chart(document.getElementById('barChart').getContext('2d'),{type:'bar',data:{labels:['Gross','PERA','RATA','Other','Total Ded.','Net Pay'],datasets:[{data:[d.gross_salary,d.allowance_pera,d.allowance_rata,d.allowance_other,d.total_deductions,d.net_pay],backgroundColor:['#1a3a1a','#047857','#0891b2','#6366f1','#dc2626','#15803d'],borderRadius:5,borderSkipped:false}]},options:{responsive:true,maintainAspectRatio:true,aspectRatio:2.4,plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>' '+fmt(ctx.raw)}}},scales:{y:{ticks:{callback:v=>'₱'+(v/1000).toFixed(0)+'k',font:{size:10}},grid:{color:'#f3f4f6'}},x:{ticks:{font:{size:10}},grid:{display:false}}}}});
    activeCharts.push(bar);

    document.getElementById('overlay').classList.add('show');
    document.getElementById('detailPanel').classList.add('open');
    document.body.style.overflow='hidden';
}

function closePanel(){
    document.getElementById('detailPanel').classList.remove('open');
    document.getElementById('overlay').classList.remove('show');
    document.body.style.overflow='';
    setTimeout(destroyCharts,380);
}
document.addEventListener('keydown',e=>{if(e.key==='Escape')closePanel();});
</script>
@endsection