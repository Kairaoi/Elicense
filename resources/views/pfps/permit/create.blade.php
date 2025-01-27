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
                <h1>Create New Permit</h1>
                <p>Add details for a new permit.</p>
            </div>

            <!-- Form Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('pfps.permits.generate') }}">
                        @csrf
                        
                        <!-- Permit Number -->
                        <div class="form-group">
                            <label for="permit_number">Permit Number *</label>
                            <input type="text" 
                                   class="form-control @error('permit_number') is-invalid @enderror" 
                                   id="permit_number" 
                                   name="permit_number" 
                                   value="{{ old('permit_number') }}" 
                                   required>
                            @error('permit_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Application ID -->
                        <div class="form-group">
                            <label for="application_id">Application ID *</label>
                            <input type="number" 
                                   class="form-control @error('application_id') is-invalid @enderror" 
                                   id="application_id" 
                                   name="application_id" 
                                   value="{{ old('application_id') }}" 
                                   required>
                            @error('application_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Invoice ID -->
                        <div class="form-group">
                            <label for="invoice_id">Invoice ID *</label>
                            <input type="number" 
                                   class="form-control @error('invoice_id') is-invalid @enderror" 
                                   id="invoice_id" 
                                   name="invoice_id" 
                                   value="{{ old('invoice_id') }}" 
                                   required>
                            @error('invoice_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Issue Date -->
                        <div class="form-group">
                            <label for="issue_date">Issue Date *</label>
                            <input type="date" 
                                   class="form-control @error('issue_date') is-invalid @enderror" 
                                   id="issue_date" 
                                   name="issue_date" 
                                   value="{{ old('issue_date') }}" 
                                   required>
                            @error('issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Expiry Date -->
                        <div class="form-group">
                            <label for="expiry_date">Expiry Date *</label>
                            <input type="date" 
                                   class="form-control @error('expiry_date') is-invalid @enderror" 
                                   id="expiry_date" 
                                   name="expiry_date" 
                                   value="{{ old('expiry_date') }}" 
                                   required>
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Permit Type -->
                        <div class="form-group">
                            <label for="permit_type">Permit Type</label>
                            <select class="form-control @error('permit_type') is-invalid @enderror" 
                                    id="permit_type" 
                                    name="permit_type" required>
                                <option value="printed" {{ old('permit_type') == 'printed' ? 'selected' : '' }}>Printed</option>
                                <option value="e-copy" {{ old('permit_type') == 'e-copy' ? 'selected' : '' }}>E-Copy</option>
                                <option value="General" {{ old('permit_type') == 'General' ? 'selected' : '' }}>General</option>
                                <option value="Special" {{ old('permit_type') == 'Special' ? 'selected' : '' }}>Special</option>
                                <option value="Temporary" {{ old('permit_type') == 'Temporary' ? 'selected' : '' }}>Temporary</option>
                            </select>
                            @error('permit_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Special Conditions -->
                        <div class="form-group">
                            <label for="special_conditions">Special Conditions</label>
                            <textarea class="form-control @error('special_conditions') is-invalid @enderror" 
                                      id="special_conditions" 
                                      name="special_conditions">{{ old('special_conditions') }}</textarea>
                            @error('special_conditions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Save Permit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
