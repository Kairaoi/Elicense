@extends('layouts.app')

@push('styles')
<style>
    /* Custom styles reused for lodge forms */
    .form-group label {
        font-weight: bold;
    }
    .form-text {
        font-size: 0.9em;
    }
    .custom-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="page-header mb-4">
                <h1>Visitor Details</h1>
                <p>View the details of the selected visitor.</p>
            </div>

            <!-- Card displaying visitor details -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <p>{{ $visitor->first_name }}</p>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <p>{{ $visitor->last_name }}</p>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <p>{{ ucfirst($visitor->gender) }}</p>
                    </div>

                    <div class="form-group">
                        <label for="home_address">Home Address</label>
                        <p>{{ $visitor->home_address ?? 'N/A' }}</p>
                    </div>

                    <div class="form-group">
                        <label for="passport_number">Passport Number</label>
                        <p>{{ $visitor->passport_number }}</p>
                    </div>

                    <div class="form-group">
                        <label for="country_id">Country</label>
                        <p>{{ $visitor->country->name }}</p>  <!-- Assuming 'country' is a relation -->
                    </div>

                    <div class="form-group">
                        <label for="organization_id">Organization</label>
                        <p>{{ $visitor->organization->name ?? 'N/A' }}</p>  <!-- Assuming 'organization' is a relation -->
                    </div>

                    <div class="form-group">
                        <label for="arrival_date">Arrival Date</label>
                        <p>{{ $visitor->arrival_date->format('Y-m-d') }}</p>
                    </div>

                    <div class="form-group">
                        <label for="departure_date">Departure Date</label>
                        <p>{{ $visitor->departure_date->format('Y-m-d') }}</p>
                    </div>

                    <div class="form-group">
                        <label for="lodge_id">Lodge</label>
                        <p>{{ $visitor->lodge->lodge_name }}</p>  <!-- Assuming 'lodge' is a relation -->
                    </div>

                    <div class="form-group">
                        <label for="emergency_contact">Emergency Contact</label>
                        <p>{{ $visitor->emergency_contact ?? 'N/A' }}</p>
                    </div>

                    <div class="form-group">
                        <label for="certification_number">Certification Number</label>
                        <p>{{ $visitor->certification_number ?? 'N/A' }}</p>
                    </div>

                    <div class="form-group">
                        <label for="certification_type">Certification Type</label>
                        <p>{{ $visitor->certification_type ?? 'N/A' }}</p>
                    </div>

                    <div class="form-group">
                        <label for="certification_expiry">Certification Expiry</label>
                        <p>{{ $visitor->certification_expiry ? $visitor->certification_expiry->format('Y-m-d') : 'N/A' }}</p>
                    </div>

                    <!-- Buttons to Edit or Go Back -->
                    <a href="{{ route('pfps.visitors.edit', $visitor->visitor_id) }}" class="btn btn-warning">Edit Visitor</a>
                    <a href="{{ route('pfps.visitors.index') }}" class="btn btn-secondary">Back to Visitors</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
