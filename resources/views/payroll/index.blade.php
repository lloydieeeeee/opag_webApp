@extends('layouts.app')
@section('title', 'Payroll — Admin')
@section('page-title', 'Payroll')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');
*, *::before, *::after { box-sizing: border-box; }
body, input, select, button, textarea { font-family: 'Plus Jakarta Sans', sans-serif; }

:root {
  --forest:   #1a3a1a;
  --forest-2: #243f24;
  --forest-3: #2d5a1b;
  --leaf:     #4a9b4a;
  --mist:     #f4f6f3;
  --bone:     #fafaf8;
  --ink:      #141a14;
  --slate:    #4a5568;
  --dust:     #9aa3a0;
  --line:     #e8ede8;
  --red:      #c0392b;
  --red-l:    #fce8e6;
  --green:    #1a6b1a;
  --green-l:  #e6f4e6;
  --amber:    #b45309;
  --amber-l:  #fef3c7;
  --blue:     #1e3a6e;
  --mono:     'JetBrains Mono', monospace;
}

html, body { height: 100%; }

.content-wrapper,
[data-page-content],
#page-content,
.page-content,
main {
  display: flex;
  flex-direction: column;
  flex: 1 1 auto;
  min-height: 0;
  height: 100%;
}

.pay-page {
  display: flex;
  flex-direction: column;
  flex: 1 1 auto;
  height: 100%;
  min-height: 0;
  overflow: hidden;
  gap: 0;
  padding: 0;
}

/* ══════════════════════════════
   BREADCRUMB
══════════════════════════════ */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #6b7280;
  margin-bottom: 16px;
  flex-shrink: 0;
  flex-wrap: wrap;
}
.breadcrumb a { color: #6b7280; text-decoration: none; }
.breadcrumb a:hover { color: var(--forest); }
.breadcrumb .sep { color: #d1d5db; }
.breadcrumb .current { color: var(--forest); font-weight: 600; }

/* ══════════════════════════════
   STATS STRIP
══════════════════════════════ */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px;
  margin-bottom: 14px;
  flex-shrink: 0;
}

.stat-card {
  background: #fff;
  border-radius: 14px;
  padding: 14px 18px;
  box-shadow: 0 1px 4px rgba(0,0,0,.06);
  border: 1px solid #f3f4f6;
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  transition: box-shadow .18s, transform .18s;
}
.stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.13); transform: translateY(-1px); }
.stat-card:active { transform: translateY(0); box-shadow: 0 1px 4px rgba(0,0,0,.06); }

