<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquarium Regulation 2024 License</title>
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

        .content {
            position: relative;
            z-index: 1;
            max-width: 700px;
            margin: 0 auto;
            background-color: white;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header p {
            margin: 3px;
            font-size: 14px;
        }

        .header img {
            max-width: 100px; /* Adjust the size as needed */
            margin-bottom: 10px;
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

        @media print {
            .watermark {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-30deg);
                font-size: 100px;
                color: rgba(200, 200, 200, 0.5);
                pointer-events: none;
                z-index: 1000;
                white-space: nowrap;
                user-select: none;
                font-weight: bold;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
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
    <div class="content">
        <div class="header">
            <img src="{{ asset('images/coat_of_arms.png') }}" alt="Coat of Arms of Kiribati">
            <p><strong>GOVERNMENT OF KIRIBATI</strong></p>
            <p>MINISTRY OF FISHERIES AND MARINE RESOURCES DEVELOPMENT</p>
            <p>P.O.Box 64, Bairiki, Tarawa, Republic of Kiribati</p>
            <p>Tel: (686) 21099 | Fax: (686) 21120</p>
            <p>Email: <a href="mailto:info@mfmrd.gov.ki">info@mfmrd.gov.ki</a></p>
        </div>

        <h1>Aquarium Regulation 2024</h1>
        <h2>{{ $license->getDisplayLicenseTypeName() }}<br>[Regulation 11(1)]</h2>
        
        <div class="license-number"><strong>License No.</strong> {{ $license->license_number }}</div>

        <p>HAVING SATISFIED ALL the necessary legal requirements prescribed by the above-mentioned regulation, a License is hereby granted to:</p>
        
        <p style="text-align: center;"><strong>{{ $license->applicant->first_name ?? 'N/A' }}  {{ $license->applicant->last_name ?? 'N/A' }}</strong></p>
        
        <h3>Subject to the following Conditions:</h3>
        
        <ol class="conditions">
            <li>License is not transferable;</li>
            <li>License valid until your allocated quota or time period is reached;</li>
            <li>License must be exhibited at the Head Office of the Business;</li>
            <li>Must be presented to an authorised officer upon request;</li>
            <li>Licensee must cooperate and assist an authorised officer while executing his/her duties under the regulation or the Act;</li>
            <li>Licensee must ensure that only permitted species are harvested for aquarium purposes as per the approved species list;</li>
            <li>Licensee must maintain records of all collected species and provide reports as required by the regulation;</li>
            <li>Licensee must ensure proper handling and transportation of marine species to minimize harm;</li>
            <li>Licensee must cease all operations once the allowable quota or species limit is reached;</li>
            <li>Licensee must not operate or harvest species in designated protected areas;</li>
            <li>Licensee must follow all guidelines for aquarium species collection, including size limits and sustainable practices;</li>
            <li>Licensee is prohibited from exporting any species not listed in the application form or without proper authorization;</li>
            <li>Licensee shall pay their license fee no later than November of every fiscal year of operation.</li>
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
