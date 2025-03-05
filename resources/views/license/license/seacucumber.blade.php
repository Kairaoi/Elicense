<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sea Cucumber Regulation 2024 License</title>
    <style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    line-height: 1.25;
    font-size: 15px;
}

.content {
    max-width: 750px;
    margin: 0 auto;
    padding: 10px;
    background-color: white;
}

.header {
    text-align: center;
    margin-bottom: 8px;
}

.header p {
    margin: 2px;
    font-size: 15px;
}

.header img {
    max-width: 110px;
    margin-bottom: 5px;
}

h1 {
    text-align: center;
    font-size: 18px;
    margin: 6px 0;
}

h2 {
    text-align: center;
    font-size: 16px;
    margin: 6px 0;
}

h3 {
    font-size: 15px;
    margin: 6px 0;
}

.license-number {
    font-weight: bold;
    margin: 4px 0;
    text-align: center;
    font-size: 15px;
}

.conditions {
    margin: 6px 0;
    padding-left: 20px;
}

.conditions li {
    margin-bottom: 1px;
}

.issue-date {
    font-size: 14px;
    margin-top: 6px;
}

.signature {
    text-align: right;
    font-size: 14px;
    line-height: 1.2;
    margin-top: 5px;
}

.signature p {
    margin: 2px 0;
}

p {
    margin: 4px 0;
}

@page {
    size: A4;
    margin: 12mm;
}

@media print {
    body {
        width: 210mm;
        height: 297mm;
    }
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

        <h1>Sea Cucumber Regulation 2024</h1>
        <h2>{{ $license->licenseType->name }}<br>[Regulation 11(1)]</h2>
        
        <div class="license-number"><strong>License No.</strong> {{ $license->license_number }}</div>

        <p>HAVING SATISFIED ALL the necessary legal requirements prescribed by the above-mentioned regulation, a License is hereby granted to:</p>
        
        <p style="text-align: center;"><strong>{{ $license->applicant->first_name ?? 'N/A' }}  {{ $license->applicant->last_name ?? 'N/A' }} - {{ $license->applicant->company_name ?? 'N/A' }}</strong></p>
        
        <h3>Subject to the following Conditions:</h3>
        
        <ol class="conditions">
            <li>License is not transferable;</li>
            <li>License valid until your allocated quota is reached and/or expiration of license;</li>
            <li>Must be exhibited at the Head Office of the Business;</li>
            <li>Must be presented to an authorised officer upon request;</li>
            <li>Licensee must cooperate and assist an authorised officer upon request, while the said officer is executing his/her functions under the regulation or the Act;</li>
            <li>Licensee to comply with the minimum price set for all landed sea cucumber species prescribed by the Director;</li>
            <li>Licensee must collect sea cucumber (live or dried) from listed stated agents;</li>
            <li>Licensee must not possess or have in possession undersize sea cucumber (see Schedule 3);</li>
            <li>Licensee must make sure sea cucumber species be separated in accordance to species per Island until ready to be sealed for export, and must not exceed 25kg per bag or packaging;</li>
            <li>Licensee must keep record of all supplied sea cucumber from Outer Islands and submit monthly to the Director of Fisheries;</li>
            <li>Quota is not transferable in terms of spatial and time;</li>
            <li>Licensee must not operate and harvest sea cucumber in the uninhabited island of the Line and Phoenix groups or any other designated closed area;</li>
            <li>Licensee not allowed to assist and provide SCUBA gears to local fishers;</li>
            <li>Licensee must not buy sea cucumber that are harvested under clause 12 and 13 above;</li>
        </ol>

        <p class="issue-date">
            Issued: 1st January 2025, Expires: 31st December 2025
        </p>

        <div class="signature">
            <p>…...........................</p>
            <p>Director of Fisheries</p>
        </div>
    </div>
</body>

</html>