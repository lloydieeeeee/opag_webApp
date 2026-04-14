<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslips</title>
    <style>
        @page {
            margin: 5mm;
            size: letter portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 6pt;
            color: #000;
            background: #fff;
        }

        /*
         * Letter usable area with 5mm margins: ~206mm x 269mm
         * 4 slips per page: 2 cols x 2 rows = ~103mm x 134mm each
         */
        .page-wrap {
            width: 206mm;
            display: flex;
            flex-wrap: wrap;
        }

        .payslip-doc {
            width: 103mm;
            height: 134mm;
            padding: 3mm 3mm 2mm 3mm;
            overflow: hidden;
            border: 0.5pt dashed #aaa;
        }

        /* Header */
        .payslip-header {
            text-align: center;
            margin-bottom: 1.5mm;
        }

        .payslip-header p {
            font-size: 6pt;
            line-height: 1.35;
        }

        .payslip-header .title {
            font-weight: bold;
            font-size: 6.5pt;
        }

        /* Info rows */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0.5mm;
        }

        .info-table td {
            font-size: 6pt;
            padding: 0.2mm 0;
            vertical-align: top;
        }

        .info-table .field-label {
            width: 18mm;
        }

        .info-table .field-value {
            font-weight: bold;
        }

        /* Main payslip table */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5mm;
        }

        .main-table td {
            font-size: 6pt;
            padding: 0.25mm 1px;
            vertical-align: bottom;
            line-height: 1.3;
        }

        .main-table .row-label { width: 52%; }
        .main-table .row-line  { width: 30%; border-bottom: 0.5pt solid #000; }
        .main-table .row-amount { width: 18%; text-align: right; padding-right: 1px; }

        .main-table .section-label { padding-top: 0.8mm; }
        .main-table .total-row td  { font-weight: bold; padding-top: 0.8mm; }
        .main-table .net-row td    { font-weight: bold; }

        /* Signature */
        .signature-section {
            margin-top: 2mm;
            text-align: center;
        }

        .signature-name {
            font-weight: bold;
            font-size: 6pt;
            text-transform: uppercase;
        }

        .signature-title { font-size: 6pt; }
    </style>
</head>
<body>

{{--
    Defensive fallback:
    - If controller passes $records (collection) → use it directly
    - If old route still passes $record (single model) → wrap it so foreach never crashes
--}}
@php
    if (isset($records)) {
        $list = $records;
    } elseif (isset($record)) {
        $list = collect([$record]);
    } else {
        $list = collect([]);
    }
@endphp

<div class="page-wrap">

@forelse($list as $record)
<div class="payslip-doc">

    <!-- Header -->
    <div class="payslip-header">
        <p>Republic of the Philippines</p>
        <p>PROVINCE OF CAMARINES NORTE</p>
        <p>D A E T</p>
        <p>OFFICE OF THE PROVINCIAL AGRICULTURIST</p>
        <p class="title">PAY SLIP</p>
    </div>

    <!-- Employee Info -->
    <table class="info-table">
        <tr>
            <td class="field-label">Name</td>
            <td class="field-value">
                {{ strtoupper($record->employee->last_name) }},
                {{ strtoupper($record->employee->first_name) }}
                @if($record->employee->extension_name) {{ strtoupper($record->employee->extension_name) }}@endif
            </td>
        </tr>
        <tr>
            <td class="field-label">Position</td>
            <td class="field-value">{{ $record->designation ?? optional($record->employee->position)->position_code ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="field-label">For the Period</td>
            <td class="field-value">{{ $record->period->period_label }}</td>
        </tr>
    </table>

    <!-- Main Table -->
    <table class="main-table">
        <tr>
            <td class="row-label"><strong>Gross Salary</strong></td>
            <td class="row-line"></td>
            <td class="row-amount"><strong>{{ number_format($record->gross_salary, 2) }}</strong></td>
        </tr>
        <tr>
            <td class="row-label"><strong>PERA/RA/TA</strong></td>
            <td class="row-line"></td>
            <td class="row-amount"><strong>{{ number_format($record->allowance_pera + $record->allowance_rata + $record->allowance_ta, 2) }}</strong></td>
        </tr>
        @if($record->allowance_other > 0)
        <tr>
            <td class="row-label">Other Allowance</td>
            <td class="row-line"></td>
            <td class="row-amount">{{ number_format($record->allowance_other, 2) }}</td>
        </tr>
        @endif

        <tr>
            <td class="row-label section-label">Less Deduction</td>
            <td class="row-line"></td>
            <td class="row-amount"></td>
        </tr>

        <tr><td class="row-label">UCPB</td><td class="row-line"></td><td class="row-amount"></td></tr>
        <tr>
            <td class="row-label">MPL</td><td class="row-line"></td>
            <td class="row-amount">@if($record->gsis_mpl > 0){{ number_format($record->gsis_mpl, 2) }}@endif</td>
        </tr>
        <tr><td class="row-label">CBCN</td><td class="row-line"></td><td class="row-amount"></td></tr>
        <tr><td class="row-label">MSLAP</td><td class="row-line"></td><td class="row-amount"></td></tr>
        <tr>
            <td class="row-label">Withholding Tax</td><td class="row-line"></td>
            <td class="row-amount">@if($record->withholding_tax > 0){{ number_format($record->withholding_tax, 2) }}@endif</td>
        </tr>
        <tr><td class="row-label">GSIS salary Loan</td><td class="row-line"></td><td class="row-amount"></td></tr>
        <tr>
            <td class="row-label">GSIS Policy Loan</td><td class="row-line"></td>
            <td class="row-amount">@if($record->gsis_policy > 0){{ number_format($record->gsis_policy, 2) }}@endif</td>
        </tr>
        <tr>
            <td class="row-label">Medicare</td><td class="row-line"></td>
            <td class="row-amount">@if($record->philhealth_ee > 0){{ number_format($record->philhealth_ee, 2) }}@endif</td>
        </tr>
        <tr>
            <td class="row-label">GSIS Premium</td><td class="row-line"></td>
            <td class="row-amount">@if($record->gsis_ee > 0){{ number_format($record->gsis_ee, 2) }}@endif</td>
        </tr>
        <tr>
            <td class="row-label">PAG-IBIG</td><td class="row-line"></td>
            <td class="row-amount">@if($record->pagibig_ee > 0){{ number_format($record->pagibig_ee, 2) }}@endif</td>
        </tr>
        <tr>
            <td class="row-label">LBP</td><td class="row-line"></td>
            <td class="row-amount">@if($record->loan_lbp > 0){{ number_format($record->loan_lbp, 2) }}@endif</td>
        </tr>
        <tr>
            <td class="row-label">DBP</td><td class="row-line"></td>
            <td class="row-amount">@if($record->loan_dbp > 0){{ number_format($record->loan_dbp, 2) }}@endif</td>
        </tr>
        <tr>
            <td class="row-label">CNGWMPC</td><td class="row-line"></td>
            <td class="row-amount">@if($record->loan_cngwmpc > 0){{ number_format($record->loan_cngwmpc, 2) }}@endif</td>
        </tr>
        <tr><td class="row-label">UOLI</td><td class="row-line"></td><td class="row-amount"></td></tr>
        <tr>
            <td class="row-label">GSIS Real State Loan</td><td class="row-line"></td>
            <td class="row-amount">@if($record->gsis_real_estate > 0){{ number_format($record->gsis_real_estate, 2) }}@endif</td>
        </tr>
        <tr>
            <td class="row-label">GSIS Calamity Loan</td><td class="row-line"></td>
            <td class="row-amount">@if($record->pagibig_calamity > 0){{ number_format($record->pagibig_calamity, 2) }}@endif</td>
        </tr>
        <tr><td class="row-label">Nursery</td><td class="row-line"></td><td class="row-amount"></td></tr>
        <tr>
            <td class="row-label">GSIS Em. Loan</td><td class="row-line"></td>
            <td class="row-amount">@if($record->gsis_emergency > 0){{ number_format($record->gsis_emergency, 2) }}@endif</td>
        </tr>
        <tr>
            <td class="row-label">GSIS Educ loan</td><td class="row-line"></td>
            <td class="row-amount">@if($record->gsis_computer > 0){{ number_format($record->gsis_computer, 2) }}@endif</td>
        </tr>
        <tr><td class="row-label">PAG IBG Loyalty Card</td><td class="row-line"></td><td class="row-amount"></td></tr>

        <tr class="total-row">
            <td class="row-label"><strong>TOTAL DEDUCTION</strong></td>
            <td class="row-line"></td>
            <td class="row-amount"><strong>{{ number_format($record->total_deductions, 2) }}</strong></td>
        </tr>
        <tr class="net-row">
            <td class="row-label"><strong>NET PAY</strong></td>
            <td class="row-line"></td>
            <td class="row-amount"><strong>{{ number_format($record->net_pay, 2) }}</strong></td>
        </tr>
    </table>

    <!-- Signature -->
    <div class="signature-section">
        <p class="signature-name">
            {{ strtoupper(optional($record->period->createdBy)->last_name ?? '') }},
            {{ strtoupper(optional($record->period->createdBy)->first_name ?? 'ADMIN') }}
        </p>
        <p class="signature-title">AO V/PAYROLL CLERK</p>
    </div>

</div>
@empty
    <p style="padding:10mm; font-size:10pt;">No payslip records found.</p>
@endforelse

</div>
</body>
</html>