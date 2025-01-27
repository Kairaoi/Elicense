<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shark Fin Regulation 2024 License</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            line-height: 1.2;
            position: relative;
        }

        .watermark-revoked {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(255, 0, 0, 0.5);
            pointer-events: none;
            z-index: 1000;
            white-space: nowrap;
            user-select: none;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .watermark-revoked-text {
            position: fixed;
            top: 60%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(255, 0, 0, 0.5);
            pointer-events: none;
            z-index: 1000;
            white-space: nowrap;
            user-select: none;
            font-weight: bold;
        }

        @media print {
            .watermark-revoked,
            .watermark-revoked-text {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header p {
            margin: 3px;
            font-size: 14px;
        }

        h1 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 8px;
        }

        h2 {
            text-align: center;
            font-size: 18px;
            margin: 10px 0;
        }

        h3 {
            font-size: 16px;
            margin-top: 8px;
            margin-bottom: 5px;
            text-align: center;
            font-weight: bold;
            color: #2c3e50;
        }

        .license-number {
            font-weight: bold;
            margin: 5px 0;
            text-align: center;
        }

        .conditions {
            margin: 8px 0;
            padding-left: 15px;
            font-size: 14px;
        }

        .conditions li {
            margin-bottom: 3px;
        }

        .signature {
            margin-top: 15px;
            text-align: right;
            font-size: 14px;
        }

        .content {
            max-width: 700px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        @media print {
            .watermark {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .coat-of-arms {
            width: 100px; /* Adjust size as necessary */
            margin: 0 auto; /* Center the image */
        }
    </style>
</head>

<body>
@if(isset($isRevoked) && $isRevoked)
        <div class="watermark-revoked">REVOKED</div>
        <div class="watermark-revoked-text">License No Longer Valid</div>
    @else
        <div class="watermark">DRAFTED</div>
    @endif
    <div class="watermark">DRAFTED</div>
    <div class="content">
        <div class="header">
        <img src="{{ asset('images/logo.jpg') }}" alt="Logo" style="height: 60px;">
            <p><strong>GOVERNMENT OF KIRIBATI</strong></p>
            <p>MINISTRY OF FISHERIES AND MARINE RESOURCES DEVELOPMENT</p>
            <p>P.O.Box 64, Bairiki, Tarawa, Republic of Kiribati</p>
            <p>Tel: (686) 21099 | Fax: (686) 21120</p>
            <p>Email: <a href="mailto:info@mfmrd.gov.ki">info@mfmrd.gov.ki</a></p>
        </div>

        <h1>Shark Fin Regulation 2024</h1>
        <h2>{{ $license->getDisplayLicenseTypeName() }}<br>[Regulation 11(1)]</h2>
        
        <div class="license-number"><strong>License No.</strong> {{ $license->license_number }}</div>

        <p>HAVING SATISFIED ALL the necessary legal requirements prescribed by the above-mentioned regulation, a License is hereby granted to:</p>
        
        <p style="text-align: center;"><strong>{{ $license->applicant->first_name ?? 'N/A' }}  {{ $license->applicant->last_name ?? 'N/A' }}</strong></p>
        
        <h3>Subject to the following Conditions:</h3>
        
        <ol class="conditions">
            <li>License is not transferable;</li>
            <li>License valid until the allocated shark fin quota is reached or expiry date is due;</li>
            <li>License must be displayed prominently at the Head Office of the Business;</li>
            <li>Must be presented to an authorized officer upon request;</li>
            <li>Licensee must comply with all legal requirements and cooperate with authorized officers during inspections or investigations;</li>
            <li>Licensee must not exceed the allowed shark fin harvest quota as stated in the license;</li>
            <li>Licensee must ensure shark fins are collected only from permitted areas and within the allocated time period;</li>
            <li>Shark fin collection must be documented, and detailed reports must be submitted to the Ministry as per the reporting schedule;</li>
            <li>Licensee must adhere to all sustainability guidelines and ensure minimal harm to non-target species;</li>
            <li>It is prohibited to harvest or trade fins from protected shark species;</li>
            <li>Licensee must cease operations immediately if found in violation of the Shark Fin Regulation;</li>
            <li>License fees must be paid by November 30th of each fiscal year.</li>
        </ol>

        <p>
            Issued this {{ \Carbon\Carbon::parse($license->issue_date)->format('jS') }} day of {{ \Carbon\Carbon::parse($license->issue_date)->format('F') }} {{ \Carbon\Carbon::parse($license->issue_date)->format('Y') }},
            Expiry date: {{ \Carbon\Carbon::parse($license->expiry_date)->format('jS') }} day of {{ \Carbon\Carbon::parse($license->expiry_date)->format('F') }} {{ \Carbon\Carbon::parse($license->expiry_date)->format('Y') }}
        </p>

        <div class="signature">
            <p>…...........................</p>
            <p>Director of Fisheries</p>
        </div>
    </div>
</body>

</html>
