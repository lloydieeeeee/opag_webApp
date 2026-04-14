@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php
    $periodLabel = $period?->period_label ?? now()->format('F Y');
    $totalEmployees  = $totalEmployees  ?? 0;
    $totalGross      = (float)($totalGross      ?? 0);
    $totalNet        = (float)($totalNet        ?? 0);
    $totalDeductions = (float)($totalDeductions ?? 0);
    $totalGsis       = (float)($totalGsis       ?? 0);
    $totalWtax       = (float)($totalWtax       ?? 0);
    $totalPhilhealth = (float)($totalPhilhealth ?? 0);
    $totalPagibig    = (float)($totalPagibig    ?? 0);
    $payrollData     = $payrollData ?? [];
    $pendingLeave    = $pendingLeave ?? 0;
    $pendingHalfDay  = $pendingHalfDay ?? 0;
@endphp

{{-- ══════════════════════════════════════════════
     STAT CARDS
══════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    {{-- Card 1: Total Employees --}}
    <div id="card1"
         class="rounded-xl p-5 flex items-center justify-between shadow-md opacity-0 translate-y-4 transition-all duration-500 ease-out"
         style="background-color:#1a3a1a;">
        <div>
            <p class="text-sm font-medium mb-1" style="color:rgba(255,255,255,0.7);">Total Number of Employee</p>
            <p class="text-white text-4xl font-bold" id="countEmployees" data-target="{{ $totalEmployees }}">0</p>
            <p class="text-xs mt-2 flex items-center gap-1" style="color:#86efac;">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Active — {{ $periodLabel }}
            </p>
        </div>
        <div class="w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0" style="background:rgba(255,255,255,0.15);">
            <svg class="w-7 h-7" style="color:#86efac;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857
                         M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857
                         m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
    </div>

    {{-- Card 2: Total Gross Salary --}}
    <div id="card2"
         class="bg-white rounded-xl p-5 flex items-center justify-between shadow-md border border-gray-100 opacity-0 translate-y-4 transition-all duration-500 ease-out">
        <div>
            <p class="text-gray-500 text-sm font-medium mb-1">Total Gross Salary</p>
            <p class="text-gray-800 text-3xl font-bold">₱{{ number_format($totalGross, 0) }}</p>
            <p class="text-xs mt-2 flex items-center gap-1" style="color:#16a34a;">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                {{ $periodLabel }}
            </p>
        </div>
        <div class="w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0" style="background:#dcfce7;">
            <svg class="w-7 h-7" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0
                         002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
    </div>

    {{-- Card 3: Total Net Pay --}}
    <div id="card3"
         class="bg-white rounded-xl p-5 flex items-center justify-between shadow-md border border-gray-100 opacity-0 translate-y-4 transition-all duration-500 ease-out">
        <div>
            <p class="text-gray-500 text-sm font-medium mb-1">Total Net Pay</p>
            <p class="text-gray-800 text-3xl font-bold">₱{{ number_format($totalNet, 0) }}</p>
            <p class="text-xs mt-2 flex items-center gap-1" style="color:#16a34a;">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                After all deductions
            </p>
        </div>
        <div class="w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0" style="background:#dcfce7;">
            <svg class="w-7 h-7" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
            </svg>
        </div>
    </div>
</div>

{{-- Pending approvals banner --}}
@if($pendingLeave > 0 || $pendingHalfDay > 0)
<div class="rounded-xl px-5 py-3 mb-6 flex items-center gap-3 shadow-sm"
     style="background:#fef9c3; border:1px solid #fde68a;">
    <svg class="w-5 h-5 flex-shrink-0" style="color:#854d0e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-sm font-medium" style="color:#854d0e;">
        @if($pendingLeave > 0)<strong>{{ $pendingLeave }}</strong> leave application{{ $pendingLeave > 1 ? 's' : '' }} awaiting approval.@endif
        @if($pendingLeave > 0 && $pendingHalfDay > 0) &nbsp;·&nbsp; @endif
        @if($pendingHalfDay > 0)<strong>{{ $pendingHalfDay }}</strong> half-day pending.@endif
    </p>
</div>
@endif

{{-- ══════════════════════════════════════════════
     AREA CHART
══════════════════════════════════════════════ --}}
<div id="chartCard"
     class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mb-6 opacity-0 translate-y-4 transition-all duration-500 ease-out">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-1 pb-4"
         style="border-bottom:1px solid #f3f4f6;">
        <div>
            <h3 class="text-base font-bold text-gray-800">Payroll Overview</h3>
            <p class="text-xs text-gray-400 mt-0.5">Gross vs Net Pay — {{ $periodLabel }}</p>
        </div>
        <div class="relative">
            <select id="chartFilter"
                    class="appearance-none text-sm border border-gray-200 rounded-lg px-4 py-1.5 pr-8 text-gray-600 focus:outline-none cursor-pointer"
                    style="background:#f0fdf4;">
                <option value="all">All Employees</option>
                <option value="top10">Top 10 by Gross</option>
                <option value="bottom10">Bottom 10 by Gross</option>
            </select>
            <svg class="w-4 h-4 text-gray-400 absolute right-2 top-2 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>
    <div class="pt-4">
        <canvas id="payrollChart"></canvas>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     BOTTOM ROW
══════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ── Employee Payroll Summary Table ── --}}
    <div id="activityCard"
         class="bg-white rounded-xl shadow-md border border-gray-100 p-6 opacity-0 translate-y-4 transition-all duration-500 ease-out">
        <h3 class="text-base font-bold text-gray-800 mb-4">Employee Payroll Summary</h3>
        <div class="overflow-auto" style="max-height:340px;">
            <table class="w-full text-sm">
                <thead class="sticky top-0 bg-white">
                    <tr style="border-bottom:2px solid #f3f4f6;">
                        <th class="text-left py-2 px-2 text-xs font-semibold text-gray-400 uppercase">Name</th>
                        <th class="text-right py-2 px-2 text-xs font-semibold text-gray-400 uppercase">Gross</th>
                        <th class="text-right py-2 px-2 text-xs font-semibold text-gray-400 uppercase hidden sm:table-cell">Deductions</th>
                        <th class="text-right py-2 px-2 text-xs font-semibold text-gray-400 uppercase">Net Pay</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrollData as $i => $emp)
                    @php
                        $empTotDed = ($emp['gsis'] ?? 0) + ($emp['pagibig'] ?? 0) + ($emp['phic'] ?? 0) + ($emp['wtax'] ?? 0);
                    @endphp
                    <tr class="employee-row opacity-0 translate-x-3 transition-all duration-300 group cursor-pointer"
                        style="border-bottom:1px solid #f9fafb;"
                        data-index="{{ $i }}"
                        data-emp="{{ json_encode($emp) }}">
                        <td class="py-2 px-2">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                     style="background:#2d5a1b;">
                                    {{ strtoupper(substr($emp['name'] ?? 'E', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700 text-xs leading-tight">{{ $emp['name'] ?? '—' }}</p>
                                    <p class="text-gray-400" style="font-size:10px;">{{ $emp['designation'] ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-2 px-2 text-right text-xs font-medium text-gray-700">
                            ₱{{ number_format($emp['gross'] ?? 0, 0) }}
                        </td>
                        <td class="py-2 px-2 text-right text-xs font-medium text-red-400 hidden sm:table-cell">
                            −₱{{ number_format($empTotDed, 0) }}
                        </td>
                        <td class="py-2 px-2 text-right text-xs font-bold" style="color:#16a34a;">
                            ₱{{ number_format($emp['net'] ?? 0, 0) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-xs text-gray-400">
                            No payroll data for {{ $periodLabel }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Deductions Breakdown Donut ── --}}
    <div id="donutCard"
         class="bg-white rounded-xl shadow-md border border-gray-100 p-6 opacity-0 translate-y-4 transition-all duration-500 ease-out">
        <h3 class="text-base font-bold text-gray-800 mb-1">Deductions Breakdown</h3>
        <p class="text-xs text-gray-400 mb-4">{{ $periodLabel }} — All Employees</p>

        <div class="flex flex-col sm:flex-row items-center gap-6">
            {{-- Donut --}}
            <div class="relative flex-shrink-0" style="width:200px;height:200px;">
                <canvas id="donutChart" width="200" height="200"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <p class="text-xs text-gray-400">Total</p>
                    <p class="text-sm font-bold text-gray-800">₱{{ number_format($totalDeductions, 0) }}</p>
                </div>
            </div>

            {{-- Legend with formulas --}}
            <div class="space-y-4 flex-1">
                @php
                $donutRows = [
                    [
                        'label'   => 'GSIS',
                        'formula' => 'Salary × 9%',
                        'val'     => $totalGsis,
                        'color'   => '#1a3a1a',
                        'pct'     => $totalDeductions > 0 ? round(($totalGsis / $totalDeductions) * 100, 1) : 0,
                    ],
                    [
                        'label'   => 'Withholding Tax',
                        'formula' => 'BIR 2023 bracket',
                        'val'     => $totalWtax,
                        'color'   => '#3d7a2a',
                        'pct'     => $totalDeductions > 0 ? round(($totalWtax / $totalDeductions) * 100, 1) : 0,
                    ],
                    [
                        'label'   => 'PhilHealth',
                        'formula' => '(Salary × 5%) ÷ 2',
                        'val'     => $totalPhilhealth,
                        'color'   => '#86efac',
                        'pct'     => $totalDeductions > 0 ? round(($totalPhilhealth / $totalDeductions) * 100, 1) : 0,
                    ],
                    [
                        'label'   => 'PAG-IBIG',
                        'formula' => 'min(Salary × 2%, ₱100)',
                        'val'     => $totalPagibig,
                        'color'   => '#a3e635',
                        'pct'     => $totalDeductions > 0 ? round(($totalPagibig / $totalDeductions) * 100, 1) : 0,
                    ],
                ];
                @endphp

                @foreach($donutRows as $d)
                <div>
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-2.5 h-2.5 rounded-full flex-shrink-0 mt-0.5" style="background:{{ $d['color'] }};"></div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-700 leading-tight">{{ $d['label'] }}</p>
                                <p class="text-gray-400 leading-tight" style="font-size:10px;">{{ $d['formula'] }}</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs font-bold text-gray-700">₱{{ number_format($d['val'], 0) }}</p>
                            <p class="text-gray-400" style="font-size:10px;">{{ $d['pct'] }}%</p>
                        </div>
                    </div>
                    <div class="w-full rounded-full h-1" style="background:#f3f4f6;">
                        <div class="h-1 rounded-full donut-bar" style="background:{{ $d['color'] }}; width:0%;"
                             data-w="{{ $d['pct'] }}%"></div>
                    </div>
                </div>
                @endforeach

                {{-- Net Pay formula note --}}
                <div class="rounded-lg px-3 py-2.5 mt-2" style="background:#f0fdf4; border:1px solid #dcfce7;">
                    <p class="text-xs font-semibold mb-0.5" style="color:#1a3a1a;">Net Pay Formula (Excel col AF)</p>
                    <p class="text-xs leading-relaxed" style="color:#3d7a2a;">
                        Gross − GSIS − PAG-IBIG − PhilHealth − W.Tax − Loans + Allowances
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════
     EMPLOYEE DETAIL DRAWER (slide-in)
══════════════════════════════════════════════ --}}
<div id="empDrawer"
     class="fixed inset-y-0 right-0 z-50 w-80 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col"
     style="border-left:1px solid #e5e7eb;">
    <div class="flex items-center justify-between px-5 py-4" style="background:#1a3a1a;">
        <h4 class="text-sm font-bold text-white">Payroll Detail</h4>
        <button id="closeDrawer" class="text-white opacity-70 hover:opacity-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <div class="flex-1 overflow-y-auto p-5" id="drawerContent">
        {{-- filled by JS --}}
    </div>
</div>
<div id="drawerBackdrop" class="fixed inset-0 bg-black opacity-0 pointer-events-none z-40 transition-opacity duration-300"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Fade-in cards ──────────────────────────────────────────────
    [
        {id:'card1',delay:100},{id:'card2',delay:200},{id:'card3',delay:300},
        {id:'chartCard',delay:400},{id:'activityCard',delay:500},{id:'donutCard',delay:600},
    ].forEach(({id,delay}) => setTimeout(() => {
        const el = document.getElementById(id);
        if (el) { el.classList.remove('opacity-0','translate-y-4'); el.classList.add('opacity-100','translate-y-0'); }
    }, delay));

    // ── Count-up ───────────────────────────────────────────────────
    const countEl = document.getElementById('countEmployees');
    if (countEl) {
        const target = parseInt(countEl.dataset.target) || 0;
        let c = 0;
        const step = Math.max(1, Math.ceil(target / 40));
        const tm = setInterval(() => { c = Math.min(c + step, target); countEl.textContent = c; if (c >= target) clearInterval(tm); }, 40);
    }

    // ── Employee rows animate ──────────────────────────────────────
    document.querySelectorAll('.employee-row').forEach((el, i) => {
        setTimeout(() => { el.classList.remove('opacity-0','translate-x-3'); el.classList.add('opacity-100','translate-x-0'); }, 600 + i * 35);
    });

    // ── Donut bars animate ─────────────────────────────────────────
    setTimeout(() => {
        document.querySelectorAll('.donut-bar').forEach(b => { b.style.width = b.dataset.w + '%'; });
    }, 800);

    // ── Employee detail drawer ─────────────────────────────────────
    const drawer   = document.getElementById('empDrawer');
    const backdrop = document.getElementById('drawerBackdrop');
    const content  = document.getElementById('drawerContent');

    function openDrawer(emp) {
        const totDed = (emp.gsis||0)+(emp.pagibig||0)+(emp.phic||0)+(emp.wtax||0);
        const fmt = v => '₱' + parseFloat(v||0).toLocaleString('en-PH',{minimumFractionDigits:2});
        const pct = v => totDed > 0 ? (v/totDed*100).toFixed(1)+'%' : '0%';
        const rows = [
            {label:'Gross Salary',   formula:'',              val:emp.gross||0,   type:'gross'},
            {label:'GSIS',           formula:'× 9%',          val:emp.gsis||0,    type:'ded', color:'#1a3a1a'},
            {label:'PAG-IBIG',       formula:'× 2% max ₱100', val:emp.pagibig||0, type:'ded', color:'#a3e635'},
            {label:'PhilHealth',     formula:'× 5% ÷ 2',      val:emp.phic||0,    type:'ded', color:'#86efac'},
            {label:'Withholding Tax',formula:'BIR bracket',   val:emp.wtax||0,    type:'ded', color:'#3d7a2a'},
        ];

        let html = `
            <div class="flex items-center gap-3 mb-5">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-lg font-bold" style="background:#1a3a1a;">
                    ${(emp.name||'E')[0].toUpperCase()}
                </div>
                <div>
                    <p class="font-bold text-gray-800 text-sm leading-tight">${emp.name||'—'}</p>
                    <p class="text-xs text-gray-400">${emp.designation||''}</p>
                </div>
            </div>
            <div class="space-y-0 mb-4">`;

        rows.forEach(r => {
            html += `<div class="flex justify-between items-center py-2.5" style="border-bottom:1px solid #f9fafb;">
                <div>
                    <p class="text-xs font-semibold ${r.type==='ded' ? 'text-gray-600' : 'text-gray-800'}">${r.label}</p>
                    ${r.formula ? `<p class="text-gray-400" style="font-size:10px;">Salary ${r.formula}</p>` : ''}
                </div>
                <p class="text-xs font-bold ${r.type==='ded' ? 'text-red-400' : 'text-gray-800'}">${r.type==='ded'?'−':''}${fmt(r.val)}</p>
            </div>`;
        });

        // Stacked bars
        html += `</div><div class="space-y-2 mb-4">`;
        rows.filter(r=>r.type==='ded').forEach(r => {
            html += `<div>
                <div class="flex justify-between mb-0.5">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full" style="background:${r.color};"></div>
                        <span class="text-xs text-gray-600">${r.label}</span>
                    </div>
                    <span class="text-xs text-gray-500">${pct(r.val)}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full" style="background:${r.color};width:${pct(r.val)};"></div>
                </div>
            </div>`;
        });

        html += `</div>
            <div class="rounded-lg px-4 py-3 mt-2" style="background:#f0fdf4;">
                <div class="flex justify-between items-center">
                    <p class="text-sm font-bold" style="color:#1a3a1a;">NET PAY</p>
                    <p class="text-sm font-bold" style="color:#1a3a1a;">${fmt(emp.net||0)}</p>
                </div>
                <p class="text-xs mt-1" style="color:#3d7a2a;">Gross − GSIS − PAG-IBIG − PhilHealth − W.Tax</p>
            </div>`;

        content.innerHTML = html;
        drawer.classList.remove('translate-x-full');
        backdrop.classList.remove('opacity-0','pointer-events-none');
        backdrop.classList.add('opacity-30','pointer-events-auto');
    }

    function closeDrawer() {
        drawer.classList.add('translate-x-full');
        backdrop.classList.remove('opacity-30','pointer-events-auto');
        backdrop.classList.add('opacity-0','pointer-events-none');
    }

    document.querySelectorAll('.employee-row').forEach(row => {
        row.addEventListener('click', () => {
            try { openDrawer(JSON.parse(row.dataset.emp)); } catch(e) {}
        });
    });
    document.getElementById('closeDrawer').addEventListener('click', closeDrawer);
    backdrop.addEventListener('click', closeDrawer);

    // ── Area Chart ─────────────────────────────────────────────────
    const allEmployees = @json($payrollData);
    const sorted = [...allEmployees].sort((a,b) => (b.gross||0)-(a.gross||0));
    const ds = { all: allEmployees, top10: sorted.slice(0,10), bottom10: sorted.slice(-10).reverse() };

    function buildData(data) {
        return {
            labels: data.map(e => (e.name||'').split(',')[0].trim()),
            gross:  data.map(e => parseFloat(e.gross)||0),
            net:    data.map(e => parseFloat(e.net)||0),
        };
    }

    const ctx = document.getElementById('payrollChart').getContext('2d');
    const g1 = ctx.createLinearGradient(0,0,0,300);
    g1.addColorStop(0,'rgba(61,122,42,0.8)'); g1.addColorStop(1,'rgba(61,122,42,0)');
    const g2 = ctx.createLinearGradient(0,0,0,300);
    g2.addColorStop(0,'rgba(163,230,53,0.7)'); g2.addColorStop(1,'rgba(163,230,53,0)');

    const isMobile = () => window.innerWidth < 640;
    let init = buildData(ds.all);

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: init.labels,
            datasets: [
                {label:'Gross Salary', data:init.gross, fill:true, backgroundColor:g1, borderColor:'#3d7a2a',
                 borderWidth:2, pointRadius:isMobile()?0:3, pointHoverRadius:5, pointBackgroundColor:'#3d7a2a', tension:0.4},
                {label:'Net Pay', data:init.net, fill:true, backgroundColor:g2, borderColor:'#a3e635',
                 borderWidth:2, pointRadius:isMobile()?0:3, pointHoverRadius:5, pointBackgroundColor:'#a3e635', tension:0.4},
            ]
        },
        options: {
            responsive:true, maintainAspectRatio:true, aspectRatio:isMobile()?1.4:2.5,
            interaction:{mode:'index',intersect:false},
            animation:{duration:800,easing:'easeInOutQuart'},
            plugins:{
                legend:{position:'top',labels:{font:{size:12},color:'#4b5563',usePointStyle:true}},
                tooltip:{callbacks:{label:c=>' ₱'+c.raw.toLocaleString('en-PH',{minimumFractionDigits:0})}}
            },
            scales:{
                x:{grid:{display:false},ticks:{color:'#9ca3af',font:{size:10},maxRotation:45,autoSkip:true,maxTicksLimit:isMobile()?6:20}},
                y:{grid:{color:'rgba(0,0,0,0.04)'},ticks:{color:'#9ca3af',font:{size:11},callback:v=>'₱'+(v/1000).toFixed(0)+'k',maxTicksLimit:6},beginAtZero:true}
            }
        }
    });

    document.getElementById('chartFilter').addEventListener('change', function() {
        const d = buildData(ds[this.value]), m = isMobile();
        chart.data.labels = d.labels;
        chart.data.datasets[0].data = d.gross; chart.data.datasets[1].data = d.net;
        chart.data.datasets[0].pointRadius = m?0:3; chart.data.datasets[1].pointRadius = m?0:3;
        chart.options.scales.x.ticks.maxTicksLimit = m?6:20;
        chart.options.aspectRatio = m?1.4:2.5; chart.update();
    });

    let rt;
    window.addEventListener('resize', () => { clearTimeout(rt); rt = setTimeout(() => {
        const m = isMobile();
        chart.data.datasets[0].pointRadius = m?0:3; chart.data.datasets[1].pointRadius = m?0:3;
        chart.options.scales.x.ticks.maxTicksLimit = m?6:20;
        chart.options.aspectRatio = m?1.4:2.5; chart.update();
    }, 200); });

    // ── Deductions Donut ───────────────────────────────────────────
    new Chart(document.getElementById('donutChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['GSIS (9%)','W-Tax (BIR)','PhilHealth (5%/2)','PAG-IBIG (2%/₱100)'],
            datasets:[{
                data:[{{ $totalGsis }},{{ $totalWtax }},{{ $totalPhilhealth }},{{ $totalPagibig }}],
                backgroundColor:['#1a3a1a','#3d7a2a','#86efac','#a3e635'],
                borderWidth:0, hoverOffset:8,
            }]
        },
        options:{
            responsive:false, cutout:'68%',
            animation:{animateRotate:true,duration:1000,easing:'easeInOutQuart'},
            plugins:{
                legend:{display:false},
                tooltip:{callbacks:{label:c=>' ₱'+c.raw.toLocaleString('en-PH',{minimumFractionDigits:2})}}
            }
        }
    });
});
</script>
@endsection