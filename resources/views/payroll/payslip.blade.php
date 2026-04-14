@extends('layouts.app')
@section('title', 'My Payslip')
@section('page-title', 'Payslip')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');

*, *::before, *::after { box-sizing: border-box; }
body, input, select, button { font-family: 'Plus Jakarta Sans', sans-serif; }

.payslip-page {
    display: flex;
    flex-direction: column;
    min-height: calc(100vh - 120px);
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #9ca3af;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.breadcrumb a {
    color: #9ca3af;
    text-decoration: none;
    transition: color .15s;
}

.breadcrumb a:hover {
    color: #1a3a1a;
}

.breadcrumb .sep {
    color: #e5e7eb;
}

.breadcrumb .current {
    color: #1a3a1a;
    font-weight: 700;
}

.main-card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 20px rgba(0, 0, 0, .07);
    overflow: hidden;
    flex: 1;
}

.card-topbar {
    padding: 18px 26px 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    border-bottom: 1px solid #f0f2f0;
    background: linear-gradient(135deg, #fafffe 0%, #f6faf6 100%);
}

.card-topbar-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.card-topbar-icon {
    width: 40px;
    height: 40px;
    border-radius: 11px;
    flex-shrink: 0;
    background: linear-gradient(135deg, #1a3a1a 0%, #2d5a1b 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 3px 8px rgba(26, 58, 26, .3);
}

.card-topbar-icon svg {
    width: 20px;
    height: 20px;
    color: #fff;
}

.card-topbar-title {
    font-size: 16px;
    font-weight: 800;
    color: #111827;
    margin: 0;
    letter-spacing: -.3px;
}

.card-topbar-sub {
    font-size: 11px;
    color: #9ca3af;
    margin: 2px 0 0;
}

.period-select-wrap {
    position: relative;
    min-width: 200px;
}

.period-select {
    width: 100%;
    padding: 10px 38px 10px 14px;
    font-size: 13px;
    font-weight: 600;
    border: 1.5px solid #e9ecef;
    border-radius: 11px;
    color: #111827;
    background: #fff;
    appearance: none;
    -webkit-appearance: none;
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%239ca3af' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    transition: all .15s;
}

.period-select:focus {
    outline: none;
    border-color: #2d5a1b;
    box-shadow: 0 0 0 3px rgba(45, 90, 27, .09);
}

.btn-download {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 20px;
    font-size: 13px;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, #1a3a1a, #2d5a1b);
    border: none;
    border-radius: 11px;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 3px 10px rgba(26, 58, 26, .28);
    text-decoration: none;
}

.btn-download:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 16px rgba(26, 58, 26, .38);
    color: #fff;
}

.payslip-container {
    padding: 40px 26px;
    max-width: 1100px;
    margin: 0 auto;
    width: 100%;
}

.no-record {
    text-align: center;
    padding: 80px 20px;
    color: #9ca3af;
}

.no-record-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    color: #9ca3af;
}

.no-record-icon svg {
    width: 32px;
    height: 32px;
}

.no-record h3 {
    font-size: 18px;
    font-weight: 700;
    color: #6b7280;
    margin: 0 0 8px;
}

.no-record p {
    font-size: 13px;
    color: #9ca3af;
    margin: 0;
}

/* Payslip Document */
.payslip-doc {
    background: #fff;
    border: 2px solid #e5e7eb;
    border-radius: 16px;
    padding: 48px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, .08);
}

.payslip-header {
    text-align: center;
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 3px solid #1a3a1a;
}

.payslip-header h1 {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    margin: 0 0 4px;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.payslip-header h2 {
    font-size: 18px;
    font-weight: 800;
    color: #111827;
    margin: 0 0 4px;
    text-transform: uppercase;
}

.payslip-header h3 {
    font-size: 15px;
    font-weight: 700;
    color: #1a3a1a;
    margin: 0 0 8px;
    text-transform: uppercase;
}

.payslip-header h4 {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    margin: 0 0 16px;
    text-transform: uppercase;
}

.payslip-header .pay-period {
    font-size: 14px;
    font-weight: 700;
    color: #111827;
    margin: 12px 0 0;
}

/* Employee Info Box */
.emp-info-box {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 28px;
}

.emp-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px 32px;
}

.emp-info-item {
    display: flex;
    align-items: baseline;
}

.emp-info-label {
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .05em;
    min-width: 140px;
}

.emp-info-value {
    font-size: 13px;
    font-weight: 600;
    color: #111827;
}

/* Payslip Sections */
.payslip-section {
    margin-bottom: 24px;
}

.section-title {
    font-size: 12px;
    font-weight: 800;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: .5px;
    padding: 8px 16px;
    margin: 0 0 12px;
    border-radius: 8px;
}