.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.stat-val { font-size: 22px; font-weight: 800; color: #111827; line-height: 1; }
.stat-lbl { font-size: 10px; color: #6b7280; font-weight: 700; margin-top: 2px; text-transform: uppercase; letter-spacing: .05em; }

/* ══════════════════════════════
   SUCCESS / ERROR BANNER
══════════════════════════════ */
.success-banner {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  background: var(--green-l);
  border: 1px solid #a7d8a7;
  border-radius: 14px;
  font-size: 13px;
  color: var(--green);
  margin-bottom: 14px;
  font-weight: 500;
}

.error-banner {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  background: var(--red-l);
  border: 1px solid #f5c6c6;
  border-radius: 14px;
  font-size: 13px;
  color: var(--red);
  margin-bottom: 14px;
  font-weight: 500;
}

/* ══════════════════════════════
   MAIN CARD — fills remaining space
══════════════════════════════ */
.app-card {
  flex: 1 1 0;
  min-height: 0;
  display: flex;
  flex-direction: column;
  background: #fff;
  border-radius: 16px;
  border: 1px solid #f3f4f6;
  box-shadow: 0 1px 3px rgba(0,0,0,.05);
  overflow: hidden;
}

/* ══════════════════════════════
   TOP BAR
══════════════════════════════ */
.card-topbar {
  flex-shrink: 0;
  background: linear-gradient(135deg, #1a3a1a 0%, #2d5a1b 100%);
  padding: 14px 20px;
  border-bottom: none;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}
.topbar-left p:first-child {
  font-weight: 700;
  color: #fff;
  font-size: 15px;
  margin: 0 0 2px;
}
.topbar-left p:last-child {
  font-size: 11px;
  color: rgba(255,255,255,.6);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 6px;
}

/* ── PERIOD SELECT ── */
.period-select {
  appearance: none;
  -webkit-appearance: none;
  padding: 7px 28px 7px 10px;
  font-size: 12px;
  font-weight: 600;
  border: 1.5px solid rgba(255,255,255,.25);
  border-radius: 8px;
  background: rgba(255,255,255,.12) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' fill='none' stroke='%23ffffff' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") no-repeat right 9px center;
  color: #fff;
  outline: none;
  cursor: pointer;
  font-family: 'Plus Jakarta Sans', sans-serif;
  transition: border-color .15s, background .15s;
}
.period-select:focus { border-color: rgba(255,255,255,.6); background-color: rgba(255,255,255,.2); }
.period-select option { background: #1a3a1a; color: #fff; }

/* ── BUTTONS ── */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 700;
  color: #1a3a1a;
  background: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  text-decoration: none;
  transition: background .15s, color .15s;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.btn-primary:hover { background: #dcfce7; color: #1a3a1a; }

.btn-outline {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  font-size: 12px;
  font-weight: 600;
  color: rgba(255,255,255,.85);
  background: rgba(255,255,255,.1);
  border: 1.5px solid rgba(255,255,255,.2);
  border-radius: 8px;
  cursor: pointer;
  text-decoration: none;
  transition: all .15s;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.btn-outline:hover { border-color: rgba(255,255,255,.5); background: rgba(255,255,255,.2); color: #fff; }

.btn-danger {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 700;
  color: #fff;
  background: var(--red);
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all .15s;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.btn-danger:hover { background: #a93226; }

.btn-divider { width: 1px; height: 22px; background: rgba(255,255,255,.2); flex-shrink: 0; }

/* ══════════════════════════════
   BADGES
══════════════════════════════ */
.badge-DRAFT {
  background: var(--amber-l);
  color: var(--amber);
  padding: 2px 9px;
  border-radius: 20px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .04em;
}
.badge-FINALIZED {
  background: var(--green-l);
  color: var(--green);
  padding: 2px 9px;
  border-radius: 20px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .04em;
}

/* ══════════════════════════════
   PANEL TOOLBAR / FILTER ROW
══════════════════════════════ */
.panel-toolbar {
  flex-shrink: 0;
  padding: 12px 16px;
  border-bottom: 1px solid #f3f4f6;
  background: linear-gradient(135deg, #fafffe 0%, #f6faf6 100%);
}
.toolbar-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 10px;
}
.toolbar-head-text p:first-child { font-size: 11px; font-weight: 800; color: #1f2937; margin: 0 0 2px; text-transform: uppercase; letter-spacing: .05em; }
.toolbar-head-text p:last-child  { font-size: 11px; color: #9ca3af; margin: 0; }

.filter-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.filter-row .rel { position: relative; }
.filter-row .rel.rel-search { flex: 1 1 200px; min-width: 140px; }
.filter-row .rel:has(select) { flex: 0 0 auto; }
.filter-row input,
.filter-row select {
  width: 100%;
  appearance: none;
  -webkit-appearance: none;
  padding: 7px 10px;
  font-size: 12px;
  border: 1.5px solid #e9ecef;
  border-radius: 8px;
  background: #fff;
  color: #374151;
  outline: none;
  transition: border-color .15s, box-shadow .15s;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.filter-row select { padding-right: 26px; }
.filter-row input  { padding-left: 30px; }
.filter-row input:focus, .filter-row select:focus { border-color: var(--forest); box-shadow: 0 0 0 3px rgba(45,90,27,.09); }
.filter-row .chevron     { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #9ca3af; }
.filter-row .search-icon { position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; }

/* ══════════════════════════════
   SPREADSHEET TABLE
══════════════════════════════ */
.sheet-wrap {
  flex: 1 1 0;
  min-height: 0;
  overflow: auto;
  scrollbar-width: thin;
  scrollbar-color: #d1d5db transparent;
  position: relative;
  scroll-padding-bottom: 44px;
}
.sheet-wrap::-webkit-scrollbar { width: 5px; height: 5px; }
.sheet-wrap::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }
.sheet-wrap::-webkit-scrollbar-track { background: transparent; }

.sheet-table {
  border-collapse: collapse;
  font-size: 12px;
  font-family: var(--mono);
  min-width: max-content;
  width: 100%;
}

.sheet-table tbody tr:last-child td {
  padding-bottom: 44px;
}

/* ── Group header row (row 1) ── */
.sheet-table thead tr:first-child th {
  position: sticky;
  top: 0;
  z-index: 10;
  font-size: 9px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .06em;
  padding: 7px 10px;
  height: 31px;
  border-right: 1px solid rgba(255,255,255,.15);
  text-align: center;
  color: #fff;
  white-space: nowrap;
  user-select: none;
}

/* ── Sub-header row (row 2) ── */
.sheet-table thead tr:nth-child(2) th {
  position: sticky;
  top: 31px;
  z-index: 10;
  padding: 8px 10px;
  font-size: 9.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .05em;
  white-space: nowrap;
  user-select: none;
  border-right: 1px solid #e9ecef;
  text-align: right;
}

/* ── Frozen cols ── */
.col-frozen-0 { position: sticky; left: 0; z-index: 8; }
.col-frozen-1 { position: sticky; left: 40px; z-index: 8; }
.col-frozen-2 { position: sticky; left: 246px; z-index: 8; box-shadow: 3px 0 10px rgba(0,0,0,.07); }
thead tr:first-child .col-frozen-0,
thead tr:first-child .col-frozen-1,
thead tr:first-child .col-frozen-2 { z-index: 30 !important; }
thead tr:nth-child(2) .col-frozen-0,
thead tr:nth-child(2) .col-frozen-1,
thead tr:nth-child(2) .col-frozen-2 { z-index: 30 !important; }
tfoot .col-frozen-0, tfoot .col-frozen-1, tfoot .col-frozen-2 { z-index: 20; }

/* ── Group colours ── */
.grp-employee { background: #1a3a1a !important; }
.grp-gsis     { background: #1e40af !important; }
.grp-pagibig  { background: #7c3aed !important; }
.grp-ph       { background: #0891b2 !important; }
.grp-loans    { background: #b45309 !important; }
.grp-allow    { background: #065f46 !important; }
.grp-totals   { background: #991b1b !important; }
.grp-action   { background: #374151 !important; }

/* ── Sub-header colours ── */
.sub-employee { background: #f0fdf4 !important; color: #166534 !important; }
.sub-gsis     { background: #dbeafe !important; color: #1e40af !important; }
.sub-pagibig  { background: #ede9fe !important; color: #5b21b6 !important; }
.sub-ph       { background: #cffafe !important; color: #155e75 !important; }
.sub-loans    { background: #fef3c7 !important; color: #92400e !important; }
.sub-allow    { background: #d1fae5 !important; color: #065f46 !important; }
.sub-totals   { background: #fee2e2 !important; color: #991b1b !important; font-weight: 800 !important; }
.sub-action   { background: #f3f4f6 !important; }

/* ── Body rows ── */
.sheet-table tbody tr { transition: background .08s; cursor: pointer; }
.sheet-table tbody tr:nth-child(even) td { background: #fafaf8; }
.sheet-table tbody tr:nth-child(odd) td  { background: #fff; }
.sheet-table tbody tr:hover td           { background: #f0fdf4 !important; }
.sheet-table tbody tr.row-dirty td       { background: #fffbeb !important; }
.sheet-table tbody tr.row-saving td      { opacity: .55; }
.sheet-table tbody tr.row-saved td       { background: #e6f4e6 !important; }

.sheet-table td {
  padding: 0;
  border-right: 1px solid #f0f2f0;
  border-bottom: 1px solid #f0f2f0;
  vertical-align: middle;
  white-space: nowrap;
}
.sheet-table td:last-child { border-right: none; }

/* Frozen body bg */
tbody tr:nth-child(even) .col-frozen-0,
tbody tr:nth-child(even) .col-frozen-1,
tbody tr:nth-child(even) .col-frozen-2 { background: #fafaf8; }
tbody tr:nth-child(odd) .col-frozen-0,
tbody tr:nth-child(odd) .col-frozen-1,
tbody tr:nth-child(odd) .col-frozen-2  { background: #fff; }
tbody tr:hover .col-frozen-0,
tbody tr:hover .col-frozen-1,
tbody tr:hover .col-frozen-2           { background: #f0fdf4 !important; }
tbody tr.row-dirty .col-frozen-0,
tbody tr.row-dirty .col-frozen-1,
tbody tr.row-dirty .col-frozen-2       { background: #fffbeb !important; }
tbody tr.row-saved .col-frozen-0,
tbody tr.row-saved .col-frozen-1,
tbody tr.row-saved .col-frozen-2       { background: #e6f4e6 !important; }

/* ── Cell styles ── */
.cell-static { padding: 9px 10px; color: var(--slate); font-size: 12px; font-family: 'Plus Jakarta Sans', sans-serif; }
.cell-num { padding: 9px 10px; text-align: right; font-size: 11px; color: var(--slate); font-family: var(--mono); }
.cell-num.neg { color: var(--red); }
.cell-num.pos { color: var(--green); font-weight: 700; }

/* ── Editable input cells ── */
.cell-input-wrap {
  position: relative;
  cursor: text;
  min-width: 88px;
  text-align: right;
  transition: background .15s;
}
.cell-input-wrap:hover { background: #f0fdf4; }
.cell-input-wrap.focused { background: #fff !important; box-shadow: inset 0 0 0 2px #2d5a1b; border-radius: 4px; }

.cell-input {
  display: block;
  width: 100%;
  padding: 9px 10px;
  font-size: 11px;
  font-family: var(--mono);
  font-weight: 500;
  text-align: right;
  border: none;
  background: transparent;
  color: #374151;
  outline: none;
  cursor: text;
  min-width: 88px;
  transition: color .12s;
}
.cell-input:focus { color: #111827; }
.cell-input.changed { color: var(--forest); font-weight: 700; }
.cell-input[readonly] { color: #9ca3af; cursor: not-allowed; }

.cell-computed {
  padding: 9px 10px;
  text-align: right;
  font-size: 11.5px;
  font-family: var(--mono);
  font-weight: 800;
  min-width: 110px;
  display: block;
}

/* col sizing */
.col-num  { width: 40px; min-width: 40px; text-align: center; }
.col-name { width: 206px; min-width: 206px; }
.col-desig { width: 96px; min-width: 96px; }

/* ── Totals footer ── */
.sheet-table tfoot td {
  background: var(--forest) !important;
  color: #fff;
  font-weight: 700;
  font-size: 11px;
  font-family: var(--mono);
  padding: 11px 10px;
  white-space: nowrap;
  border-right: 1px solid rgba(255,255,255,.08);
  position: sticky;
  bottom: 0;
  z-index: 100;
}
tfoot .col-frozen-0, tfoot .col-frozen-1, tfoot .col-frozen-2 { z-index: 20; }

/* ══════════════════════════════
   SAVE BAR
══════════════════════════════ */
.save-bar {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 20px;
  background: #f0fdf4;
  border-top: 1px solid #b7ddb7;
  gap: 12px;
  transition: opacity .25s, transform .25s;
}
.save-bar.hidden { opacity: 0; pointer-events: none; transform: translateY(4px); }
.save-bar-info {
  font-size: 12px;
  color: var(--green);
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 8px;
}
.save-bar-info::before {
  content: '';
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--leaf);
  animation: pulse 1.4s ease-in-out infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50%       { opacity: .5; transform: scale(.85); }
}

.btn-save-all {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 8px 20px;
  font-size: 12px;
  font-weight: 700;
  color: #fff;
  background: linear-gradient(135deg, #1a3a1a, #2d5a1b);
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all .2s;
  font-family: 'Plus Jakarta Sans', sans-serif;
  box-shadow: 0 3px 10px rgba(26,58,26,.28);
}
.btn-save-all:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(26,58,26,.38); }

.btn-discard-all {
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  background: #fff;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all .15s;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.btn-discard-all:hover { border-color: #9ca3af; color: #374151; }

/* ══════════════════════════════
   TOAST
══════════════════════════════ */
#toast {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 999;
  min-width: 240px;
  background: #fff;
  border-radius: 15px;
  padding: 13px 17px;
  box-shadow: 0 10px 36px rgba(0,0,0,.16);
  display: flex;
  align-items: center;
  gap: 12px;
  opacity: 0;
  transform: translateY(14px);
  transition: all .32s cubic-bezier(.34,1.56,.64,1);
  pointer-events: none;
  border: 1px solid #f3f4f6;
}
#toast.show { opacity: 1; transform: translateY(0); }

/* ══════════════════════════════
   RESPONSIVE
══════════════════════════════ */
@media (max-width: 767px) {
  .pay-page { height: auto; overflow: visible; padding-bottom: 60px; flex: none; }
  .app-card { overflow: visible; flex: none; height: auto; }
  .sheet-wrap { overflow-x: auto; height: auto; max-height: none; flex: none; }
  .stats-grid {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    overflow-x: auto !important;
    gap: 8px !important;
    padding-bottom: 4px;
    scrollbar-width: none;
  }
  .stats-grid::-webkit-scrollbar { display: none; }
  .stat-card {
    flex: 0 0 100px !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    padding: 10px 12px !important;
  }
  .stat-val { font-size: 18px !important; }
  .card-topbar { flex-direction: column; align-items: flex-start; }
  .filter-row { flex-wrap: nowrap !important; }
  .filter-row .rel.rel-search { flex: 1 1 0% !important; min-width: 0 !important; }
  .filter-row select { min-width: 90px !important; max-width: 110px !important; font-size: 11px !important; }
}
@media (min-width: 768px) and (max-width: 1023px) {
  .stats-grid { grid-template-columns: repeat(3, 1fr); }
}
</style>

@php
$dbDeductions = \App\Models\PayrollDeduction::where('is_active', 1)->get()->keyBy('name');

$jsConfig = [
    'gsisEeType'      => $dbDeductions->get('GSIS Employee Share')?->rate_type    ?? 'percent',
    'gsisEeValue'     => (float)($dbDeductions->get('GSIS Employee Share')?->rate_value   ?? 0.09),
    'gsisEeLimit'     => $dbDeductions->get('GSIS Employee Share')?->limit_amount !== null
                            ? (float)$dbDeductions->get('GSIS Employee Share')->limit_amount : null,

    'gsisGovtType'    => $dbDeductions->get("GSIS Gov't Share")?->rate_type  ?? 'percent',
    'gsisGovtValue'   => (float)($dbDeductions->get("GSIS Gov't Share")?->rate_value ?? 0.12),
    'gsisGovtLimit'   => $dbDeductions->get("GSIS Gov't Share")?->limit_amount !== null
                            ? (float)$dbDeductions->get("GSIS Gov't Share")->limit_amount : null,

    'gsisEcType'      => $dbDeductions->get('ECF (Employee Compensation Fund)')?->rate_type  ?? 'flat',
    'gsisEcValue'     => (float)($dbDeductions->get('ECF (Employee Compensation Fund)')?->rate_value ?? 100),
    'gsisEcLimit'     => null,

    'pagibigEeType'   => $dbDeductions->get("PAGIBIG Gov't Share")?->rate_type  ?? 'flat',
    'pagibigEeValue'  => (float)($dbDeductions->get("PAGIBIG Gov't Share")?->rate_value ?? 200),
    'pagibigEeLimit'  => null,

    'phicEeType'      => $dbDeductions->get('PhilHealth Employee Share')?->rate_type  ?? 'percent',
    'phicEeValue'     => (float)($dbDeductions->get('PhilHealth Employee Share')?->rate_value ?? 0.025),
    'phicEeLimit'     => $dbDeductions->get('PhilHealth Employee Share')?->limit_amount !== null
                            ? (float)$dbDeductions->get('PhilHealth Employee Share')->limit_amount : null,

    'phicGovtType'    => $dbDeductions->get("PhilHealth Gov't Share")?->rate_type  ?? 'percent',
    'phicGovtValue'   => (float)($dbDeductions->get("PhilHealth Gov't Share")?->rate_value ?? 0.025),
    'phicGovtLimit'   => $dbDeductions->get("PhilHealth Gov't Share")?->limit_amount !== null
                            ? (float)$dbDeductions->get("PhilHealth Gov't Share")->limit_amount : null,

    'peraType'        => $dbDeductions->get('PERA')?->rate_type   ?? 'flat',
    'peraValue'       => (float)($dbDeductions->get('PERA')?->rate_value ?? 2000),
    'peraLimit'       => null,
];
@endphp

<div class="pay-page">

  {{-- Breadcrumb --}}
  <div class="breadcrumb" style="flex-shrink:0;">
    <a href="{{ route('dashboard') }}">Payroll</a>
    <span class="sep">›</span>
    <span class="current">Payroll Management</span>
  </div>

  {{-- Stats --}}
  <div class="stats-grid" style="flex-shrink:0;">
    <div class="stat-card">
      <div class="stat-icon" style="background:#e6f4e6;">
        <svg style="width:18px;height:18px;color:#1a6b1a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
      </div>
      <div style="min-width:0;">
        <div class="stat-val">{{ $summary->employees }}</div>
        <div class="stat-lbl">Employees</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:#dbeafe;">
        <svg style="width:18px;height:18px;color:#1e3a8a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div style="min-width:0;">
        <div class="stat-val" style="font-size:14px;">₱{{ number_format($summary->gross, 0) }}</div>
        <div class="stat-lbl">Gross Salary</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:#fce8e6;">
        <svg style="width:18px;height:18px;color:#c0392b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div style="min-width:0;">
        <div class="stat-val" style="font-size:14px;">₱{{ number_format($summary->deductions, 0) }}</div>
        <div class="stat-lbl">Total Deductions</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:#dcfce7;">
        <svg style="width:18px;height:18px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div style="min-width:0;">
        <div class="stat-val" style="font-size:14px;">₱{{ number_format($summary->net, 0) }}</div>
        <div class="stat-lbl">Net Pay</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:#ede9fe;">
        <svg style="width:18px;height:18px;color:#5b21b6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
      </div>
      <div style="min-width:0;">
        <div class="stat-val" style="font-size:14px;">₱{{ number_format($summary->wtax, 0) }}</div>
        <div class="stat-lbl">W/Tax</div>
      </div>
    </div>
  </div>

  @if(session('success'))
  <div class="success-banner">
    <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
  </div>
  @endif

  @if(session('error'))
  <div class="error-banner">
    <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
    {{ session('error') }}
  </div>
  @endif

  {{-- Main card --}}
  <div class="app-card">

    {{-- Top bar --}}
    <div class="card-topbar">
      <div class="topbar-left">
        <p>Payroll Records</p>
        <p>
          @if($selectedPeriod)
            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            {{ $selectedPeriod->period_label }}
            <span style="color:rgba(255,255,255,.3);">·</span>
            <span class="badge-{{ $selectedPeriod->status }}">{{ $selectedPeriod->status }}</span>
          @else
            No period selected
          @endif
        </p>
      </div>
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <form method="GET" action="{{ route('payroll.index') }}" style="display:flex;align-items:center;">
          <select name="period_id" class="period-select" onchange="this.form.submit()">
            @foreach($periods as $p)
              <option value="{{ $p->period_id }}" {{ $p->period_id == optional($selectedPeriod)->period_id ? 'selected' : '' }}>
                {{ $p->period_label }}
              </option>
            @endforeach
          </select>
        </form>

        <div class="btn-divider"></div>

        @if($selectedPeriod && $selectedPeriod->status === 'DRAFT')
          <form method="POST" action="{{ route('payroll.finalize', $selectedPeriod->period_id) }}" onsubmit="return confirm('Finalize this payroll? This cannot be undone.')">
            @csrf
            <button type="submit" class="btn-danger">
              <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              Finalize
            </button>
          </form>
        @endif

        @if($selectedPeriod)
          <a href="{{ route('payroll.pdf', $selectedPeriod->period_id) }}" class="btn-outline" target="_blank">
            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Print
          </a>
          <a href="{{ route('payroll.remittances', ['period_id' => $selectedPeriod->period_id]) }}" class="btn-outline">
            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Remittances
          </a>
        @endif

        <div class="btn-divider"></div>

        <a href="{{ route('payroll.create') }}" class="btn-primary">
          <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
          New Period
        </a>
      </div>
    </div>

    {{-- Toolbar / filter row --}}
    <div class="panel-toolbar">
      <div class="toolbar-head">
        <div class="toolbar-head-text">
          <p>Payroll Sheet</p>
          <p>Click a cell to edit · Ctrl+S saves all changes</p>
        </div>
      </div>
      <div class="filter-row">
        <div class="rel rel-search">
          <svg class="search-icon" style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
          <input type="text" placeholder="Search employee name / ID…" id="payrollSearch" oninput="filterPayroll(this.value)">
        </div>
        <div class="rel">
          <select id="filterDept" onchange="filterPayroll(document.getElementById('payrollSearch').value)">
            <option value="">All Departments</option>
            @foreach($records->pluck('employee.department.department_name')->filter()->unique()->sort() as $dept)
              <option value="{{ strtolower($dept) }}">{{ $dept }}</option>
            @endforeach
          </select>
          <svg class="chevron" style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
      </div>
    </div>

    {{-- Spreadsheet table --}}
    <div class="sheet-wrap" id="sheetWrap">
      <table class="sheet-table" id="payrollSheet">
        <thead>
          {{-- GROUP HEADER ROW --}}
          <tr>
            <th class="col-frozen-0 col-num grp-employee" rowspan="2"></th>
            <th class="col-frozen-1 col-name grp-employee" rowspan="2" style="text-align:left;padding-left:14px;">Employee</th>
            <th class="col-frozen-2 col-desig grp-employee" rowspan="2" style="text-align:left;padding-left:10px;">Position</th>
            <th class="grp-employee" rowspan="2" style="text-align:right;min-width:130px;padding:0 10px;">Gross Salary</th>
            {{-- GSIS: 3 fixed + 8 loan cols = 11 --}}
            <th class="grp-gsis" colspan="11" style="text-align:center;">G &nbsp; S &nbsp; I &nbsp; S</th>
            {{-- PAG-IBIG: 2 fixed + 2 loan = 4 --}}
            <th class="grp-pagibig" colspan="4" style="text-align:center;">PAG-IBIG</th>
            {{-- PHILHEALTH: 2 --}}
            <th class="grp-ph" colspan="2" style="text-align:center;">PHILHEALTH</th>
            {{-- LOANS: 6 --}}
            <th class="grp-loans" colspan="6" style="text-align:center;">LOANS &amp; DEDUCTIONS</th>
            {{-- ALLOWANCES: 3 --}}
            <th class="grp-allow" colspan="3" style="text-align:center;">ALLOWANCES</th>
            {{-- TOTALS: 2 --}}
            <th class="grp-totals" colspan="2" style="text-align:center;">TOTALS</th>
            <th class="grp-action" rowspan="2" style="text-align:center;min-width:48px;">St.</th>
          </tr>
          {{-- SUB-HEADER ROW --}}
          <tr>
            {{-- GSIS (11 cols) --}}
            <th class="sub-gsis" style="min-width:95px;">EE (9%)</th>
            <th class="sub-gsis" style="min-width:95px;">Gov't (12%)</th>
            <th class="sub-gsis" style="min-width:80px;">EC</th>
            <th class="sub-gsis" style="min-width:95px;">Policy Loan</th>
            <th class="sub-gsis" style="min-width:95px;">Emergency</th>
            <th class="sub-gsis" style="min-width:95px;">Real Estate</th>
            <th class="sub-gsis" style="min-width:80px;">MPL</th>
            <th class="sub-gsis" style="min-width:84px;">MPL Lite</th>
            <th class="sub-gsis" style="min-width:80px;">GFAL</th>
            <th class="sub-gsis" style="min-width:86px;">Computer</th>
            <th class="sub-gsis" style="min-width:80px;">Conso</th>
            {{-- PAG-IBIG (4 cols) --}}
            <th class="sub-pagibig" style="min-width:80px;">EE</th>
            <th class="sub-pagibig" style="min-width:80px;">Gov't</th>
            <th class="sub-pagibig" style="min-width:80px;">MPL</th>
            <th class="sub-pagibig" style="min-width:95px;">Calamity</th>
            {{-- PHILHEALTH (2 cols) --}}
            <th class="sub-ph" style="min-width:80px;">EE</th>
            <th class="sub-ph" style="min-width:80px;">Gov't</th>
            {{-- LOANS (6 cols) --}}
            <th class="sub-loans" style="min-width:80px;">W/Tax</th>
            <th class="sub-loans" style="min-width:80px;">DBP</th>
            <th class="sub-loans" style="min-width:80px;">LBP</th>
            <th class="sub-loans" style="min-width:95px;">CNGWMPC</th>
            <th class="sub-loans" style="min-width:86px;">PARACLE</th>
            <th class="sub-loans" style="min-width:95px;">Overpayment</th>
            {{-- ALLOWANCES (3 cols) --}}
            <th class="sub-allow" style="min-width:80px;">PERA</th>
            <th class="sub-allow" style="min-width:80px;">RATA</th>
            <th class="sub-allow" style="min-width:84px;">TA/Other</th>
            {{-- TOTALS (2 cols) --}}
            <th class="sub-totals" style="min-width:115px;">Total Ded.</th>
            <th class="sub-totals" style="min-width:115px;">Net Pay</th>
          </tr>
        </thead>

        <tbody id="payrollTbody">
          @forelse($records as $i => $r)
          <?php
            $pid    = $r->payroll_id;
            $locked = optional($selectedPeriod)->status === 'FINALIZED';
            $ro     = $locked ? 'readonly' : '';
            $onch   = $locked ? '' : "markDirty({$pid}, this)";
            $dept   = strtolower($r->employee->department->department_name ?? '');
            $name   = strtolower(($r->employee->last_name ?? '') . ' ' . ($r->employee->first_name ?? ''));
            $empid  = strtolower($r->employee->formatted_employee_id ?? $r->employee_id ?? '');
          ?>
          <tr id="row_{{ $pid }}"
              data-id="{{ $pid }}"
              data-orig="{{ $r->toJson() }}"
              data-search="{{ $name }} {{ $empid }}"
              data-dept="{{ $dept }}"
              data-gross="{{ $r->gross_salary }}">

            {{-- FROZEN: # --}}
            <td class="col-frozen-0 col-num">
              <div class="cell-static" style="text-align:center;color:#9ca3af;font-size:11px;padding:9px 4px;">{{ $i+1 }}</div>
            </td>

            {{-- FROZEN: Name --}}
            <td class="col-frozen-1 col-name" style="border-right:1px solid #f0f2f0;">
              <div style="padding:8px 14px;">
                <div style="font-weight:700;color:#111827;font-family:'Plus Jakarta Sans',sans-serif;font-size:12.5px;line-height:1.3;">
                  {{ $r->employee->last_name ?? '—' }}, {{ $r->employee->first_name ?? '' }}
                </div>
                <div style="font-size:10px;color:#9ca3af;font-family:'JetBrains Mono',monospace;margin-top:1px;">
                  {{ $r->employee->department->department_name ?? '' }}
                </div>
              </div>
            </td>

            {{-- FROZEN: Designation --}}
            <td class="col-frozen-2 col-desig">
              <div class="cell-static" style="font-size:11.5px;color:#374151;font-weight:600;padding:9px 10px;">
                {{ $r->designation ?? optional($r->employee->position)->position_code }}
              </div>
            </td>

            {{-- Gross (read-only) --}}
            <td>
              <div class="cell-num" style="font-weight:700;color:#111827;min-width:130px;">{{ number_format($r->gross_salary, 2) }}</div>
            </td>

            {{-- ── GSIS (11 cols) ── --}}

            {{-- GSIS EE --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_ee, 2, '.', '') }}"
                  data-field="gsis_ee" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- GSIS Govt (readonly computed) --}}
            <td>
              <div class="cell-input-wrap">
                <input class="cell-input" type="number" step="0.01" min="0" readonly
                  value="{{ number_format($r->gsis_govt ?? 0, 2, '.', '') }}"
                  style="color:#9ca3af;cursor:not-allowed;">
              </div>
            </td>
            {{-- GSIS EC --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_ec ?? 0, 2, '.', '') }}"
                  data-field="gsis_ec" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- GSIS Policy --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_policy ?? 0, 2, '.', '') }}"
                  data-field="gsis_policy" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- GSIS Emergency --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_emergency ?? 0, 2, '.', '') }}"
                  data-field="gsis_emergency" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- GSIS Real Estate --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_real_estate ?? 0, 2, '.', '') }}"
                  data-field="gsis_real_estate" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- GSIS MPL --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_mpl ?? 0, 2, '.', '') }}"
                  data-field="gsis_mpl" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- GSIS MPL Lite --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_mpl_lite ?? 0, 2, '.', '') }}"
                  data-field="gsis_mpl_lite" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- GSIS GFAL --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_gfal ?? 0, 2, '.', '') }}"
                  data-field="gsis_gfal" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- GSIS Computer --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_computer ?? 0, 2, '.', '') }}"
                  data-field="gsis_computer" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- GSIS Conso --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->gsis_conso ?? 0, 2, '.', '') }}"
                  data-field="gsis_conso" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>

            {{-- ── PAG-IBIG (4 cols) ── --}}

            {{-- PAG-IBIG EE --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->pagibig_ee ?? 0, 2, '.', '') }}"
                  data-field="pagibig_ee" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- PAG-IBIG Govt (readonly computed) --}}
            <td>
              <div class="cell-input-wrap">
                <input class="cell-input" type="number" step="0.01" min="0" readonly
                  value="{{ number_format($r->pagibig_govt ?? 0, 2, '.', '') }}"
                  style="color:#9ca3af;cursor:not-allowed;">
              </div>
            </td>
            {{-- PAG-IBIG MPL --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->pagibig_mpl ?? 0, 2, '.', '') }}"
                  data-field="pagibig_mpl" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- PAG-IBIG Calamity --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->pagibig_calamity ?? 0, 2, '.', '') }}"
                  data-field="pagibig_calamity" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>

            {{-- ── PHILHEALTH (2 cols) ── --}}

            {{-- PHILHEALTH EE --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->philhealth_ee ?? 0, 2, '.', '') }}"
                  data-field="philhealth_ee" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- PHILHEALTH Govt (readonly computed) --}}
            <td>
              <div class="cell-input-wrap">
                <input class="cell-input" type="number" step="0.01" min="0" readonly
                  value="{{ number_format($r->philhealth_govt ?? 0, 2, '.', '') }}"
                  style="color:#9ca3af;cursor:not-allowed;">
              </div>
            </td>

            {{-- ── LOANS & DEDUCTIONS (6 cols) ── --}}

            {{-- W/Tax --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->withholding_tax ?? 0, 2, '.', '') }}"
                  data-field="withholding_tax" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- DBP --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->loan_dbp ?? 0, 2, '.', '') }}"
                  data-field="loan_dbp" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- LBP --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->loan_lbp ?? 0, 2, '.', '') }}"
                  data-field="loan_lbp" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- CNGWMPC --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->loan_cngwmpc ?? 0, 2, '.', '') }}"
                  data-field="loan_cngwmpc" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- PARACLE --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->loan_paracle ?? 0, 2, '.', '') }}"
                  data-field="loan_paracle" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- Overpayment --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->overpayment ?? 0, 2, '.', '') }}"
                  data-field="overpayment" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>

            {{-- ── ALLOWANCES (3 cols) ── --}}

            {{-- PERA --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->allowance_pera ?? 0, 2, '.', '') }}"
                  data-field="allowance_pera" oninput="{{ $onch }}"
                  style="color:#065f46;font-weight:600;"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- RATA --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->allowance_rata ?? 0, 2, '.', '') }}"
                  data-field="allowance_rata" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>
            {{-- TA/Other --}}
            <td>
              <div class="cell-input-wrap" onclick="this.querySelector('input')?.focus()">
                <input class="cell-input" type="number" step="0.01" min="0" {{ $ro }}
                  value="{{ number_format($r->allowance_other ?? 0, 2, '.', '') }}"
                  data-field="allowance_other" oninput="{{ $onch }}"
                  onfocus="this.parentElement.classList.add('focused')"
                  onblur="this.parentElement.classList.remove('focused')">
              </div>
            </td>

            {{-- COMPUTED TOTALS --}}
            <td>
              <span class="cell-computed neg" id="ded_{{ $pid }}">{{ number_format($r->total_deductions, 2) }}</span>
            </td>
            <td>
              <span class="cell-computed pos" id="net_{{ $pid }}">{{ number_format($r->net_pay, 2) }}</span>
            </td>

            {{-- STATUS --}}
            <td style="text-align:center;padding:0 8px;">
              <span id="status_{{ $pid }}" style="font-size:11px;color:#9ca3af;font-family:'Plus Jakarta Sans',sans-serif;">—</span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="34" style="padding:64px;text-align:center;color:#9ca3af;font-family:'Plus Jakarta Sans',sans-serif;">
              <svg style="width:40px;height:40px;color:#d1d5db;margin:0 auto 12px;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              No payroll records found.
              <a href="{{ route('payroll.create') }}" style="color:var(--forest);font-weight:600;text-decoration:none;border-bottom:1px solid var(--leaf);">Create a payroll period →</a>
            </td>
          </tr>
          @endforelse
        </tbody>

        @if($records->count())
        <tfoot>
          <tr>
            <td class="col-frozen-0 col-num" style="text-align:center;font-size:12px;opacity:.7;">Σ</td>
            <td class="col-frozen-1 col-name" style="font-family:'Plus Jakarta Sans',sans-serif;font-size:12px;opacity:.85;">{{ $records->count() }} employees</td>
            <td class="col-frozen-2 col-desig"></td>
            {{-- Gross --}}
            <td style="text-align:right;padding:11px 10px;">{{ number_format($summary->gross, 2) }}</td>
            {{-- GSIS --}}
            <td style="text-align:right;padding:11px 10px;">{{ number_format($summary->gsis_ee ?? 0, 2) }}</td>
            <td style="text-align:right;padding:11px 10px;">{{ number_format($summary->gsis_govt ?? 0, 2) }}</td>
            <td style="text-align:right;padding:11px 10px;">{{ number_format($summary->gsis_ec ?? 0, 2) }}</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            {{-- PAG-IBIG --}}
            <td style="text-align:right;padding:11px 10px;">{{ number_format($summary->pagibig ?? 0, 2) }}</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            {{-- PhilHealth --}}
            <td style="text-align:right;padding:11px 10px;">{{ number_format($summary->philhealth ?? 0, 2) }}</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            {{-- Loans --}}
            <td style="text-align:right;padding:11px 10px;">{{ number_format($summary->wtax ?? 0, 2) }}</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            {{-- Allowances --}}
            <td style="text-align:right;padding:11px 10px;">{{ number_format($summary->pera_total ?? 0, 2) }}</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            <td style="text-align:right;padding:11px 10px;opacity:.4;">—</td>
            {{-- Totals --}}
            <td style="text-align:right;padding:11px 10px;color:#fca5a5;">{{ number_format($summary->deductions, 2) }}</td>
            <td style="text-align:right;padding:11px 10px;color:#86efac;">{{ number_format($summary->net, 2) }}</td>
            <td></td>
          </tr>
        </tfoot>
        @endif
      </table>
    </div>

    {{-- Save bar --}}
    <div class="save-bar hidden" id="saveBar">
      <div class="save-bar-info">
        <span id="dirtyCount">0</span> unsaved row(s)
      </div>
      <div style="display:flex;gap:8px;">
        <button class="btn-discard-all" onclick="discardAll()">↩ Discard</button>
        <button class="btn-save-all" onclick="saveAll()">
          <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
          Save All Changes
        </button>
      </div>
    </div>

  </div>{{-- end .app-card --}}
