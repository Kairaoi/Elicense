@extends('layouts.app')

@push('styles')
<style>
    /* Custom styles reused for permit forms */
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
                <h1>Edit Permit</h1>
                <p>Update details for the selected permit.</p>
            </div>

            <!-- Form Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('pfps.permits.update', $permit->permit_id) }}">
                        @csrf
                        @method('PUT')  <!-- Method Spoofing to send PUT request -->
                        
                        <!-- Permit Number -->
                        <div class="form-group">
                            <label for="permit_number">Permit Number *</label>
                            <input type="text" 
                                   class="form-control @error('permit_number') is-invalid @enderror" 
                                   id="permit_number" 
                                   name="permit_number" 
                                   value="{{ old('permit_number', $permit->permit_number) }}" 
                                   required>
                            @error('permit_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Other fields (same as Create Permit) -->

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Update Permit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
