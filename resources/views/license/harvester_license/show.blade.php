@extends('layouts.app')

@section('content')
<div class="container main-content">
    <!-- Header Section with increased top margin -->
    <div class="card mb-5 shadow-sm mt-5">
        <div class="card-body py-4">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="text-primary mb-0">
                    <i class="fas fa-file-alt me-2"></i>Harvester Details
                </h3>
                <div class="btn-group">
                    <a href="{{ route('harvester.licenses.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                    <a href="{{ route('harvester.licenses.edit', $harvesterLicense->id) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Part I: Applicant Details -->
        <div class="col-md-12 mb-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>PART I: APPLICANT DETAILS</h5>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-spacious">
                            <tbody>
                                <tr>
                                    <th width="35%">Applicant Name</th>
                                    <td>{{ $harvesterLicense->applicant->first_name }} {{ $harvesterLicense->applicant->last_name }}</td>
                                </tr>
                                <tr>
                                    <th>Applicant Type</th>
                                    <td><span class="badge {{ $harvesterLicense->applicant->is_group ? 'bg-info' : 'bg-success' }} py-2 px-3">
                                        {{ $harvesterLicense->applicant->is_group ? 'Group' : 'Individual' }}</span></td>
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
                            </tbody>
                        </table>
                    </div>

                    <!-- Group Members Card (if applicable) -->
                    @if($harvesterLicense->applicant->is_group && $harvesterLicense->groupMembers->count() > 0)
                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-info text-white py-3">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Group Members</h5>
                        </div>
                        <div class="card-body py-4">
                            <div class="table-responsive">
                                <table class="table table-hover table-spacious">
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
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Part II: Operation Details -->
        <div class="col-md-12 mb-5">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>PART II: OPERATION DETAILS</h5>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-spacious">
                            <tbody>
                                <tr>
                                    <th width="35%">Area of Operation</th>
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
                                <tr>
                                    <th>Status</th>
                                    <td><span class="badge bg-success py-2 px-3">Active</span></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Species Card -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header bg-warning py-3">
                                <h5 class="mb-0"><i class="fas fa-fish me-2"></i>Targeted Species</h5>
                            </div>
                            <div class="card-body py-4">
                                @if($harvesterLicense->species->isNotEmpty())
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($harvesterLicense->species as $species)
                                    <span class="badge bg-info py-2 px-3">{{ $species->name }}</span>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-muted mb-0">No targeted species specified</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Part III: License Conditions -->
      

        <!-- Action Buttons -->
        <div class="col-md-12 mb-5">
            <div class="card shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('harvester.licenses.pdf', $harvesterLicense->id) }}" class="btn btn-success btn-lg">
    <i class="fas fa-download me-2"></i> Download PDF
</a>

                        <form action="{{ route('harvester.licenses.destroy', $harvesterLicense->id) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this license?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-trash me-2"></i> Delete License
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Increased spacing and improved layout */
    .main-content {
        padding-top: 2rem;
        padding-bottom: 3rem;
    }
    
    .card {
        border-radius: 12px;
        border: none;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
        padding: 1rem 1.5rem;
    }
    
    .table-spacious td,
    .table-spacious th {
        padding: 1rem 1.5rem;
    }
    
    .table th {
        background-color: rgba(0,0,0,0.03);
        font-weight: 600;
    }
    
    .badge {
        font-weight: 500;
        font-size: 0.9rem;
    }
    
    .condition-list .list-group-item {
        margin-bottom: 0.5rem;
        border-radius: 8px;
        border: 1px solid rgba(0,0,0,0.125);
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1.1rem;
    }
    
    .shadow-sm {
        box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.05)!important;
    }
    
    /* Increased spacing between sections */
    .mb-5 {
        margin-bottom: 3rem !important;
    }
    
    /* Added padding for card bodies */
    .card-body {
        padding: 1.5rem;
    }
    
    /* Improved gap between badges */
    .gap-3 {
        gap: 1rem !important;
    }
    
    /* Main content padding from top */
    main {
        padding-top: 80px;
    }
</style>
@endpush