</div>{{-- end .pay-page --}}

{{-- Toast --}}
<div id="toast">
  <div id="toastIcon" style="width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"></div>
  <div>
    <p style="font-size:13px;font-weight:700;color:#111827;margin:0;" id="toastTitle"></p>
    <p style="font-size:11px;color:#6b7280;margin:3px 0 0;" id="toastMsg"></p>
  </div>
</div>

<script>
const CSRF         = '{{ csrf_token() }}';
const IS_FINALIZED = {{ optional($selectedPeriod)->status === 'FINALIZED' ? 'true' : 'false' }};
const DED_CONFIG   = @json($jsConfig);

function computeFixed(type, value, limit, gross) {
    let amount;
    if (type === 'percent') {
        amount = Math.round(gross * value * 100) / 100;
        if (limit !== null && limit !== undefined) amount = Math.min(amount, limit);
    } else {
        amount = Math.round(value * 100) / 100;
    }
    return amount;
}

/* ── Dirty tracking ── */
const dirtyRows = new Set();

function markDirty(pid, inputEl) {
  const row = document.getElementById('row_' + pid);
  if (!row) return;
  row.classList.add('row-dirty');
  if (inputEl) inputEl.classList.add('changed');
  dirtyRows.add(pid);
  updateSaveBar();
  recomputeRow(pid);
}