.section-title.earnings {
    background: linear-gradient(135deg, #059669, #10b981);
}

.section-title.deductions {
    background: linear-gradient(135deg, #dc2626, #ef4444);
}

.section-title.net {
    background: linear-gradient(135deg, #1a3a1a, #2d5a1b);
}

.payslip-table {
    width: 100%;
    border-collapse: collapse;
}

.payslip-table td {
    padding: 9px 16px;
    font-size: 12px;
    border-bottom: 1px solid #f3f4f6;
}

.payslip-table tr:last-child td {
    border-bottom: none;
}

.payslip-table .label-cell {
    color: #6b7280;
    font-weight: 500;
    width: 60%;
}

.payslip-table .amount-cell {
    text-align: right;
    font-family: 'JetBrains Mono', monospace;
    font-weight: 600;
    color: #111827;
    width: 40%;
}

.payslip-table .sub-item .label-cell {
    padding-left: 32px;
    font-size: 11px;
    color: #9ca3af;
}

.payslip-table .sub-item .amount-cell {
    font-size: 11px;
    color: #6b7280;
    font-weight: 500;
}

.payslip-table .total-row td {
    padding: 14px 16px;
    font-weight: 800;
    font-size: 13px;
    border-top: 2px solid #e5e7eb;
    border-bottom: 2px solid #e5e7eb;
    background: #f9fafb;
}

.payslip-table .total-row .amount-cell {
    font-size: 14px;
}

.payslip-table .subtotal-row td {
    padding: 11px 16px;
    font-weight: 700;
    font-size: 12px;
    background: #f9fafb;
}

/* Net Pay Highlight */
.net-pay-box {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 2px solid #f59e0b;
    border-radius: 12px;
    padding: 20px 24px;
    margin-top: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.net-pay-label {
    font-size: 16px;
    font-weight: 800;
    color: #92400e;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.net-pay-amount {
    font-size: 28px;
    font-weight: 800;
    color: #92400e;
    font-family: 'JetBrains Mono', monospace;
}

/* Footer */
.payslip-footer {
    margin-top: 48px;
    padding-top: 24px;
    border-top: 2px solid #e5e7eb;
}

.signature-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 48px;
    margin-top: 48px;
}

.signature-box {
    text-align: center;
}

.signature-line {
    border-top: 2px solid #111827;
    padding-top: 8px;
    margin-top: 60px;
}

.signature-name {
    font-size: 13px;
    font-weight: 700;
    color: #111827;
    text-transform: uppercase;
}

.signature-title {
    font-size: 11px;
    color: #6b7280;
    margin-top: 2px;
}

@media print {
    .payslip-page {
        min-height: auto;
    }
    
    .card-topbar,
    .breadcrumb {
        display: none;
    }
    
    .payslip-doc {
        border: none;
        box-shadow: none;
        padding: 0;
    }
    
    .payslip-container {
        padding: 0;
    }
}

@media (max-width: 767px) {
    .payslip-doc {
        padding: 24px 20px;
    }
    
    .emp-info-grid,
    .signature-grid {
        grid-template-columns: 1fr;
        gap: 14px;
    }
    
    .emp-info-item {
        flex-direction: column;
    }
    
    .emp-info-label {
        min-width: 0;
        margin-bottom: 4px;
    }
    
    .net-pay-box {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .card-topbar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .period-select-wrap {
        min-width: 0;
    }
}
</style>

<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="sep">›</span>
    <span class="current">My Payslip</span>
</div>

<div class="payslip-page">
    <div class="main-card">
        <div class="card-topbar">
            <div class="card-topbar-left">
                <div class="card-topbar-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="card-topbar-title">My Payslip</p>
                    <p class="card-topbar-sub">View your salary details</p>
                </div>
            </div>
            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <div class="period-select-wrap">
                    <select class="period-select" onchange="window.location.href='{{ route('payroll.payslip') }}?period_id='+this.value">
                        <option value="">Select Period</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->period_id }}" {{ $selectedPeriodId == $p->period_id ? 'selected' : '' }}>
                                {{ $p->period_label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($record)
                <a href="{{ route('payroll.payslip.pdf', $record->period_id) }}?emp_id={{ $record->employee_id }}" class="btn-download" target="_blank">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download PDF
                </a>
                @endif
            </div>
        </div>

        <div class="payslip-container">
            @if(!$record)
                <div class="no-record">
                    <div class="no-record-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3>No Payslip Available</h3>
                    <p>Please select a payroll period from the dropdown above.</p>
                </div>
            @else
                @include('payroll.payslip-content', ['record' => $record])
            @endif
        </div>
    </div>
</div>
@endsection
