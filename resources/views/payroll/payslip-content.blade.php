    <div class="payslip-doc">
        <!-- Header -->
        <div class="payslip-header">
            <h1>Republic of the Philippines</h1>
            <h2>PROVINCE OF CAMARINES NORTE</h2>
            <h3>D A E T</h3>
            <h4>OFFICE OF THE PROVINCIAL AGRICULTURIST</h4>
            <h4>PAY SLIP</h4>
            <div class="pay-period">For the Period: <strong>{{ $record->period->period_label }}</strong></div>
        </div>

        <!-- Employee Information -->
        <div class="emp-info-box">
            <div class="emp-info-grid">
                <div class="emp-info-item">
                    <span class="emp-info-label">Name:</span>
                    <span class="emp-info-value">
                        {{ strtoupper($record->employee->last_name) }}, 
                        {{ strtoupper($record->employee->first_name) }}
                        @if($record->employee->extension_name) {{ strtoupper($record->employee->extension_name) }}@endif
                    </span>
                </div>
                <div class="emp-info-item">
                    <span class="emp-info-label">Employee ID:</span>
                    <span class="emp-info-value">{{ $record->employee_id }}</span>
                </div>
                <div class="emp-info-item">
                    <span class="emp-info-label">Position:</span>
                    <span class="emp-info-value">{{ $record->designation ?? optional($record->employee->position)->position_code ?? 'N/A' }}</span>
                </div>
                <div class="emp-info-item">
                    <span class="emp-info-label">Gross Salary:</span>
                    <span class="emp-info-value">₱{{ number_format($record->gross_salary, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Earnings Section -->
        <div class="payslip-section">
            <div class="section-title earnings">Allowances</div>
            <table class="payslip-table">
                <tr>
                    <td class="label-cell">PERA</td>
                    <td class="amount-cell">{{ number_format($record->allowance_pera, 2) }}</td>
                </tr>
                @if($record->allowance_rata > 0)
                <tr>
                    <td class="label-cell">RATA</td>
                    <td class="amount-cell">{{ number_format($record->allowance_rata, 2) }}</td>
                </tr>
                @endif
                @if($record->allowance_ta > 0)
                <tr>
                    <td class="label-cell">TA</td>
                    <td class="amount-cell">{{ number_format($record->allowance_ta, 2) }}</td>
                </tr>
                @endif
                @if($record->allowance_other > 0)
                <tr>
                    <td class="label-cell">Other Allowance</td>
                    <td class="amount-cell">{{ number_format($record->allowance_other, 2) }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Deductions Section -->
        <div class="payslip-section">
            <div class="section-title deductions">Less Deductions</div>
            <table class="payslip-table">
                <!-- GSIS -->
                @if($record->gsis_ee > 0 || $record->gsis_policy > 0 || $record->gsis_emergency > 0 || 
                    $record->gsis_real_estate > 0 || $record->gsis_mpl > 0 || $record->gsis_mpl_lite > 0 || 
                    $record->gsis_gfal > 0 || $record->gsis_computer > 0 || $record->gsis_conso > 0)
                <tr class="subtotal-row">
                    <td class="label-cell">GSIS</td>
                    <td class="amount-cell"></td>
                </tr>
                @if($record->gsis_ee > 0)
                <tr class="sub-item">
                    <td class="label-cell">Employee Share</td>
                    <td class="amount-cell">{{ number_format($record->gsis_ee, 2) }}</td>
                </tr>
                @endif
                @if($record->gsis_ec > 0)
                <tr class="sub-item">
                    <td class="label-cell">ECF</td>
                    <td class="amount-cell">{{ number_format($record->gsis_ec, 2) }}</td>
                </tr>
                @endif
                @if($record->gsis_policy > 0)
                <tr class="sub-item">
                    <td class="label-cell">GSIS Policy Loan</td>
                    <td class="amount-cell">{{ number_format($record->gsis_policy, 2) }}</td>
                </tr>
                @endif
                @if($record->gsis_emergency > 0)
                <tr class="sub-item">
                    <td class="label-cell">GSIS Emergency Loan</td>
                    <td class="amount-cell">{{ number_format($record->gsis_emergency, 2) }}</td>
                </tr>
                @endif
                @if($record->gsis_real_estate > 0)
                <tr class="sub-item">
                    <td class="label-cell">GSIS Real Estate Loan</td>
                    <td class="amount-cell">{{ number_format($record->gsis_real_estate, 2) }}</td>
                </tr>
                @endif
                @if($record->gsis_mpl > 0)
                <tr class="sub-item">
                    <td class="label-cell">GSIS MPL</td>
                    <td class="amount-cell">{{ number_format($record->gsis_mpl, 2) }}</td>
                </tr>
                @endif
                @if($record->gsis_mpl_lite > 0)
                <tr class="sub-item">
                    <td class="label-cell">GSIS MPL Lite</td>
                    <td class="amount-cell">{{ number_format($record->gsis_mpl_lite, 2) }}</td>
                </tr>
                @endif
                @if($record->gsis_gfal > 0)
                <tr class="sub-item">
                    <td class="label-cell">GSIS GFAL</td>
                    <td class="amount-cell">{{ number_format($record->gsis_gfal, 2) }}</td>
                </tr>
                @endif
                @if($record->gsis_computer > 0)
                <tr class="sub-item">
                    <td class="label-cell">GSIS Computer Loan</td>
                    <td class="amount-cell">{{ number_format($record->gsis_computer, 2) }}</td>
                </tr>
                @endif
                @if($record->gsis_conso > 0)
                <tr class="sub-item">
                    <td class="label-cell">GSIS Consolidated Loan</td>
                    <td class="amount-cell">{{ number_format($record->gsis_conso, 2) }}</td>
                </tr>
                @endif
                @endif

                <!-- PAG-IBIG -->
                @if($record->pagibig_ee > 0 || $record->pagibig_mpl > 0 || $record->pagibig_calamity > 0)
                <tr class="subtotal-row">
                    <td class="label-cell">PAG-IBIG</td>
                    <td class="amount-cell"></td>
                </tr>
                @if($record->pagibig_ee > 0)
                <tr class="sub-item">
                    <td class="label-cell">Employee Share</td>
                    <td class="amount-cell">{{ number_format($record->pagibig_ee, 2) }}</td>
                </tr>
                @endif
                @if($record->pagibig_mpl > 0)
                <tr class="sub-item">
                    <td class="label-cell">PAG-IBIG MPL</td>
                    <td class="amount-cell">{{ number_format($record->pagibig_mpl, 2) }}</td>
                </tr>
                @endif
                @if($record->pagibig_calamity > 0)
                <tr class="sub-item">
                    <td class="label-cell">PAG-IBIG Calamity Loan</td>
                    <td class="amount-cell">{{ number_format($record->pagibig_calamity, 2) }}</td>
                </tr>
                @endif
                @endif

                <!-- PhilHealth -->
                @if($record->philhealth_ee > 0)
                <tr class="subtotal-row">
                    <td class="label-cell">PhilHealth</td>
                    <td class="amount-cell"></td>
                </tr>
                <tr class="sub-item">
                    <td class="label-cell">Employee Share</td>
                    <td class="amount-cell">{{ number_format($record->philhealth_ee, 2) }}</td>
                </tr>
                @endif

                <!-- Other Deductions -->
                @if($record->withholding_tax > 0)
                <tr>
                    <td class="label-cell">Withholding Tax</td>
                    <td class="amount-cell">{{ number_format($record->withholding_tax, 2) }}</td>
                </tr>
                @endif
                @if($record->loan_dbp > 0)
                <tr>
                    <td class="label-cell">DBP Loan</td>
                    <td class="amount-cell">{{ number_format($record->loan_dbp, 2) }}</td>
                </tr>
                @endif
                @if($record->loan_lbp > 0)
                <tr>
                    <td class="label-cell">LBP Loan</td>
                    <td class="amount-cell">{{ number_format($record->loan_lbp, 2) }}</td>
                </tr>
                @endif
                @if($record->loan_cngwmpc > 0)
                <tr>
                    <td class="label-cell">CNGWMPC</td>
                    <td class="amount-cell">{{ number_format($record->loan_cngwmpc, 2) }}</td>
                </tr>
                @endif
                @if($record->loan_paracle > 0)
                <tr>
                    <td class="label-cell">PARACLE</td>
                    <td class="amount-cell">{{ number_format($record->loan_paracle, 2) }}</td>
                </tr>
                @endif
                @if($record->overpayment > 0)
                <tr>
                    <td class="label-cell">Overpayment</td>
                    <td class="amount-cell">{{ number_format($record->overpayment, 2) }}</td>
                </tr>
                @endif

                <!-- Total Deductions -->
                <tr class="total-row">
                    <td class="label-cell">TOTAL DEDUCTION</td>
                    <td class="amount-cell">{{ number_format($record->total_deductions, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Net Pay -->
        <div class="net-pay-box">
            <div class="net-pay-label">NET PAY</div>
            <div class="net-pay-amount">₱{{ number_format($record->net_pay, 2) }}</div>
        </div>

        <!-- Footer with Signatures -->
        <div class="payslip-footer">
            <div class="signature-grid">
                <div class="signature-box">
                    <div class="signature-line">
                        <div class="signature-name">{{ strtoupper(optional($record->period->createdBy)->full_name ?? 'ADMIN') }}</div>
                        <div class="signature-title">AO V/Payroll Clerk</div>
                    </div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">
                        <div class="signature-name">
                            {{ strtoupper($record->employee->last_name) }}, 
                            {{ strtoupper($record->employee->first_name) }}
                            @if($record->employee->extension_name) {{ strtoupper($record->employee->extension_name) }}@endif
                        </div>
                        <div class="signature-title">Employee Signature</div>
                    </div>
                </div>
            </div>
        </div>
    </div>