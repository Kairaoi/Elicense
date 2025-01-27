@extends('layouts.app')

@push('styles')
<style>
    .form-group label {
        font-weight: bold;
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
                <h1>Create Annual Trip Fee</h1>
                <p>Add a new fee for a specific year.</p>
            </div>

            <!-- Form Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('pfps.annual_trip_fees.store') }}">
                        @csrf
                        
                        <!-- Amount -->
                        <div class="form-group mb-3">
                            <label for="amount">Amount *</label>
                            <input type="number" 
                                   class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" 
                                   name="amount" 
                                   value="{{ old('amount', 250.00) }}" 
                                   step="0.01" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Currency -->
                        <div class="form-group mb-3">
                            <label for="currency">Currency *</label>
                            <input type="text" 
                                   class="form-control @error('currency') is-invalid @enderror" 
                                   id="currency" 
                                   name="currency" 
                                   value="{{ old('currency', 'USD') }}" 
                                   maxlength="3" required>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Year -->
                        <div class="form-group mb-3">
                            <label for="year">Year *</label>
                            <input type="number" 
                                   class="form-control @error('year') is-invalid @enderror" 
                                   id="year" 
                                   name="year" 
                                   value="{{ old('year', now()->year) }}" required>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Save Fee</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
