<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $license->invoice_number }}</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Header Styling */
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .invoice-header img {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
        }

        /* Invoice Details */
        .invoice-details {
            text-align: right;
            margin-bottom: 30px;
        }
        .invoice-details p {
            margin: 5px 0;
            font-size: 16px;
        }

        h3 {
            font-size: 20px;
            color: #007bff;
            margin-top: 30px;
            margin-bottom: 10px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }

        /* Bill To and From Section */
        .bill-info p {
            margin: 5px 0;
            font-size: 16px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f1f1f1;
            font-weight: bold;
            color: #007bff;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .total-row td {
            border-top: 2px solid #007bff;
        }

        /* Amount in Words */
        .amount-in-words {
            margin-top: 20px;
            font-size: 16px;
            font-style: italic;
        }

        /* Payment Info */
        .payment-info {
            margin-top: 30px;
        }
        .payment-info p {
            margin: 5px 0;
            font-size: 16px;
        }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 40px;
        }

        /* Button Styles */
        .download-button {
            display: inline-block;
            padding: 12px 20px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
            border: none;
        }

        /* Print Styles */
        @media print {
            body {
                background-color: #fff;
                color: #000;
            }
            .download-button {
                display: none;
            }
        }
    </style>
    <!-- Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="invoice-container">
    <!-- Invoice Header -->
    <div class="invoice-header">
        <img src="{{ public_path('images/logos.png') }}" alt="Logo">
        <p class="invoice-title">Invoice</p>
    </div>

    <!-- Invoice Details -->
    <div class="invoice-details">
        <p><strong>Invoice #:</strong> {{ $license->invoice_number }}</p>
        <p><strong>Date:</strong> {{ $license->created_at->format('F d, Y') }}</p>
        <p><strong>Due:</strong> {{ $license->created_at->addDays(30)->format('F d, Y') }}</p>
    </div>

    <!-- From Section -->
    <div class="bill-info">
        <h3>From</h3>
        <p><strong>Ministry of Fisheries and Ocean Resources</strong><br>P.O. Box 64<br>Bairiki, Tarawa<br>Republic of Kiribati</p>
    </div>

    <!-- Bill To Section -->
    <div class="bill-info">
        <h3>Bill To</h3>
        <p><strong>{{ $license->applicant->first_name }} {{ $license->applicant->last_name }}</strong><br>
            {{ $license->applicant->company_name }}<br>
            {{ $license->applicant->email }}<br>
            {{ $license->applicant->address }}
        </p>
    </div>

    <!-- Invoice Items Table -->
   <!-- Table with Requested Quota in Kg -->
<table>
    <thead>
        <tr>
            <th>Island</th>
            <th>Species</th>
            <th>Requested Quota (Kg)</th>  <!-- Changed to show Kg -->
            <th>Unit Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($license->licenseItems->groupBy('island_id') as $islandId => $items)
            @php
                $island = App\Models\Reference\Island::find($islandId);
            @endphp
            <tr class="total-row">
                <td colspan="5">{{ $island->name }}</td>
            </tr>
            @foreach($items as $item)
                <tr>
                    <td></td>
                    <td>{{ $item->species->name }}</td>
                    <td style="text-align: right;">{{ number_format($item->requested_quota, 2) }} Kg</td> <!-- Added Kg after the value -->
                    <td style="text-align: right;">{{ $currencyDetails['symbol'] }} {{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right;">{{ $currencyDetails['symbol'] }} {{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        @endforeach
        <tr class="total-row">
            <td colspan="4">Subtotal</td>
            <td style="text-align: right;">{{ $currencyDetails['symbol'] }} {{ number_format($license->total_fee, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td colspan="4">VAT (12.5%)</td>
            <td style="text-align: right;">{{ $currencyDetails['symbol'] }} {{ number_format($license->vat_amount, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td colspan="4">Total (Including VAT)</td>
            <td style="text-align: right;">{{ $currencyDetails['symbol'] }} {{ number_format($license->total_amount_with_vat, 2) }}</td>
        </tr>
    </tbody>
</table>

    <!-- Amount in Words Section -->
    <div class="amount-in-words">
        <p><strong>Amount in words:</strong> 
        {{ $license->getAmountInWords() }} {{ $currencyDetails['name'] }} dollars only
        </p>
    </div>

    <!-- Payment Info Section -->
    <div class="payment-info">
        <h3>Payment Information</h3>
        <p><strong>Account Name:</strong> Kiribati Government No.1</p>
        <p><strong>Account Number:</strong> 268253</p>
        <p><strong>BSB No:</strong> 018970</p>
        <p><strong>SWIFT Code:</strong> BKIRKIKI</p>
        <p><strong>Bank Name:</strong> ANZ Bank (Kiribati) Ltd</p>
        <p><strong>Bank Address:</strong> Bairiki, Tarawa</p>
        <p><strong>Reference:</strong> Invoice #{{ $license->invoice_number }}</p>
        <p><strong>Currency:</strong> {{ $currencyDetails['full_name'] }}</p>
    </div>

    <!-- Footer Section -->
    <div class="footer">
        <p>Thank you for your business. Payment is due within 30 days.</p>
        <p>If you have any questions, please contact us at finance@mfor.gov.ki</p>
        <p><strong>Terms & Conditions</strong></p>
        <p>1. Payment is due within 30 days of invoice date</p>
        <p>2. Please include invoice number in payment reference</p>
        <p>3. This invoice is valid for 30 days from the date of issue</p>
    </div>

    <!-- PDF Download Button -->
    @if(!$isPdfDownload)
        <div style="text-align: center;">
            <a href="{{ route('license.licenses.downloadInvoice', $license->id) }}" class="download-button">
                Download PDF
            </a>
        </div>
    @endif
</div>

</body>
</html>
