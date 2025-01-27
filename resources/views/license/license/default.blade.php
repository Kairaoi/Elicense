<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Default License Regulation 2024 License</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            line-height: 1.2;
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
        }
    </style>
</head>

<body>
    <div class="content">
        <div class="header">
        <img src="{{ asset('images/coat_of_arms.png') }}" alt="Coat of Arms of Kiribati">
            <p><strong>GOVERNMENT OF KIRIBATI</strong></p>
            <p>MINISTRY OF FISHERIES AND MARINE RESOURCES DEVELOPMENT</p>
            <p>P.O.Box 64, Bairiki, Tarawa, Republic of Kiribati</p>
            <p>Tel: (686) 21099 | Fax: (686) 21120</p>
            <p>Email: <a href="mailto:info@mfmrd.gov.ki">info@mfmrd.gov.ki</a></p>
        </div>

        <h1>Default License Regulation 2024</h1>
        <h2>{{ $license->licenseType->name }}<br>[Regulation 11(1)]</h2>
        
        <div class="license-number"><strong>License No.</strong> {{ $license->license_number }}</div>

        <p>HAVING SATISFIED ALL the necessary legal requirements prescribed by the above-mentioned regulation, a License is hereby granted to:</p>
        
        <p style="text-align: center;"><strong>{{ $license->applicant->first_name ?? 'N/A' }}  {{ $license->applicant->last_name ?? 'N/A' }}</strong></p>
        
        <h3>Subject to the following Conditions:</h3>
        
        <ol class="conditions">
            <li>License is not transferable;</li>
            <li>License valid until its expiry date;</li>
            <li>License must be displayed at the head office of the business;</li>
            <li>License must be presented to an authorized officer upon request;</li>
            <li>Licensee must comply with all legal regulations governing the activity;</li>
            <li>Licensee must submit periodic reports as required;</li>
            <li>All operations must cease immediately upon the expiry or cancellation of the license;</li>
            <li>License fees must be paid by the due date each fiscal year.</li>
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
