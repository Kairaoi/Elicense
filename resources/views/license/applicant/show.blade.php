@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="text-center fancy-title mb-5">License Application Form</h1>

    <!-- Applicant Information Card -->
    <div class="info-card mb-5 shadow-lg">
        <div class="card-header fancy-header text-white">
            <h2 class="card-title mb-0">
                <i class="fas fa-user-circle me-2"></i>
                Applicant Information
            </h2>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach([
                    ['Full Name', $applicant->first_name . ' ' . $applicant->last_name, 'fa-user'],
                    ['Company', $applicant->company_name, 'fa-building'],
                    ['Registration Number', $applicant->local_registration_number, 'fa-id-card'],
                    ['Company Type', $applicant->types_of_company, 'fa-industry'],
                    ['Establishment Date', $applicant->date_of_establishment, 'fa-calendar-alt'],
                    ['Citizenship', $applicant->citizenship, 'fa-flag'],
                    ['Contact Phone', $applicant->phone_number, 'fa-phone'],
                    ['Email Address', $applicant->email, 'fa-envelope'],
                    ['Foreign Investment License', $applicant->foreign_investment_license, 'fa-certificate'],
                    ['Work Address', $applicant->work_address, 'fa-map-marker-alt', 'col-md-12'],
                    ['Registered Address', $applicant->registered_address, 'fa-home', 'col-md-12']
                ] as $item)
                <div class="col-md-6 {{ $item[3] ?? '' }} mb-4">
                    <div class="info-item d-flex align-items-center p-3 bg-light rounded shadow-sm">
                        <i class="fas {{ $item[2] }} fa-2x text-primary me-3"></i>
                        <div>
                            <label class="d-block fw-bold text-secondary">{{ $item[0] }}</label>
                            <span class="text-dark">{{ $item[1] }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- License Information Section -->
    <div class="section-card mb-5">
        <h2 class="section-title fancy-subtitle">License Information</h2>
        @foreach($licenses as $license)
        <div class="license-box mb-4">
            <div class="license-header">
                <span class="license-number">License #{{ $license->id }}</span>
                <span class="license-status {{ strtolower($license->status) }}">
                    @if($license->status === 'license_revoked')
                        Revoked
                    @else
                        {{ str_replace('_', ' ', ucfirst($license->status)) }}
                    @endif
                </span>
            </div>
            <div class="license-content">
                @foreach([
                    ['License Type', $licenseTypes->firstWhere('id', $license->license_type_id)->name],
                    ['Issue Date', $license->issue_date ?? 'Pending'],
                    ['Expiry Date', $license->expiry_date ?? 'Pending']
                ] as $info)
                <div class="info-box">
                    <span class="info-label">{{ $info[0] }}:</span>
                    <span class="info-value">{{ $info[1] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Species Quotas Section -->
    @foreach($licenses as $license)
    <div class="section-card mb-5">
        <h2 class="section-title fancy-subtitle">Species Quotas for License #{{ $license->id }}</h2>
        
        @if(isset($requestedQuotas[$license->id]) && $requestedQuotas[$license->id]->isNotEmpty())
        <div class="table-responsive">
            <table class="table custom-table">
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
        </div>
        @else
        <p class="text-center mb-0">No species quotas requested for this license.</p>
        @endif

        <!-- License Actions -->
        <div class="license-actions mt-4">
            <a href="{{ route('license.licenses.edit', $license->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('license.licenses.invoice', $license->id) }}" class="btn btn-info">
                <i class="fas fa-file-invoice"></i> Invoice
            </a>
            @if($license->status != 'license_issued')
            <form action="{{ route('license.licenses.review', [$license->applicant_id, $license->id]) }}" method="PUT" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-check-circle"></i> Mark as Reviewed
                </button>
            </form>
            @endif
            @if($license->status == 'license_issued')
            <form action="{{ route('license.licenses.revoke', $license->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT') <!-- Use PUT or DELETE as per your route -->
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-ban"></i> Revoke License
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforeach

    <!-- Back Button -->
    <div class="text-center mt-5">
        <a href="{{ route('applicant.applicants.index') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-arrow-left"></i> Back to Applicants List
        </a>
    </div>
</div>

<style>
    /* General Styles */
    .fancy-title {
        font-size: 3rem;
        color: #4b6cb7;
        text-transform: uppercase;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    .fancy-header {
        background: linear-gradient(to right, #4b6cb7, #182848);
        padding: 1rem;
        color: white;
        border-radius: 8px 8px 0 0;
    }

    .fancy-subtitle {
        font-size: 1.75rem;
        color: #4b6cb7;
        border-bottom: 2px solid #4b6cb7;
        margin-bottom: 1rem;
    }

    .license-status {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: bold;
    }

    .license-status.pending {
        background-color: #ffc107;
        color: #000;
    }

    .license-status.reviewed {
        background-color: #17a2b8;
        color: #fff;
    }

    .license-status.license_issued {
        background-color: #28a745;
        color: #fff;
    }

    .license-status.license_revoked {
        background-color: #dc3545;
        color: #fff;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
    }
</style>
@endsection