function updateSaveBar() {
  document.getElementById('dirtyCount').textContent = dirtyRows.size;
  document.getElementById('saveBar').classList.toggle('hidden', dirtyRows.size === 0);
}

/*
 * All editable deduction fields (reduce net pay):
 * Note: gsis_govt, pagibig_govt, philhealth_govt are government-side and do NOT reduce employee net.
 */
const EDITABLE_DEDUCTION_FIELDS = [
  'gsis_ee','gsis_ec','gsis_policy','gsis_emergency',
  'gsis_real_estate','gsis_mpl','gsis_mpl_lite','gsis_gfal',
  'gsis_computer','gsis_conso',
  'pagibig_ee','pagibig_mpl','pagibig_calamity',
  'philhealth_ee','withholding_tax',
  'loan_dbp','loan_lbp','loan_cngwmpc','loan_paracle','overpayment'
];
const ALLOWANCE_FIELDS = ['allowance_pera','allowance_rata','allowance_other'];

function getRowVal(row, field) {
  const inp = row.querySelector('[data-field="' + field + '"]');
  return parseFloat(inp?.value) || 0;
}

function recomputeRow(pid) {
  const row = document.getElementById('row_' + pid);
  if (!row) return;
  const gross = parseFloat(row.dataset.gross) || 0;
  const ded  = EDITABLE_DEDUCTION_FIELDS.reduce((s, f) => s + getRowVal(row, f), 0);
  const alw  = ALLOWANCE_FIELDS.reduce((s, f) => s + getRowVal(row, f), 0);
  const net  = gross - ded + alw;
  const fmt  = n => n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  const dedEl = document.getElementById('ded_' + pid);
  const netEl = document.getElementById('net_' + pid);
  if (dedEl) dedEl.textContent = fmt(ded);
  if (netEl) netEl.textContent = fmt(net);
}

