<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sea Cucumber Regulation 2024 License</title>
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

        .header img {
    width: 140px; /* Increase size from 100px to 150px */
    height: auto;
    margin-bottom: 2px;
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
    </style>
</head>

<body>
    @if(isset($isRevoked) && $isRevoked)
        <div class="watermark-revoked">REVOKED</div>
        <div class="watermark-revoked-text">License No Longer Valid</div>
    @else
        <div class="watermark"></div>
    @endif
    <div class="content">
        <div class="header">
        <img src="{{ public_path('images/coat_ovf_arms.png') }}" alt="Coat of Arms of Kiribati">
            <p><strong>GOVERNMENT OF KIRIBATI</strong></p>
            <p>MINISTRY OF FISHERIES AND MARINE RESOURCES DEVELOPMENT</p>
            <p>P.O.Box 64, Bairiki, Tarawa, Republic of Kiribati</p>
            <p>Tel: (686) 21099 | Fax: (686) 21120</p>
            <p>Email: <a href="mailto:info@mfmrd.gov.ki">info@mfmrd.gov.ki</a></p>
        </div>

        <h1>Sea Cucumber Regulation 2024</h1>
        <h2>{{ $license->licenseType->name }}<br>[Regulation 11(1)]</h2>
        
        <div class="license-number"><strong>License No.</strong> {{ $license->license_number }}</div>

        <p>HAVING SATISFIED ALL the necessary legal requirements prescribed by the above-mentioned regulation, a License is hereby granted to:</p>
        
        <p style="text-align: center;"><strong>{{ $license->applicant->first_name ?? 'N/A' }}  {{ $license->applicant->last_name ?? 'N/A' }}</strong></p>
        
        <h3>Subject to the following Conditions:</h3>
        
        <ol class="conditions">
            <li>License is not transferable;</li>
            <li>License valid until your allocated quota is reached;</li>
            <li>Must be exhibited at the Head Office of the Business;</li>
            <li>Must be presented to an authorised officer upon request;</li>
            <li>Licensee must cooperate and assist an authorised officer upon request, while the said officer is executing his/her functions under the regulation or the Act;</li>
            <li>Licensee must collect sea cucumber (live or dried) from listed Fishers stated in the application form (schedule I);</li>
            <li>Licensee must ensure Sea Cucumber species be separated in accordance to species per Island until ready to be sealed for export, and must not exceed 25kg per bag or packaging;</li>
            <li>Licensee must keep a record of all supplied Sea cucumber from listed fishers in application form (schedule I);</li>
            <li>Quota is not transferable in terms of spatial and time;</li>
            <li>Licensee must not operate and harvest Sea cucumber in the uninhabited islands of the Line and Phoenix groups;</li>
            <li>Licensee not allowed to assist and provide SCUBA gears to local fishers;</li>
            <li>Licensee must not buy sea cucumber that are harvested under clause 8 and 10 above;</li>
            <li>Licensee must cease all operations once annual quota is exhausted;</li>
            <li>Licensee shall pay their license fee no later than November at every physical year of operation.</li>
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