@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5">License Application Form</h1>

    <!-- License Information Section -->
    <div class="section-card mb-5">
        <h2 class="section-title">License Information</h2>
        @if($licenses->isNotEmpty())
            @foreach($licenses as $license)
            <div class="info-box mb-3">
                <span class="info-label">License Type:</span>
                <span class="info-value">{{ $licenseTypes->firstWhere('id', $license->license_type_id)->name }}</span>
            </div>
            @endforeach
        @else
            <div class="info-box">
                <span class="info-label">License Type:</span>
                <span class="info-value">N/A</span>
            </div>
        @endif
    </div>

    <!-- Applicant Information Section -->
    <div class="section-card mb-5">
        <h2 class="section-title">Applicant Information</h2>
        <div class="info-grid">
            <div class="info-box">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $applicant->first_name }} {{ $applicant->last_name }}</span>
            </div>
            <div class="info-box">
                <span class="info-label">Company:</span>
                <span class="info-value">{{ $applicant->company_name }}</span>
            </div>
            <div class="info-box">
                <span class="info-label">Local Registration Number:</span>
                <span class="info-value">{{ $applicant->local_registration_number }}</span>
            </div>
            <!-- ... other applicant information ... -->
        </div>
    </div>

    <!-- Species Quotas Section -->
    @foreach($licenses as $license)
    <div class="section-card mb-5">
        <h2 class="section-title">Species Quotas for License #{{ $license->id }}</h2>
        
        @if(isset($requestedQuotas[$license->id]) && $requestedQuotas[$license->id]->isNotEmpty())
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Species</th>
                            <th>Requested Quota</th>
                            <th>Unit Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requestedQuotas[$license->id] as $requestedQuota)
                            <tr>
                                <td>{{ $species->firstWhere('id', $requestedQuota->species_id)->name ?? 'N/A' }}</td>
                                <td>{{ $requestedQuota->requested_quota }}</td>
                                <td>{{ $requestedQuota->unit_price }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center mb-0">No species quotas requested for this license.</p>
        @endif

        <!-- Review Button for each license -->
        <div class="mt-4">
            <form action="{{ route('applicant.applicants.review', $license->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning">Mark License #{{ $license->id }} as Reviewed</button>
            </form>
        </div>
    </div>
    @endforeach

    <!-- Back Button -->
    <div class="text-center mt-5">
        <a href="{{ route('applicant.applicants.index') }}" class="btn btn-primary btn-lg">Back to Applicants List</a>
    </div>
</div>
<style>
    /* Your existing styles... */

    .license-card {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .license-header {
        background-color: #f8f9fa;
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .license-header h3 {
        margin: 0;
        color: #007bff;
        font-size: 1.25rem;
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

    .license-details {
        padding: 1.5rem;
    }

    .license-actions {
        border-top: 1px solid #dee2e6;
        padding-top: 1rem;
    }

    .license-actions .btn {
        margin-right: 0.5rem;
    }

    /* Add any additional styles you need... */
</style>
@endsection