/* ── Collect row data ── */
function collectRow(pid) {
  const row = document.getElementById('row_' + pid);
  if (!row) return null;
  const get = f => parseFloat(row.querySelector('[data-field="' + f + '"]')?.value) || 0;
  return {
    gsis_ee: get('gsis_ee'), gsis_ec: get('gsis_ec'),
    gsis_policy: get('gsis_policy'), gsis_emergency: get('gsis_emergency'),
    gsis_real_estate: get('gsis_real_estate'), gsis_mpl: get('gsis_mpl'),
    gsis_mpl_lite: get('gsis_mpl_lite'), gsis_gfal: get('gsis_gfal'),
    gsis_computer: get('gsis_computer'), gsis_conso: get('gsis_conso'),
    pagibig_ee: get('pagibig_ee'), pagibig_mpl: get('pagibig_mpl'),
    pagibig_calamity: get('pagibig_calamity'), philhealth_ee: get('philhealth_ee'),
    withholding_tax: get('withholding_tax'), loan_dbp: get('loan_dbp'),
    loan_lbp: get('loan_lbp'), loan_cngwmpc: get('loan_cngwmpc'),
    loan_paracle: get('loan_paracle'), overpayment: get('overpayment'),
    allowance_pera: get('allowance_pera'), allowance_rata: get('allowance_rata'),
    allowance_other: get('allowance_other'),
  };
}

