<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harvester License</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 30px 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 2.5em;
            margin: 0;
        }
        .header p {
            font-size: 1.2em;
            margin-top: 10px;
        }
        .section {
            padding: 20px;
            margin: 20px;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .section-title {
            background-color: #3498db;
            color: white;
            padding: 12px;
            font-size: 1.2em;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table th, .info-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .info-table th {
            background-color: #ecf0f1;
            width: 35%;
        }
        .info-table td {
            background-color: #fafafa;
        }
        .species-list {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
        }
        .species-list strong {
            display: block;
            margin-bottom: 10px;
        }
        .footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Harvester License</h1>
        <p>License Number: {{ $harvesterLicense->license_number }}</p>
    </div>

    <div class="section">
        <div class="section-title">PART I: APPLICANT DETAILS</div>
        <table class="info-table">
            <tr>
                <th>Applicant Name</th>
                <td>{{ $harvesterLicense->applicant->first_name }} {{ $harvesterLicense->applicant->last_name }}</td>
            </tr>
            <tr>
                <th>Applicant Type</th>
                <td>{{ $harvesterLicense->applicant->is_group ? 'Group' : 'Individual' }}</td>
            </tr>
            <tr>
                <th>National ID</th>
                <td>{{ $harvesterLicense->applicant->national_id }}</td>
            </tr>
            <tr>
                <th>Payment Receipt</th>
                <td>{{ $harvesterLicense->payment_receipt_no }}</td>
            </tr>
            <tr>
                <th>Fee</th>
                <td>${{ number_format($harvesterLicense->fee, 2) }}</td>
            </tr>
        </table>

        @if($harvesterLicense->applicant->is_group && $harvesterLicense->groupMembers->count() > 0)
        <div class="section-title">Group Members</div>
        <table class="info-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>National ID</th>
                </tr>
            </thead>
            <tbody>
                @foreach($harvesterLicense->groupMembers as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->national_id }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <div class="section">
        <div class="section-title">PART II: OPERATION DETAILS</div>
        <table class="info-table">
            <tr>
                <th>Area of Operation</th>
                <td>{{ $harvesterLicense->island->name }}</td>
            </tr>
            <tr>
                <th>Issue Date</th>
                <td>{{ date('d/m/Y', strtotime($harvesterLicense->issue_date)) }}</td>
            </tr>
            <tr>
                <th>Expiry Date</th>
                <td>{{ date('d/m/Y', strtotime($harvesterLicense->expiry_date)) }}</td>
            </tr>
        </table>

        <div class="species-list">
            <strong>Targeted Species:</strong>
            @if($harvesterLicense->species->isNotEmpty())
                @foreach($harvesterLicense->species as $species)
                    • {{ $species->name }}<br>
                @endforeach
            @else
                No targeted species specified
            @endif
        </div>
    </div>

    <div class="footer">
        <p>This license is valid until {{ date('d/m/Y', strtotime($harvesterLicense->expiry_date)) }}</p>
        <p>Issued by: Ministry of Fisheries and Ocean Resource</p>
    </div>
</body>
</html>
