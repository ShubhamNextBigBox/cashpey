<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Repayment Schedule</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #4743fa; color: white; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ cmp()->companyName }}</h2>
        <h3>Repayment Schedule</h3>
        <p>Loan Account Number: {{ $templateData->leadID }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Payment Date</th>
                <th class="text-right">Opening Balance</th>
                <th class="text-right">EMI Amount</th>
                <th class="text-right">Principal Amount</th>
                <th class="text-right">Interest Amount</th>
                <th class="text-right">Closing Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalOpening = 0;
                $totalEMI = 0;
                $totalPrincipal = 0;
                $totalInterest = 0;
                $totalClosing = 0;
            @endphp

            @foreach($repaymentScheduleSanction as $key => $repayment)
                @php
                    $totalOpening += $repayment->openingBalance;
                    $totalEMI += $repayment->emiAmount;
                    $totalPrincipal += $repayment->principalAmount;
                    $totalInterest += $repayment->interestAmount;
                    $totalClosing += $repayment->closingBalance;
                @endphp
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ df($repayment->paymentDate) }}</td>
                    <td class="text-right">{{ nf($repayment->openingBalance) }}</td>
                    <td class="text-right">{{ nf($repayment->emiAmount) }}</td>
                    <td class="text-right">{{ nf($repayment->principalAmount) }}</td>
                    <td class="text-right">{{ nf($repayment->interestAmount) }}</td>
                    <td class="text-right">{{ nf($repayment->closingBalance) }}</td>
                </tr>
            @endforeach

            <tr style="font-weight: bold;">
                <td colspan="2">Total</td>
                <td class="text-right">{{ nf($totalOpening) }}</td>
                <td class="text-right">{{ nf($totalEMI) }}</td>
                <td class="text-right">{{ nf($totalPrincipal) }}</td>
                <td class="text-right">{{ nf($totalInterest) }}</td>
                <td class="text-right">{{ nf($totalClosing) }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right;">
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>