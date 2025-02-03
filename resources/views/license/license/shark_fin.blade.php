<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revoked Shark Fin License</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            line-height: 1.2;
            position: relative;
            background: repeating-linear-gradient(
                45deg,
                rgba(255,0,0,0.1),
                rgba(255,0,0,0.1) 10px,
                rgba(255,0,0,0.2) 10px,
                rgba(255,0,0,0.2) 20px
            );
        }

        .watermark {
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

        .content {
            max-width: 700px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .license-details {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="watermark">{{ $revokeText }}</div>
    
    <div class="content">
        <div class="header">
            <h1>Shark Fin License - REVOKED</h1>
        </div>

        <div class="license-details">
            <h2>License Information</h2>
            <p><strong>License Number:</strong> {{ $license->license_number }}</p>
            <p><strong>Licensee:</strong> {{ $license->applicant->first_name ?? 'N/A' }} {{ $license->applicant->last_name ?? 'N/A' }}</p>
            
            <h3>Revocation Details</h3>
            <p><strong>Revocation Reason:</strong> {{ $license->revocation_reason }}</p>
            <p><strong>Revocation Date:</strong> {{ \Carbon\Carbon::parse($license->revocation_date)->format('jS F Y') }}</p>
            
            <h3>Original License Details</h3>
            <p><strong>Original Issue Date:</strong> {{ \Carbon\Carbon::parse($license->issue_date)->format('jS F Y') }}</p>
            <p><strong>Original Expiry Date:</strong> {{ \Carbon\Carbon::parse($license->expiry_date)->format('jS F Y') }}</p>
        </div>

        <div class="footer">
            <p>This license is no longer valid and all rights and privileges have been revoked.</p>
            <p>For any inquiries, please contact the Ministry of Fisheries and Marine Resources Development.</p>
        </div>
    </div>
</body>
</html>