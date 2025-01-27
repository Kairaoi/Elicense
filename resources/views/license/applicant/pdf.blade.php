<!DOCTYPE html>
<html>
<head>
    <title>Applicant Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            background: linear-gradient(to right, #4b6cb7, #182848);
            color: white;
            padding: 20px;
            border-radius: 8px;
        }

        .header h1 {
            font-size: 24px;
            text-transform: uppercase;
            margin: 0;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            color: #4b6cb7;
            font-size: 20px;
            border-bottom: 2px solid #4b6cb7;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .info-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-item {
            margin-bottom: 10px;
            padding: 8px;
            background: white;
            border-radius: 4px;
        }

        .info-label {
            font-weight: bold;
            color: #4b6cb7;
            display: inline-block;
            width: 150px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background: white;
        }

        th {
            background: #4b6cb7;
            color: white;
            padding: 10px;
            text-align: left;
        }

        td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .license-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .license-header {
            background: #4b6cb7;
            color: white;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending {
            background: #ffc107;
            color: black;
        }

        .status-reviewed {
            background: #17a2b8;
            color: white;
        }

        .status-issued {
            background: #28a745;
            color: white;
        }

        .status-revoked {
            background: #dc3545;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>License Application Details</h1>
    </div>

    <div class="section">
        <h2 class="section-title">Applicant Information</h2>
        <div class="info-card">
            @foreach([
                ['Full Name', $applicant->first_name . ' ' . $applicant->last_name],
                ['Company', $applicant->company_name],
                ['Registration Number', $applicant->local_registration_number],
                ['Company Type', $applicant->types_of_company],
                ['Establishment Date', $applicant->date_of_establishment],
                ['Citizenship', $applicant->citizenship],
                ['Contact Phone', $applicant->phone_number],
                ['Email Address', $applicant->email],
                ['Work Address', $applicant->work_address],
                ['Registered Address', $applicant->registered_address]
            ] as $item)
            <div class="info-item">
                <span class="info-label">{{ $item[0]}}:</span>
                <span>{{ $item[1] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    @foreach($licenses as $license)
    <div class="section">
        <div class="license-box">
            <div class="license-header">
                <h3 style="margin: 0;">License #{{ $license->id }}</h3>
                <span class="status-badge status-{{ strtolower($license->status) }}">
                    {{ str_replace('_', ' ', ucfirst($license->status)) }}
                </span>
            </div>

            <div class="info-item">
                <span class="info-label">License Type:</span>
                <span>{{ $licenseTypes->firstWhere('id', $license->license_type_id)->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Issue Date:</span>
                <span>{{ $license->issue_date ?? 'Pending' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Expiry Date:</span>
                <span>{{ $license->expiry_date ?? 'Pending' }}</span>
            </div>

            @if(isset($requestedQuotas[$license->id]) && $requestedQuotas[$license->id]->isNotEmpty())
            <h3 style="color: #4b6cb7; margin-top: 20px;">Species Quotas</h3>
            <table>
                <thead>
                    <tr>
                        <th>Species</th>
                        <th>Requested Quota</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requestedQuotas[$license->id] as $requestedQuota)
                    <tr>
                        <td>{{ $species->firstWhere('id', $requestedQuota->species_id)->name ?? 'N/A' }}</td>
                        <td>{{ $requestedQuota->requested_quota }}</td>
                        <td>${{ number_format($requestedQuota->unit_price, 2) }}</td>
                        <td>${{ number_format($requestedQuota->requested_quota * $requestedQuota->unit_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
    @endforeach
</body>
</html>