<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobster Regulation 2024 License</title>
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

        .header img {
            width: 100px; /* Adjust size as needed */
            height: auto; /* Maintain aspect ratio */
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

        a {
            color: #007BFF; /* Link color */
            text-decoration: none; /* Remove underline */
        }

        a:hover {
            text-decoration: underline; /* Underline on hover */
            color: #0056b3; /* Darker blue on hover */
        }

        @media print {
            .watermark {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                opacity: 0.3; /* Make the watermark more transparent */
            }
            body {
                font-size: 12px; /* Adjust font size for print */
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
    <div class="watermark">DRAFTED</div>
    <main class="content">
        
        <header>
            <div class="header">
            <img src="/home/pqcavaqe/coastal-elicense.fisheries.gov.ki/storage/app/public/images/coat_of_arms.png" alt="Coat of Arms of Kiribati">

                <p><strong>GOVERNMENT OF KIRIBATI</strong></p>
                <p>MINISTRY OF FISHERIES AND MARINE RESOURCES DEVELOPMENT</p>
                <p>P.O.Box 64, Bairiki, Tarawa, Republic of Kiribati</p>
                <p>Tel: (686) 21099 | Fax: (686) 21120</p>
                <p>Email: <a href="mailto:info@mfmrd.gov.ki">Contact Us via Email</a></p>
            </div>
        </header>

        <h1>Lobster Regulation 2024</h1>
        <h2>{{ $license->getDisplayLicenseTypeName() }}<br>[Regulation 11(1)]</h2>
        
        <div class="license-number"><strong>License No.</strong> {{ $license->license_number }}</div>

        <p>HAVING SATISFIED ALL the necessary legal requirements prescribed by the above-mentioned regulation, a License is hereby granted to:</p>
        
        <p style="text-align: center;"><strong>{{ $license->applicant->first_name ?? 'N/A' }}  {{ $license->applicant->last_name ?? 'N/A' }}</strong></p>
        
        <h3>Subject to the following Conditions:</h3>
        
        <ol class="conditions">
            <li>License is not transferable;</li>
            <li>License valid until your allocated quota is reached;</li>
            <li>Must be exhibited at the Head Office of the Business;</li>
            <li>Must be presented to an authorised officer upon request;</li>
            <li>Licensee must cooperate and assist an authorised officer upon request while the said officer is executing his/her functions under the regulation or the Act;</li>
            <li>Licensee must collect lobster only from listed Fishers stated in the application form (schedule I);</li>
            <li>Licensee must ensure lobsters are handled according to the regulation, ensuring they meet minimum size requirements for export;</li>
            <li>Licensee must keep a record of all supplied lobsters from listed fishers in application form (schedule I);</li>
            <li>Quota is not transferable in terms of spatial and time;</li>
            <li>Licensee must not operate or harvest lobsters in the restricted areas stated in the regulation;</li>
            <li>Licensee not allowed to assist and provide SCUBA gears to local fishers;</li>
            <li>Licensee must not buy lobsters that are harvested under prohibited methods or from restricted areas;</li>
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
    </main>
</body>

</html>
