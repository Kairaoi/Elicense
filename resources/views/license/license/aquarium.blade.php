<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice of Designated Fishery 2022</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            line-height: 1.2;
            position: relative;
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
            max-width: 140px;
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
    </style>
</head>

<body>
    <div class="content">
        <div class="header">
        <img src="{{ public_path('images/coat_ovf_arms.png') }}" alt="Coat of Arms of Kiribati">
        <p><strong>GOVERNMENT OF KIRIBATI</strong></p>
            <p>MINISTRY OF FISHERIES AND MARINE RESOURCES DEVELOPMENT</p>
            <p>P.O.Box 64, Bairiki, Tarawa, Republic of Kiribati</p>
            <p>Tel: (686) 75021099 | Fax: (686) 75021525</p>
            <p>Email: <a href="mailto:notification@mfor.gov.ki">notification@mfor.gov.ki</a></p>
        </div>

        <h1>Notice of Designated Fishery 2022</h1>
        <h2>Fisheries (Conservation & Management of Coastal Marine Resources) Regulation 15(2)</h2>
        
        <div class="license-number"><strong>License No.</strong> {{ $license->license_number }}</div>

        <p><strong>HAVING SATISFIED ALL</strong> the necessary legal requirements prescribed by the above-mentioned regulation, a License is hereby granted to:</p>
        
        <p style="text-align: center;"><strong>{{ $license->applicant->first_name ?? 'N/A' }}  {{ $license->applicant->last_name ?? 'N/A' }} - {{ $license->applicant->company_name ?? 'N/A' }}</strong></p>
        
        <h3>Subject to the following Conditions:</h3>
        
        <ol class="conditions">
            <li>License is not transferable.</li>
            <li>License valid until your allocated quota is reached.</li>
            <li>License must be exhibited at the Head Office of the Business.</li>
            <li>License must be presented to an authorized officer upon request.</li>
            <li>Licensee must cooperate and assist an authorized officer upon request, while the said officer is executing his/her functions under the Act.</li>
            <li>Licensee must not operate and harvest Pet fish in any areas protected under the Act.</li>
            <li>Licensee must strictly follow procedures specified under Processing, Import & Export Regulation 2021.</li>
            <li>Licensee must not collect, harvest, process, offer to sell or have in possession other marine species that not specified in the Notice of Designated Fishery 2021.</li>
            <li>Licensee must not involve with any forms of fraudulent activity.</li>
            <li>Licensee must comply with management measures prescribed in the Marine Aquarium Trade Management Plan 2024.</li>
            <li>Licensee must comply with the prices of marine aquarium trade fish species specified under the benchmark prizes authorized by Fisheries.</li>
            <li>Licensee must report all catch and provide copy of invoice for all consignment made to the Director of Fisheries.</li>
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