/* ── Save all dirty rows ── */
async function saveAll() {
  if (dirtyRows.size === 0) return;
  const btn = document.querySelector('.btn-save-all');
  btn.disabled = true;
  btn.textContent = 'Saving…';

  const promises = [...dirtyRows].map(pid => {
    const data = collectRow(pid);
    if (!data) return Promise.resolve();
    const statusEl = document.getElementById('status_' + pid);
    if (statusEl) statusEl.textContent = '…';
    return fetch('/payroll/record/' + pid, {
      method: 'PATCH',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(res => {
      const row      = document.getElementById('row_' + pid);
      const statusEl = document.getElementById('status_' + pid);
      if (res.success) {
        row?.classList.remove('row-dirty');
        row?.classList.add('row-saved');
        row?.querySelectorAll('.changed').forEach(el => el.classList.remove('changed'));
        if (statusEl) statusEl.innerHTML = '<span style="color:#1a6b1a;font-size:13px;">✓</span>';
        setTimeout(() => row?.classList.remove('row-saved'), 1800);
        dirtyRows.delete(pid);
      } else {
        if (statusEl) statusEl.innerHTML = '<span style="color:#c0392b;font-size:13px;">✗</span>';
      }
    })
    .catch(() => {
      const statusEl = document.getElementById('status_' + pid);
      if (statusEl) statusEl.innerHTML = '<span style="color:#c0392b;font-size:13px;">✗</span>';
    });
  });

  await Promise.all(promises);

  btn.disabled = false;
  btn.innerHTML = '<svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Save All Changes';
  updateSaveBar();

  showToast(
    dirtyRows.size === 0 ? 'Saved!' : 'Partial save',
    dirtyRows.size === 0 ? 'All changes saved successfully.' : dirtyRows.size + ' row(s) failed.',
    dirtyRows.size === 0 ? 'success' : 'error'
  );
}

/* ── Discard all ── */
function discardAll() {
  dirtyRows.forEach(pid => {
    const row = document.getElementById('row_' + pid);
    if (!row) return;
    const orig = JSON.parse(row.dataset.orig || '{}');
    const fields = [
      'gsis_ee','gsis_ec','gsis_policy','gsis_emergency','gsis_real_estate',
      'gsis_mpl','gsis_mpl_lite','gsis_gfal','gsis_computer','gsis_conso',
      'pagibig_ee','pagibig_mpl','pagibig_calamity','philhealth_ee',
      'withholding_tax','loan_dbp','loan_lbp','loan_cngwmpc','loan_paracle','overpayment',
      'allowance_pera','allowance_rata','allowance_other',
    ];
    fields.forEach(field => {
      const inp = row.querySelector('[data-field="' + field + '"]');
      if (inp) { inp.value = parseFloat(orig[field] || 0).toFixed(2); inp.classList.remove('changed'); }
    });
    row.classList.remove('row-dirty');
    recomputeRow(pid);
    const statusEl = document.getElementById('status_' + pid);
    if (statusEl) statusEl.textContent = '—';
  });
  dirtyRows.clear();
  updateSaveBar();
  showToast('Discarded', 'All changes reverted.', 'info');
}

/* ── Filter ── */
function filterPayroll(query) {
  const q    = (query || '').toLowerCase().trim();
  const dept = (document.getElementById('filterDept')?.value || '').toLowerCase();
  document.querySelectorAll('#payrollTbody tr[data-id]').forEach(row => {
    const matchName = !q    || (row.dataset.search || '').includes(q);
    const matchDept = !dept || (row.dataset.dept   || '').includes(dept);
    row.style.display = (matchName && matchDept) ? '' : 'none';
  });
}

/* ── Toast ── */
function showToast(title, msg, type) {
  const map = {
    success: { bg: '#dcfce7', c: '#16a34a', p: 'M5 13l4 4L19 7' },
    error:   { bg: '#fee2e2', c: '#dc2626', p: 'M6 18L18 6M6 6l12 12' },
    info:    { bg: '#dbeafe', c: '#2563eb', p: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
  };
  const s = map[type] || map.info;
  document.getElementById('toastTitle').textContent = title;
  document.getElementById('toastMsg').textContent   = msg;
  const icon = document.getElementById('toastIcon');
  icon.innerHTML  = '<svg style="width:16px;height:16px;" fill="none" stroke="' + s.c + '" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' + s.p + '"/></svg>';
  icon.style.background = s.bg;
  const t = document.getElementById('toast');
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}

/* ── Ctrl+S / Cmd+S ── */
document.addEventListener('keydown', e => {
  if ((e.ctrlKey || e.metaKey) && e.key === 's') {
    e.preventDefault();
    if (dirtyRows.size > 0) saveAll();
  }
});

/* ══════════════════════════════
   HEIGHT FIX
══════════════════════════════ */
(function initHeightFill() {
  const page = document.querySelector('.pay-page');
  if (!page) return;
  function applyHeight() {
    const top = page.getBoundingClientRect().top + window.scrollY;
    page.style.height = 'calc(100vh - ' + Math.ceil(top) + 'px)';
  }
  applyHeight();
  if (window.ResizeObserver) {
    new ResizeObserver(applyHeight).observe(document.documentElement);
  }
  window.addEventListener('resize', applyHeight);
})();
</script>
@endsection