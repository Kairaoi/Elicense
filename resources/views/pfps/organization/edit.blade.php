@extends('layouts.app')

@push('styles')
<style>
    /* Custom styles reused for organization forms */
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
                <h1>Edit Organization</h1>
                <p>Update details for the organization.</p>
            </div>

            <!-- Form Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('pfps.organizations.update', $organization->organization_id) }}">
                        @csrf
                        @method('PUT')  <!-- Method Spoofing to send PUT request -->
                        
                        <!-- Organization Name -->
                        <div class="form-group">
                            <label for="organization_name">Organization Name *</label>
                            <input type="text" 
                                   class="form-control @error('organization_name') is-invalid @enderror" 
                                   id="organization_name" 
                                   name="organization_name" 
                                   value="{{ old('organization_name', $organization->organization_name) }}" 
                                   required>
                            @error('organization_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Update Organization</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
