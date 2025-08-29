<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Account Statement - {{ $loanData->loanNo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.5;
            color: #000000;
            background: #f8fafc;
            padding: 15px;
            font-size: 12px;
        }

        .statement-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        /* MODERN HEADER SECTION */
        .header {
            color: white;
            padding: 25px 25px 3px 25px;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: #003366;
        }

        .logo-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-shrink: 0;
        }

        .company-info h1 {
            font-size: 28px;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .company-tagline {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
        }

        .statement-title-container {
          /*  flex-grow: 1;
            min-width: 300px;*/
            text-align: center;
        }

        .statement-title {
            background: white;
            color: #2e0cff;
            font-size: 20px;
            font-weight: 700;
            padding: 18px;
            border-radius: 8px;
            /*box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);*/
            /*border: 2px solid #f0f6ff;*/
            display: inline-block;
            width: 100%;
            max-width: 500px;
        }

        .account-number {
            font-size: 16px;
            font-weight: 600;
            margin-top: 8px;
            color: #003366;
        }

        /* MODERN CUSTOMER INFO SECTION */
        .customer-info {
            display: flex;
            justify-content: space-between;
            margin: 25px;
            gap: 25px;
        }

        .customer-details {
            flex: 1;
            background: #f0f6ff;
            padding: 18px;
            border-radius: 10px;
            border-left: 5px solid #2e0cff;
            border-right: 5px solid #2e0cff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .customer-details h3 {
            color: #003366;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: 600;
            border-bottom: 2px solid #2e0cff;
            padding-bottom: 8px;
        }

        .customer-details strong {
            color: #003366;
            font-weight: 600;
        }

        .customer-details div {
            line-height: 1.7;
            font-size: 13px;
        }

        /* ORIGINAL STYLES FOR TABLES AND REST */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
            background: #2e0cff;
            color: white;
            padding: 8px;
            border-bottom: 1px solid #003366;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #003366;
        }

        th {
            background: #2e0cff;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            font-size: 11px;
        }

        td {
            padding: 6px;
            font-size: 11px;
        }

        .loan-details-table td {
            font-weight: bold;
            width: 25%;
            color: #003366;
        }
        #overdue-table td {
             width: 25%;
        }
        #loan-details-table td {
             width: 25%;
        }
        
        .loan-details-table-bg {
           background-color: #f0f6ff; 
        }

        .transaction-table th, .transaction-table td {
            text-align: center;
        }

        .transaction-table td  {
            text-align: left;
        }

        .debit {
            color: #000000;
        }

        .credit {
            color: #000000;
        }

        .total-row {
            font-weight: bold;
            background: #2e0cff;
            color: white;
        }

        .footer {
            margin-top: 30px;
            border-top: 2px solid #003366;
            padding-top: 15px;
            font-size: 10px;
            background-color: #f0f6ff;
            padding: 15px;
        }

        .footer p {
            margin-bottom: 5px;
            text-align: center;
            color: #003366;
        }

        .notice {
            font-style: italic;
            margin-top: 20px;
            border-top: 1px dashed #003366;
            padding-top: 10px;
            background-color: #f0f6ff;
            padding: 15px;
            color: #003366;
        }

        .page-number {
            text-align: right;
            margin-top: 20px;
            font-size: 10px;
            color: #003366;
        }

        .loan-summary-table {
            margin-bottom: 20px;
            text-align: center;
            text-wrap: nowrap;
        }

        .statement-date {
            color: #003366;
            font-weight: bold;
            text-align:right;
        }

        .amount {
            text-align: right!important;
            font-weight: bold;
        }

        .negative {
            /* color: #cc0000; */
        }

        .positive {
            color: #006600;
        }

        /* Add padding to content area */
        .content-area {
            padding: 20px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .statement-container {
                border: none;
                padding: 0;
                width: 100%;
                max-width: none;
                border-radius: 0;
                box-shadow: none;
            }

            .header {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .header {
                padding: 20px;
            }

            .customer-info {
                flex-direction: column;
                margin: 20px;
                gap: 20px;
            }
            
            .customer-details {
                width: 100%;
                padding: 20px;
            }
            
            .logo-section {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .company-info h1 {
                font-size: 24px;
            }

            .statement-title {
                font-size: 18px;
                padding: 15px;
            }
            
            .statement-date {
                margin-top: 10px;
            }

        }
    </style>
</head>
<body>
    <div class="statement-container">
        <!-- MODERN HEADER -->
        <div class="header">
            <div class="logo-section">
                <div class="logo-container">
                    <img src="https://app.cashpey.in/storage/logo/CP1.png" alt="Cashpey Logo" style="height: 70px; border-radius: 8px; box-shadow: 0 4px 8px rgba(255,255,255,0.2);">
                </div>
                <div class="statement-title-container">
                    <div class="statement-title">
                        LOAN ACCOUNT STATEMENT
                        <div class="account-number">Account No: {{ $loanData->loanNo }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODERN CUSTOMER INFO -->
        <div class="customer-info">
            <div class="customer-details">
                <h3>Customer Information</h3>
                
                <!-- Statement Date aligned to right -->
                <div style="float: right; margin-top: -50px;font-weight: bold;color: #003366;">
                    Statement Date: {{ date('d-m-Y') }}
                </div>

                <div style="clear: both;"></div> <!-- Clear float -->

                <div>
                    <strong>Name:</strong> {{ $profileData->name }}<br>
                    <strong>Mailing Address:</strong> {{ $profileData->address }},<br>
                    {{ getUserNameById('lms_cities', 'cityID', $profileData->city, 'cityName')}}, 
                    {{ getUserNameById('lms_states', 'stateID', $profileData->state, 'stateName')}}, 
                    {{ $profileData->pincode }}<br>
                    <strong>Phone:</strong> {{ $profileData->mobile }}<br>
                    <strong>Email:</strong> {{ $profileData->email }}
                </div>
            </div>
        </div>


        <!-- ORIGINAL CONTENT AREA -->
        <div class="content-area">


            <table class="loan-summary-table">
                        <thead>
                            <tr>
                                <th>Loan Amount</th>
                                <th>Rate Of Interest</th>
                                <th>Overdue Charges</th>
                                <th>Total Tenure</th>
                                <th>EMI(s) Paid</th>
                                <th>EMI(s) Pending</th>
                                <th>Future EMI(s) No. / Amt. </th>
                                <th>Total Overdue as on <br>{{date('d-m-Y')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="">{{ nf($approvalData->loanAmtApproved) }}</td>
                                <td>{{ $approvalData->roi}}% p.a.</td>
                                <td> 0</td>
                                <td>{{ $approvalData->tenure }}</td>
                                <td>{{ $collections->count() }} / {{ nf($collections->sum('collectedAmount')) }}</td>
                                <td>
                                    @if($collections->count() == $repaymentScheduleDisbursed->count())
                                       0 / 0
                                    @else
                                       {{ $repaymentScheduleDisbursed->count() }} / {{round($repaymentScheduleDisbursed->sum('emiAmount') - $collections->sum('collectedAmount'))}}
                                    @endif    
                                </td>
                                <td>
                                    @if($collections->count() == $repaymentScheduleDisbursed->count())
                                        Nil
                                    @else
                                        {{$collections->count() + 1}}
                                    @endif  /
                                    @if($collections->count() == $repaymentScheduleDisbursed->count())
                                        Nil
                                    @else
                                        {{ nf($repaymentScheduleDisbursed->first()->emiAmount) }}
                                    @endif  
                                </td>
                                <td>
                                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
            <table border="1" id="loan-details-table" cellpadding="6" cellspacing="0" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 14px;">
                <tr>
                    <td class="section-title"><strong>Name of Financier</strong></td>
                    <td colspan="3" style="text-align:center;" class="section-title"><strong>Naman Commodities Pvt. Ltd.</strong></td>
                </tr>
              
                <tr>
                    <td class="loan-details-table-bg">Loan Amount:</td>
                    <td>{{ nf($approvalData->loanAmtApproved) }}</td>
                    <td class="loan-details-table-bg">Customer ID:</td>
                    <td>{{ $profileData->contactID }}</td>
                </tr>
                <tr>
                    <td class="loan-details-table-bg">ROI (Annual):</td>
                    <td>{{ $approvalData->roi }}%</td>
                    <td class="loan-details-table-bg">Branch:</td>
                    <td>{{ getUserNameById('lms_cities', 'cityID', $approvalData->branch, 'cityName') }}</td>
                </tr>
                <tr>
                    <td class="loan-details-table-bg">Total Tenure:</td>
                    <td>{{ $approvalData->tenure }}</td>
                    <td class="loan-details-table-bg">Product:</td>
                    <td>Personal Loan</td>
                </tr>
                <tr>
                    <td class="loan-details-table-bg">Loan Disbursal Date:</td>
                    <td>{{ df($loanData->disbursalDate) }}</td>
                    <td class="loan-details-table-bg">Currency:</td>
                    <td>INR</td>
                </tr>
                <tr>
                    <td class="loan-details-table-bg">Amount Disbursed:</td>
                    <td>{{ nf($approvalData->disbursementAmount) }}</td>
                    <td class="loan-details-table-bg">Loan Account No.:</td>
                    <td>{{ $loanData->loanNo }}</td>
                </tr>
                <tr>
                    <td class="loan-details-table-bg">EMI Amount:</td>
                    <td>{{ nf($repaymentScheduleDisbursed->first()->emiAmount) }}</td>
                    <td class="loan-details-table-bg">Loan Status:</td>
                    <td>
                        @if($loanData->status == 'Disbursed')
                            <strong>Active</strong>
                        @elseif($loanData->status == 'EMI Running')
                            <strong>Active</strong>
                        @elseif($loanData->status == 'Closed')
                            <strong>Closed</strong>
                        @elseif($loanData->status == 'Pre Closed')
                            <strong>Pre Closed</strong>
                        @elseif($loanData->status == 'Settlement')
                            <strong>Settled</strong>    
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="loan-details-table-bg">First EMI Date:</td>
                    <td>{{ df($repaymentScheduleDisbursed->first()->paymentDate) }}</td>
                    <td class="loan-details-table-bg">Current Asset Classification:</td>
                    <td>Regular</td>
                </tr>
                <tr>
                    <td class="loan-details-table-bg">Last EMI Date:</td>
                    <td>{{ df($repaymentScheduleDisbursed->last()->paymentDate) }}</td>
                    <td class="loan-details-table-bg">Old Linked LAN:</td>
                    <td>{{$previousLoanNo}}</td>
                </tr>
                <tr>
                    <td class="loan-details-table-bg">Interest Rate Type:</td>
                    <td>Fixed</td>
                    <td class="loan-details-table-bg">Frequency of EMI:</td>
                    <td>Monthly</td>
                </tr>
                <tr>
                    <td class="loan-details-table-bg">Loan Type:</td>
                    <td>{{ $reloanCheck > 1 ? 'Reloan' : 'New Loan' }}</td>
                    <td class="loan-details-table-bg">Repayment Mode:</td>
                    <td>e-Nach</td>
                    
                </tr>
                <tr>
                    <td class="loan-details-table-bg">Loan Closure Date:</td>
                    <td> </td>
                </tr>
            </table>

            <table border="1" id="overdue-table" cellpadding="6" cellspacing="0" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 14px;">
                <tr>
                    <td colspan="4" class="section-title" style="text-align:center;"><strong>Total Overdues as on : {{date('d-m-Y')}}</strong></td>
                </tr>
              
                <tr>
                    <td class="loan-details-table-bg">EMI(s) Paid</td>
                    <td>{{ $collections->count() }}</td>
                    <td class="loan-details-table-bg">EMI(s) Overdue</td>
                    <td>--</td>
                </tr>
                <tr>
                    <td class="loan-details-table-bg">Principle Overdue Rs.</td>
                    <td>--</td>
                    <td class="loan-details-table-bg">Interest Overdue (Rs.)</td>
                    <td>--</td>
                </tr>
                <tr>
                    <td class="loan-details-table-bg">Penalty Charges</td>
                    <td>--</td>
                    <td class="loan-details-table-bg">Bounce chgs/GST amount</td>
                    <td>--</td>
                </tr>
                <tr>
                    <td class="loan-details-table-bg">Future EMI(s)</td>
                    <td>@if($collections->count() == $repaymentScheduleDisbursed->count())
                            0
                        @else
                            {{ $repaymentScheduleDisbursed->count() - $collections->count() }}
                        @endif    
                    </td>  
                    <td class="loan-details-table-bg">Net Receivable amount</td>
                    <td>--</td>
                </tr>
            </table>
            <div class="customer-info">
            <div class="customer-details">
                <h3>Statement of Loan Account Generation</h3>
            </div>
        </div>
            <table class="transaction-table">
                     <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Enach No.</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Closing Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                                @if(optional($loanData)->status == 'Disbursed')
                                    @php
                                        // Initialize variables to calculate totals
                                        $totalDebit = 0;
                                        $totalCredit = 0;
                                        $runningBalance = 0;
                                        $isFirstPayment = 1;  
                                        $collectedAmount = 0;
                                    @endphp
                                    
                                    @php 
                                        $adminFeeWithGst = $approvalData->adminFee + $approvalData->adminGstAmount;  
                                        $stampDuty = $approvalData->stampDuty;
                                        $gapInterest = $approvalData->preEmiInterest;
                                        
                                        // Calculate footer amounts
                                        $amtPaidToCust = $approvalData->loanAmtApproved - $adminFeeWithGst - $stampDuty - $gapInterest;
                                        $totalDebit += $amtPaidToCust;
                                        
                                        if($approvalData->preEmiInterestDaysDiff < 30) {
                                            $totalDebit += $gapInterest;
                                            $amtPaidToCust+=$gapInterest;
                                        }
                                        
                                        $totalDebit += $stampDuty;
                                        $totalDebit += $adminFeeWithGst;
                                        $totalCredit += $approvalData->loanAmtApproved;
                                        
                                        $closing1 = $approvalData->loanAmtApproved - $adminFeeWithGst;
                                        $closing2 = $approvalData->loanAmtApproved - $adminFeeWithGst - $stampDuty;
                                        $closing3 = $approvalData->loanAmtApproved - $adminFeeWithGst - $stampDuty - $gapInterest;
                                        $closing = $totalDebit + $closing1 + $closing2 + $closing3;
                                    @endphp
                            
                                    @foreach($paidSchedule as $key => $repayment)
                                        @php 
                                            $installmentNo = $loop->count - $loop->iteration + 1;
                                           
                                        @endphp
                                        
                                        @php
                                            // Update total debit
                                            $totalDebit += $repayment->emiAmount;
                            
                                            // If payment exists, show Payment Received
                                            if (isset($collections[$installmentNo])) {
                                                // Payment Received Row (if exists)
                                                $collectedAmount = $collections[$installmentNo]->collectedAmount;
                                                $remainingAmount = $repayment->emiAmount - $collectedAmount;
                                                $totalCredit += $collectedAmount;
                                                $runningBalance += $remainingAmount;
                            
                                                // Only set to false after the first payment is received
                                            } else {
                                                // If no payment is received, the remaining balance will just be the full due amount
                                                $remainingAmount = $repayment->emiAmount;
                                                $runningBalance += $remainingAmount;
                                            }
                                        @endphp
                            
                                        <!-- Display Pre EMI Interest only after the first payment is received -->
                                        @if($approvalData->preEmiInterestDaysDiff < 30)
                                            @if($installmentNo==1)
                                                @if($preEmiAmountExists > 0)
                                                    <tr>
                                                        <td>{{ df($collections[$installmentNo]->collectedDate ?? '--') }}</td>
                                                        <td>Pre Emi Interest (paid to customer)</td>
                                                        <td>{{ optional($loanData)->enachID }}</td>
                                                        <td>{{ nf($gapInterest) }}</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td>{{ df($collections[$installmentNo]->collectedDate) }}</td>
                                                    <td>Pre Emi Interest (excess - payable to customer)</td>
                                                    <td>--</td>
                                                    <td>0</td>
                                                    <td>{{ nf($gapInterest) }}</td>
                                                    <td>-{{ nf($gapInterest) }}</td>
                                                </tr>
                                            @endif    
                                        @endif
                                       
                                        <!-- Row for Payment Received (if exists) -->
                                        @if(isset($collections[$installmentNo]))
                                           @if($collectedAmount > 0)
                                            <tr>
                                                <td>{{ df($collections[$installmentNo]->collectedDate) }}</td>
                                                <td>Payment Received {{ $installmentNo }}</td>
                                                <td>{{ $collections[$installmentNo]->enachID ?? '-' }}</td>
                                                <td>0</td>
                                                <td>{{ nf($collectedAmount) }}</td>
                                                <td>{{ nf($remainingAmount) }}</td>
                                            </tr>
                                           @endif
                                        @endif
                                        
                                        <!-- Row for Installment Due -->
                                        <tr>
                                            <td>{{ df($repayment->paymentDate) }}</td>
                                            <td>Due for Installment {{ $installmentNo }}</td>
                                            <td>--</td>
                                            <td>{{ nf($repayment->emiAmount) }}</td>
                                            <td>0</td>
                                            <td>{{ nf($repayment->emiAmount) }}</td>
                                        </tr>
                                         @php $isFirstPayment = 0; @endphp 
                                    @endforeach
                             
                            </tbody>
 
                        <tfoot>
                            
                           
                            @if($approvalData->preEmiInterestDaysDiff < 30)
                              <tr>
                                <td>{{ df(optional($loanData)->disbursalDate) }}</td>
                                <td>Amount Paid to Cust plus 18% GST</td>
                                <td>{{optional($loanData)->enachID}}</td>
                                <td>{{ nf($amtPaidToCust) }}</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            @else
                            <tr>
                                <td>{{ df(optional($loanData)->disbursalDate) }}</td>
                                <td>Amount Paid to Cust plus 18% GST</td>
                                <td>{{optional($loanData)->enachID}}</td>
                                <td>{{ nf($amtPaidToCust) }}</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>{{ df(optional($loanData)->disbursalDate) }}</td>
                                <td>Pre EMI interest</td>
                                <td>--</td>
                                <td>{{ nf($gapInterest) }}</td>
                                <td>0</td>
                                <td>-{{ nf($approvalData->loanAmtApproved - $adminFeeWithGst - $stampDuty - $gapInterest) }}</td>
                            </tr>
                            
                            @endif

                            
                            <tr>
                                <td>{{ df(optional($loanData)->disbursalDate) }}</td>
                                <td>Due for Stamp Duty</td>
                                <td>--</td>
                                <td>{{ nf($stampDuty) }}</td>
                                <td>0</td>
                                <td>-{{ nf($approvalData->loanAmtApproved - $adminFeeWithGst - $stampDuty) }}</td>
                            </tr>
                            <tr>
                                <td>{{ df(optional($loanData)->disbursalDate) }}</td>
                                <td>Due for Processing Fees from Customer</td>
                                <td>--</td>
                                <td>{{ nf($adminFeeWithGst) }}</td>
                                <td>0</td>
                                <td>-{{ nf($approvalData->loanAmtApproved - $adminFeeWithGst) }}</td>
                            </tr>
                            <tr>
                                <td>{{ df(optional($loanData)->disbursalDate) }}</td>
                                <td>Amt Financed - Payable</td>
                                <td>--</td>
                                <td>0</td>
                                <td>{{ nf($approvalData->loanAmtApproved) }}</td>
                                <td>-{{ nf($approvalData->loanAmtApproved) }}</td>
                            </tr>
                            <tr style="background:#4743fa;position:sticky;color: #ffffff;">
                                <td colspan="3" class="text-white"><strong>Total</strong></td>
                                <td class="text-white"><strong>{{ nf($totalDebit) }}</strong></td>
                                <td class="text-white"><strong>{{ nf($totalCredit) }}</strong></td>
                                <td class="text-white"><strong>-{{ nf($closing) }}</strong></td>
                            </tr>
                        </tfoot>
                        @endif
            </table>

            <div class="notice">
                <strong>End of the Report *****</strong><br>
                Note: This is system generated statement, hence it does not require any signature. 
                <!-- You can access your loan details through our customer's portal on website. -->
            </div>

            <div class="page-number">
                Page 1 of 1<br>
                Generated on: {{ date('d-M-Y H:i:s') }} | Cashpey
            </div>

            <div class="footer">
                <p>For any queries regarding this statement, please contact our customer service</p>
                <p>CASHPEY | Registered Office</p>
            </div>
        </div>
    </div>
</body>
</html>