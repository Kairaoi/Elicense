@extends('layouts.app')

@push('styles')
<style>
    /* Custom styles reused for duration forms */
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
                <h1>Create New Fishing Duration</h1>
                <p>Add details for a new fishing duration.</p>
            </div>

            <!-- Form Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('pfps.durations.store') }}">
                        @csrf
                        
                        <!-- Duration Name -->
                        <div class="form-group">
                            <label for="duration_name">Duration Name *</label>
                            <input type="text" 
                                   class="form-control @error('duration_name') is-invalid @enderror" 
                                   id="duration_name" 
                                   name="duration_name" 
                                   value="{{ old('duration_name') }}" 
                                   required>
                            @error('duration_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fee Amount -->
                        <div class="form-group">
                            <label for="fee_amount">Fee Amount *</label>
                            <input type="number" 
                                   class="form-control @error('fee_amount') is-invalid @enderror" 
                                   id="fee_amount" 
                                   name="fee_amount" 
                                   value="{{ old('fee_amount') }}" 
                                   required step="0.01">
                            @error('fee_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Save Duration</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
