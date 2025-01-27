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
                <h1>Create New Lodge</h1>
                <p>Add details for a new lodge.</p>
            </div>

            <!-- Form Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('pfps.lodges.store') }}">
                        @csrf
                        
                        <!-- Lodge Name -->
                        <div class="form-group">
                            <label for="lodge_name">Lodge Name *</label>
                            <input type="text" 
                                   class="form-control @error('lodge_name') is-invalid @enderror" 
                                   id="lodge_name" 
                                   name="lodge_name" 
                                   value="{{ old('lodge_name') }}" 
                                   required>
                            @error('lodge_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Location -->
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" 
                                   class="form-control @error('location') is-invalid @enderror" 
                                   id="location" 
                                   name="location" 
                                   value="{{ old('location') }}">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Save Lodge</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
