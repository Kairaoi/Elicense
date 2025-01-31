<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $license->invoice_number }}</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --text-color: #34495e;
            --light-gray: #ecf0f1;
            --white: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: var(--text-color);
        }

        .invoice-container {
            max-width: 900px;
            margin: 40px auto;
            background: var(--white);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
            padding: 20px;
        }

        .invoice-header {
            background: var(--primary-color);
            color: var(--white);
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-details p {
            margin: 5px 0;
            font-size: 14px;
        }

        .invoice-body {
            padding: 20px;
        }

        .invoice-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .company-details, .client-details {
            width: 48%;
        }

        h3 {
            font-size: 18px;
            color: var(--primary-color);
            margin-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 5px;
        }

        .company-details p, .client-details p {
            margin: 5px 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th {
            background-color: var(--secondary-color);
            color: var(--white);
            text-align: left;
            padding: 12px;
            font-weight: 600;
            font-size: 14px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid var(--light-gray);
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .subtotal-row, .vat-row, .total-row {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .total-row {
            background-color: var(--light-gray);
            font-weight: 700;
            font-size: 16px;
        }

        .total-row td {
            border-top: 2px solid var(--primary-color);
            padding: 15px 12px;
        }

        .payment-info {
            margin-top: 30px;
            background: var(--light-gray);
            padding: 20px;
            border-radius: 8px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: var(--text-color);
            font-size: 14px;
        }

        .download-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 12px 24px;
            background-color: var(--secondary-color);
            color: var(--white);
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
        }

        .download-button:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .amount-in-words {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <!-- Logo -->
            <img src="http://localhost/fisherylicense/public/images/logos.png" alt="Official Seal" class="logo" style="max-width: 150px;">
            <h1 class="invoice-title">Invoice</h1>
            <div class="invoice-details">
                <p><strong>#{{ $license->invoice_number }}</strong></p>
                <p>Date: {{ $license->created_at->format('F d, Y') }}</p>
                <p>Due: {{ $license->created_at->addDays(30)->format('F d, Y') }}</p>
            </div>
        </div>

        <div class="invoice-body">
            <div class="invoice-meta">
                <div class="company-details">
                    <h3>From</h3>
                    <p><strong>MFOR</strong></p>
                    <p>P.O. Box 64</p>
                    <p>Bairiki, Tarawa</p>
                    <p>Republic of Kiribati</p>
                </div>
                <div class="client-details">
                    <h3>Bill To</h3>
                    <p><strong>{{ $license->applicant->first_name }} {{ $license->applicant->last_name }}</strong></p>
                    <p>{{ $license->applicant->company_name }}</p>
                    <p>{{ $license->applicant->email }}</p>
                    <p>{{ $license->applicant->address }}</p>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Island</th>
                        <th>Species</th>
                        <th>Requested Quota</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($license->licenseItems->groupBy('island_id') as $islandId => $items)
                        @php
                            $island = App\Models\Reference\Island::find($islandId);
                        @endphp
                        <tr>
                            <td colspan="5" style="background-color: #f0f0f0; font-weight: bold;">
                                {{ $island->name }}
                            </td>
                        </tr>
                        @foreach($items as $item)
                        <tr>
                            <td></td>
                            <td>{{ $item->species->name }}</td>
                            <td>{{ $item->requested_quota }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>${{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                    <tr class="subtotal-row">
                        <td colspan="4">Subtotal</td>
                        <td>${{ number_format($license->total_fee, 2) }}</td>
                    </tr>
                    <tr class="vat-row">
                        <td colspan="4">VAT (12.5%)</td>
                        <td>${{ number_format($license->vat_amount, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="4">Total Amount (Including VAT)</td>
                        <td>${{ number_format($license->total_amount_with_vat, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="amount-in-words">
                <p><strong>Amount in words:</strong> 
                {{ $license->getAmountInWords() }} dollars only
                </p>
            </div>

            <div class="payment-info">
                <h3>Payment Information</h3>
                <p><strong>Account Name:</strong> Kiribati Government No.1</p>
                <p><strong>Account Number:</strong> 268253</p>
                <p><strong>BSB No:</strong> 018970</p>
                <p><strong>SWIFT Code:</strong> BKIRKIKI</p>
                <p><strong>Bank Name:</strong> ANZ Bank (Kiribati) Ltd</p>
                <p><strong>Bank Address:</strong> Bairiki, Tarawa</p>
                <p><strong>Reference:</strong> Invoice #{{ $license->invoice_number }}</p>
            </div>

            <div class="footer">
                <p>Thank you for your business. Payment is due within 30 days.</p>
                <p>If you have any questions, please contact us at finance@mfmrd.gov.ki</p>
                <p><strong>Terms & Conditions</strong></p>
                <p>1. Payment is due within 30 days of invoice date</p>
                <p>2. Please include invoice number in payment reference</p>
                <p>3. This invoice is valid for 30 days from the date of issue</p>
            </div>
        </div>
    </div>

    @if(!$isPdfDownload)
    <a href="{{ route('license.licenses.downloadInvoice', $license->id) }}" class="download-button">Download Invoice</a>
    @endif
</body>
</html>
