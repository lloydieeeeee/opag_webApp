<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Leave Card — {{ strtoupper($employee->last_name) }}, {{ strtoupper($employee->first_name) }} — {{ $year }}</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10pt;
    color: #111;
    background: #e8ecee;
    padding: 24px 16px 48px;
}

/* ── Toolbar ── */
.toolbar {
    max-width: 1100px; margin: 0 auto 16px;
    background: #fff; border-radius: 10px;
    box-shadow: 0 1px 6px rgba(0,0,0,.10); border: 1px solid #e5e7eb;
    padding: 10px 16px; display: flex; align-items: center;
    justify-content: space-between; gap: 12px;
}
.toolbar-left  { display: flex; align-items: center; gap: 10px; }
.toolbar-icon  { width:34px; height:34px; border-radius:8px; background:linear-gradient(135deg,#1a3a1a,#2d5a1b); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.toolbar-title { font-size:13px; font-weight:700; color:#1f2937; }
.toolbar-sub   { font-size:11px; color:#9ca3af; margin-top:1px; }
.toolbar-actions { display:flex; gap:8px; }
.btn { display:inline-flex; align-items:center; gap:6px; padding:7px 16px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; border:none; transition:all .15s; }
.btn-primary   { background:linear-gradient(135deg,#1a3a1a,#2d5a1b); color:#fff; }
.btn-primary:hover { opacity:.88; }
.btn-secondary { background:#f3f4f6; color:#374151; border:1.5px solid #e5e7eb; }
.btn-secondary:hover { background:#e5e7eb; }

/* ── Paper ── */
.paper { max-width:1100px; margin:0 auto; background:#fff; border:1px solid #bbb; box-shadow:0 4px 24px rgba(0,0,0,.13); }

@media print {
    body  { background:#fff; padding:0; }
    .toolbar { display:none !important; }
    .paper { box-shadow:none; border:none; max-width:none; margin:0; }
    @page  { size:A4 landscape; margin:7mm 9mm; }
    .page-break { page-break-before: always; }
}

/* ════ LETTERHEAD ════ */
.doc-top { padding:5px 12px 2px; font-size:7.5pt; color:#666; line-height:1.5; }
.doc-letterhead {
    display:flex; align-items:center; justify-content:center; gap:16px;
    padding:10px 20px 8px; border-bottom:2.5px solid #111;
}
.doc-seal { flex-shrink:0; width:72px; height:72px; border-radius:50%; overflow:hidden; background:#f0f0f0; display:flex; align-items:center; justify-content:center; }
.doc-seal img { width:100%; height:100%; object-fit:cover; display:block; }
.doc-seal-ph { font-size:7.5px; color:#888; text-align:center; padding:4px; line-height:1.4; font-family:'Courier Prime',monospace; display:none; }
.doc-org { text-align:center;}
.doc-republic  { font-size:9pt; color:#555; margin-bottom:2px; }
.doc-org-name  { font-size:14pt; font-weight:900; letter-spacing:.03em; color:#111; }
.doc-org-sub   { font-size:8.5pt; color:#555; margin-top:2px; }
.doc-title { text-align:center; padding:8px 16px 7px; border-bottom:2px solid #111; }
.doc-title h1  { font-size:13pt; font-weight:900; text-transform:uppercase; letter-spacing:.05em; color:#111; line-height:1; }
.doc-title h2  { font-size:10pt; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#444; margin-top:3px; }

/* ════ INFO TABLE ════ */
.info-table { width:100%; border-collapse:collapse; }
.info-table td { border:1px solid #999; padding:4px 10px; vertical-align:top; }
.fl { font-size:7.5pt; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#555; display:block; margin-bottom:2px; }
.fv { font-size:8pt; font-weight:700; color:#111; text-transform:uppercase; display:block; }

/* ════ OPENING BALANCE ════ */
.opening-band {
    display:flex; align-items:center; gap:20px; padding:2px 2px;
    background:#f7fdf7; border-bottom:1px solid #999; font-size:9pt;
}
.ob-lbl  { font-size:8pt; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#555; }
.ob-item { display:flex; align-items:baseline; gap:5px; }
.ob-type { font-size:8pt; font-weight:700; color:#555; text-transform:uppercase; }
.ob-val  { font-size:12pt; font-weight:900; color:#1a3a1a; font-family:'Courier New',monospace; }
.ob-div  { color:#ccc; }
.ob-note { margin-left:auto; font-size:7.5pt; color:#888; font-style:italic; }

/* ════ LEDGER TABLE ════ */
.ledger-wrap { overflow-x:auto; }
.ledger { width:100%; border-collapse:collapse; font-size:8.5pt; }
.ledger thead tr.hdr-top th {
    border:1px solid #888; padding:5px; text-align:center;
    font-size:8pt; font-weight:900; text-transform:uppercase; letter-spacing:.04em;
    background:#f0f0f0; color:#111; vertical-align:middle;
}
.ledger thead tr.hdr-top th.al { text-align:left; padding-left:8px; }
.ledger thead tr.hdr-sub th {
    border:1px solid #888; padding:3px 5px; text-align:center;
    font-size:7.5pt; font-weight:700; background:#e5e5e5; color:#333;
    text-transform:uppercase; letter-spacing:.03em;
}
.th-e  { background:#dbeafe !important; color:#1e40af !important; }
.th-t  { background:#dcfce7 !important; color:#166534 !important; }
.th-w  { background:#fef9c3 !important; color:#854d0e !important; }
.th-ta { background:#fee2e2 !important; color:#991b1b !important; }
.th-b  { background:#f0fdf4 !important; color:#166534 !important; }
.ledger tbody td {
    border:1px solid #bbb; padding:3px 5px; vertical-align:middle;
    text-align:center; color:#111; font-size:8.5pt; height:22px;
}
.ledger tbody td.al  { text-align:left; padding-left:8px; }
.ledger tbody td.ar  { text-align:right; padding-right:6px; }
.ledger tbody td.idx { color:#888; font-size:7.5pt; }
.ledger tbody td.mn  { font-family:'Courier New',monospace; text-align:right; padding-right:6px; }
.ledger tbody td.ce  { background:#f0f7ff; }
.ledger tbody td.ct  { background:#f0fdf4; }
.ledger tbody td.cw  { background:#fffde7; }
.ledger tbody td.cta { background:#fff5f5; }
.ledger tbody tr.msep td {
    background:#1a3a1a; color:#fff; font-size:8pt; font-weight:700;
    text-align:left; padding:5px 12px; letter-spacing:.1em; text-transform:uppercase;
    border-color:#2d5a1b;
}
.ledger tbody td.bvl, .ledger tbody td.bsl {
    background:#f0fdf4; color:#166534; font-weight:700;
    font-family:'Courier New',monospace; text-align:right; padding-right:7px;
}
.ledger tbody td.bvl.neg, .ledger tbody td.bsl.neg { background:#fef2f2; color:#991b1b; }
.ledger tbody td.erow { height:22px; }


/* ════ SUMMARY ════ */
.summary { display:flex; border-top:2px solid #111; border-bottom:1px solid #999; background:#fafafa; }
.sc { flex:1; padding:7px 10px; border-right:1px solid #ccc; text-align:center; }
.sc:last-child { border-right:none; }
.sc-lbl { font-size:7pt; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#777; display:block; margin-bottom:2px; }
.sc-val { font-size:12.5pt; font-weight:900; font-family:'Courier New',monospace; color:#111; line-height:1; }
.sc-val.vl  { color:#1e40af; }
.sc-val.sl  { color:#166534; }
.sc-val.neg { color:#991b1b; }



</style>
</head>
<body>

@php
    use Carbon\Carbon;

    $MONTHS = [
        1=>'January', 2=>'February', 3=>'March',    4=>'April',
        5=>'May',     6=>'June',     7=>'July',      8=>'August',
        9=>'September',10=>'October',11=>'November',12=>'December',
    ];

    $fmtN = fn($v) => ($v !== null && $v !== '') ? number_format((float)$v, 3) : '—';
    $isNeg = fn($v) => ($v !== null && $v !== '') && (float)$v < 0;

    $openVl = $card ? (float)$card->opening_vl : 0;
    $openSl = $card ? (float)$card->opening_sl : 0;

    $tevl=0; $tesl=0; $ttvl=0; $ttsl=0; $twop=0; $ttar=0;
    $lVl=null; $lSl=null;
    foreach ($entries as $e) {
        if (str_contains($e->date_particulars ?? '', '--- MONTH SEPARATOR ---')) continue;
        $tevl += (float)($e->earned_vl ?? 0);
        $tesl += (float)($e->earned_sl ?? 0);
        $ttvl += (float)($e->taken_vl  ?? 0);
        $ttsl += (float)($e->taken_sl  ?? 0);
        $twop += (float)($e->leave_wop ?? 0);
        $ttar += (float)($e->tardy_undertime ?? 0);
        if ($e->balance_vl !== null) $lVl = (float)$e->balance_vl;
        if ($e->balance_sl !== null) $lSl = (float)$e->balance_sl;
    }
    $fVl = $lVl ?? ($openVl + $tevl - $ttvl - $twop - $ttar);
    $fSl = $lSl ?? ($openSl + $tesl - $ttsl);

    $lname = strtoupper($employee->last_name   ?? '');
    $fname = strtoupper($employee->first_name  ?? '');
    $mname = strtoupper($employee->middle_name ?? '');
    $ename = strtoupper($employee->extension_name ?? '');
    $pos   = strtoupper($employee->position->position_name   ?? '—');
    $dept  = strtoupper($employee->department->department_name ?? '—');
    $empId = $employee->formatted_employee_id ?? $employee->employee_id;
    $sal   = number_format($employee->salary ?? 0, 2);

    // Group applications by status for the log
    $appByStatus = $applications->groupBy('status');
    $statusOrder = ['APPROVED','ON-PROCESS','RECEIVED','PENDING','REJECTED','CANCELLED'];
@endphp

{{-- ══ TOOLBAR ══ --}}
<div class="toolbar">
    <div class="toolbar-left">
        <div class="toolbar-icon">
            <svg style="width:18px;height:18px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <div class="toolbar-title">Record of Leave of Absence — {{ $year }}</div>
            <div class="toolbar-sub">{{ $lname }}, {{ $fname }} &nbsp;·&nbsp; {{ $pos }} &nbsp;·&nbsp; ID: {{ $empId }}</div>
        </div>
    </div>
    <div class="toolbar-actions">
        <button class="btn btn-secondary" onclick="window.close()">✕ Close</button>
        <button class="btn btn-primary" onclick="window.print()">
            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print / Save PDF
        </button>
    </div>
</div>

{{-- ══ PAPER DOCUMENT ══ --}}
<div class="paper">

    <div class="doc-letterhead">
        <div class="doc-seal">
            <img src="{{ asset('images/kapitolyo.png') }}" alt="Seal"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
            <div style="display:none;">📷 Seal</div>
        </div>
        <div class="doc-org">
            <div class="doc-republic">Republic of the Philippines</div>
            <div class="doc-org-name">PROVINCIAL GOVERNMENT OF CAMARINES NORTE</div>
            <div class="doc-org-sub">Provincial Capitol Bldg., Daet, Camarines Norte</div>
        </div>
    </div>

    <div class="doc-title" style="margin-bottom:10px;">
        <h1>Record of Leave of Absence</h1>
        {{--<h2>Leave Card &mdash; Calendar Year {{ $year }}</h2>--}}
    </div>

    

{{-- Employee Info Table --}}
<table class="info-table">
    <tr>
        <td colspan="3">
            <div style="display:flex;gap:0;">
                <span class="fl" style="white-space:nowrap;padding-right:6px;flex-shrink:0;align-self:flex-start;">1. Name:</span>
                <div style="flex:1;display:flex;flex-direction:column;">
                    <div style="display:flex;gap:0;">
                        <span style="flex:1.2;font-weight:400;font-size:7pt;color:#555;text-align:center;">(Last Name)</span>
                        <span style="flex:1;font-weight:400;font-size:7pt;color:#555;text-align:center;">(First Name)</span>
                        <span style="flex:.8;font-weight:400;font-size:7pt;color:#555;text-align:center;">(Middle Name)</span>
                        <span style="flex:.2;font-weight:400;font-size:7pt;color:#555;text-align:center;">(Ext.)</span>
                    </div>
                    <div style="display:flex;gap:0;margin-top:1px;">
                        <span class="fv" style="flex:1.2;padding:1px 4px;text-align:center;">{{ $lname }}</span>
                        <span class="fv" style="flex:1;padding:1px 4px;text-align:center;">{{ $fname }}</span>
                        <span class="fv" style="flex:.8;padding:1px 4px;text-align:center;">{{ $mname }}</span>
                        <span class="fv" style="flex:.2;padding:1px 4px;text-align:center;">{{ $ename }}</span>
                    </div>
                </div>
            </div>
        </td>
        <td style="width:30%;">
            <span class="fl">2. Office / Department</span>
            <span class="fv" style="display:block; text-align:center;" >Office of the Provincial Agriculturist</span>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="width:50%;">
            <span class="fl">3. Calendar Year</span>
            <span class="fv" style="margin-left:45px;">{{ $year }}</span>
        </td>
        <td colspan="2" style="width:50%;">
            <span class="fl">4. Position / Designation</span>
            <span class="fv" style="margin-left:45px;">{{ $pos }}</span>
        </td>
    </tr>
</table>

    {{-- Opening Balance Band --}}
    <div class="opening-band">
        <span class="ob-lbl">Opening Balance (As Carried Forward):</span>
        <span class="ob-item">
            <span class="ob-type">Vacation Leave</span>
            <span class="ob-val" style="text-decoration: underline;">{{ number_format($openVl, 3) }}</span>
        </span>
        <span class="ob-div">|</span>
        <span class="ob-item">
            <span class="ob-type">Sick Leave</span>
            <span class="ob-val" style="text-decoration: underline;">{{ number_format($openSl, 3) }}</span>
        </span>
    </div>

    {{-- ══ LEDGER TABLE ══ --}}
<div class="ledger-wrap">
    <table class="ledger">
        <thead>
            <tr class="hdr-top">
                <th rowspan="2" style="width:22px;">#</th>
                <th rowspan="2" style="width:64px;">Month</th>
                <th rowspan="2" class="al" style="min-width:200px;">Date / Particulars</th>
                <th colspan="2" class="th-e">Leave Credits Earned</th>
                <th colspan="2" class="th-t">Leave Taken (With Pay)</th>
                <th rowspan="2" class="th-w" style="width:54px;">Leave<br>W/O Pay</th>
                <th rowspan="2" class="th-ta" style="width:58px;">Tardy /<br>Undertime</th>
                <th colspan="2" class="th-b">Balance of Leave Credits</th>
                <th rowspan="2" class="al" style="min-width:110px;">Remarks</th>
                <th rowspan="2" class="al" style="min-width:64px;">Status</th>
            </tr>
            <tr class="hdr-sub">
                <th class="th-e">Vacation<br>Leave</th>
                <th class="th-e">Sick<br>Leave</th>
                <th class="th-t">Vacation<br>Leave</th>
                <th class="th-t">Sick<br>Leave</th>
                <th class="th-b">VL Balance</th>
                <th class="th-b">SL Balance</th>
            </tr>
        </thead>
        <tbody>
            @php $rowNum = 0; @endphp

            @forelse($entries as $entry)
                @if(str_contains($entry->date_particulars ?? '', '--- MONTH SEPARATOR ---'))
                    <tr class="msep">
                        <td colspan="13">
                            &mdash;&mdash; {{ $MONTHS[$entry->month ?? 1] ?? '' }} &nbsp; {{ $year }} &mdash;&mdash;
                        </td>
                    </tr>
                @else
                    @php $rowNum++; @endphp
                    <tr>
                        <td class="idx">{{ $rowNum }}</td>
                        <td style="font-size:8pt;text-align:center;">
                            {{ isset($entry->month) && $entry->month ? ($MONTHS[(int)$entry->month] ?? '') : '' }}
                        </td>
                        <td class="al">
                            @if(!$entry->is_manual)
                                
                            @endif
                            {{ $entry->date_particulars }}
                        </td>
                        <td class="mn ce">{{ $fmtN($entry->earned_vl) }}</td>
                        <td class="mn ce">{{ $fmtN($entry->earned_sl) }}</td>
                        <td class="mn ct">{{ $fmtN($entry->taken_vl)  }}</td>
                        <td class="mn ct">{{ $fmtN($entry->taken_sl)  }}</td>
                        <td class="mn cw">{{ $fmtN($entry->leave_wop) }}</td>
                        <td class="mn cta">{{ $fmtN($entry->tardy_undertime) }}</td>
                        <td class="bvl {{ $isNeg($entry->balance_vl) ? 'neg' : '' }}">
                            {{ $entry->balance_vl !== null ? number_format((float)$entry->balance_vl, 3) : '—' }}
                        </td>
                        <td class="bsl {{ $isNeg($entry->balance_sl) ? 'neg' : '' }}">
                            {{ $entry->balance_sl !== null ? number_format((float)$entry->balance_sl, 3) : '—' }}
                        </td>
                        <td class="al" style="font-size:8pt;">{{ $entry->remarks }}</td>
                        <td class="al" style="font-size:8pt;">{{ $entry->status }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="13" style="padding:32px;text-align:center;color:#9ca3af;font-style:italic;">
                        No leave card entries recorded for {{ $year }}.
                    </td>
                </tr>
            @endforelse

            @if(count($entries) < 4)
                @for($p = 0; $p < max(0, 5 - count($entries)); $p++)
                    <tr>@for($c = 0; $c < 13; $c++)<td class="erow"></td>@endfor</tr>
                @endfor
            @endif
        </tbody>

        {{-- ══ CLOSING BALANCE  ══ --}}
        <tfoot>
            <tr style="background:#f0fdf4;border:1.5px solid #000000;">
                <td style="border:1px solid #999;"></td>
                <td style="border:1px solid #999;"></td>
                <td class="al" style="border:1px solid #999;font-size:8pt;font-weight:900;text-transform:uppercase;letter-spacing:.05em;color:#1a3a1a;padding:6px 10px;">
                    Closing Balance for {{ $year }}
                </td>
                <td style="border:1px solid #999;background:#f0f7ff;"></td>
                <td style="border:1px solid #999;background:#f0f7ff;"></td>
                <td style="border:1px solid #999;background:#f0fdf4;"></td>
                <td style="border:1px solid #999;background:#f0fdf4;"></td>
                <td style="border:1px solid #999;background:#fffde7;"></td>
                <td style="border:1px solid #999;background:#fff5f5;"></td>
                <td class="bvl {{ $isNeg($lVl ?? $fVl) ? 'neg' : '' }}" style="border:1px solid #999;font-size:10pt;font-weight:900;color:#991b1b;text-align: right; ">{{ $lVl !== null ? number_format($lVl, 3) : number_format($fVl, 3) }}</td>
                <td class="bsl {{ $isNeg($lSl ?? $fSl) ? 'neg' : '' }}" style="border:1px solid #999;font-size:10pt;font-weight:900;color:#166534;text-align: right;">{{ $lSl !== null ? number_format($lSl, 3) : number_format($fSl, 3) }}</td>
                <td style="border:1px solid #999;"></td>
                <td style="border:1px solid #999;"></td>
            </tr>
        </tfoot>

    </table>
</div>



</div>{{-- end .paper --}}
</body>
</html>