@extends('layouts.app')

@push('styles')
<style>
    /* Custom styles reused for species forms */
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
                <h1>Edit Target Species</h1>
                <p>Update details for the selected species.</p>
            </div>

            <!-- Form Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('pfps.target_species.update', $species->species_id) }}">
                        @csrf
                        @method('PUT')  <!-- Method Spoofing to send PUT request -->
                        
                        <!-- Species Name -->
                        <div class="form-group">
                            <label for="species_name">Species Name *</label>
                            <input type="text" 
                                   class="form-control @error('species_name') is-invalid @enderror" 
                                   id="species_name" 
                                   name="species_name" 
                                   value="{{ old('species_name', $species->species_name) }}" 
                                   required>
                            @error('species_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Species Category -->
                        <div class="form-group">
                            <label for="species_category">Species Category *</label>
                            <input type="text" 
                                   class="form-control @error('species_category') is-invalid @enderror" 
                                   id="species_category" 
                                   name="species_category" 
                                   value="{{ old('species_category', $species->species_category) }}" 
                                   required>
                            @error('species_category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description">{{ old('description', $species->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Update Species</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
