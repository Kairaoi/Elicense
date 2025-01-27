@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5">License Application Form</h1>

    <!-- License Information Section -->
    <div class="section-card mb-5">
        <h2 class="section-title">License Information</h2>
        <div class="info-box">
            <span class="info-label">License Type:</span>
            <span class="info-value">{{ $license ? $licenseTypes->firstWhere('id', $license->license_type_id)->name : 'N/A' }}</span>
        </div>
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
            <div class="info-box">
                <span class="info-label">Type of Company:</span>
                <span class="info-value">{{ $applicant->types_of_company }}</span>
            </div>
            <div class="info-box">
                <span class="info-label">Date of Establishment:</span>
                <span class="info-value">{{ $applicant->date_of_establishment }}</span>
            </div>
            <div class="info-box">
                <span class="info-label">Citizenship:</span>
                <span class="info-value">{{ $applicant->citizenship }}</span>
            </div>
            <div class="info-box">
                <span class="info-label">Work Address:</span>
                <span class="info-value">{{ $applicant->work_address }}</span>
            </div>
            <div class="info-box">
                <span class="info-label">Registered Address:</span>
                <span class="info-value">{{ $applicant->registered_address }}</span>
            </div>
            <div class="info-box">
                <span class="info-label">Foreign Investment License:</span>
                <span class="info-value">{{ $applicant->foreign_investment_license }}</span>
            </div>
            <div class="info-box">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $applicant->phone_number }}</span>
            </div>
            <div class="info-box">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $applicant->email }}</span>
            </div>
        </div>
    </div>

    <!-- Species Quotas Section -->
    <div class="section-card mb-5">
        <h2 class="section-title">Species Quotas Requested</h2>
        
        @if(!empty($requestedQuotas))
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
                        @foreach($requestedQuotas as $requestedQuota)
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
            <p class="text-center mb-0">No species quotas requested.</p>
        @endif
    </div>

    <!-- Back Button -->
    <div class="text-center mt-5">
        <a href="{{ route('applicant.applicants.index') }}" class="btn btn-primary btn-lg">Back to Applicants List</a>
    </div>

    <!-- Other HTML content -->

<!-- Review Button -->
@if($license)
    <form action="{{ route('applicant.applicants.review', $license->id) }}" method="POST" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-warning">Mark as Reviewed</button>
    </form>
@else
    <p class="text-danger">License information is not available for this applicant.</p>
@endif

<!-- Other HTML content -->


</div>

@endsection

<style>
    body {
        background-color: #f8f9fa; /* Light background for better contrast */
        color: #333; /* Dark text for readability */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Modern font */
    }

    .container {
        max-width: 900px; /* Limit the width of the container */
        margin: auto; /* Center the container */
    }

    h1 {
        color: #007bff; /* Primary color for the title */
        font-weight: 700; /* Bold title */
        margin-bottom: 2rem;
    }

    .section-card {
        background-color: #ffffff; /* White background for cards */
        border-radius: 8px; /* Rounded corners */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        padding: 2rem; /* Padding inside cards */
        transition: all 0.3s ease; /* Smooth transition on hover */
    }

    .section-card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); /* Darker shadow on hover */
    }

    .section-title {
        color: #343a40; /* Darker title color */
        font-size: 1.5rem; /* Larger font size for section titles */
        margin-bottom: 1rem;
        border-bottom: 2px solid #007bff; /* Underline effect */
        padding-bottom: .5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Responsive grid layout */
        gap: 1rem; /* Space between grid items */
    }

    .info-box {
        background-color: #f1f1f1; /* Light gray background for info boxes */
        border-radius: 6px; /* Rounded corners for info boxes */
        padding: 1rem; /* Padding inside info boxes */
        transition: background-color 0.3s ease; /* Smooth transition on hover */
    }

    .info-box:hover {
        background-color: #e2e6ea; /* Darker gray on hover */
    }

    .info-label {
        display: block;
        font-size: 0.9rem; /* Smaller font size for labels */
        color: #6c757d; /* Muted color for labels */
        margin-bottom: 0.3rem;
    }

    .info-value {
        font-weight: bold; /* Bold text for values */
        color: #212529; /* Darker text color for values */
    }

    .custom-table {
        width: 100%; /* Full width table */
        border-collapse: collapse; /* Collapse borders between cells */
    }

    .custom-table th,
    .custom-table td {
        padding: 1rem; /* Padding inside table cells */
        text-align: left; /* Left align text in table cells */
    }

    .custom-table th {
        background-color: #007bff; /* Primary color for table headers */
        color: white; /* White text in headers */
    }

    .custom-table tr:nth-child(even) {
        background-color: #f8f9fa; /* Light gray for even rows */
    }

    .custom-table tr:hover td {
        background-color: #e9ecef; /* Highlight row on hover */
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        padding: 0.75rem 2rem;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
        transform: translateY(-2px); 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); 
    }

    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr; 
        }
    }
</style>
