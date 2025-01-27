@extends('layouts.app')

@push('styles')
<style>
    /* Custom styles reused for permit category forms */
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
                <h1>Create New Permit Category</h1>
                <p>Add details for a new permit category.</p>
            </div>

            <!-- Form Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('pfps.permit-categories.store') }}">
                        @csrf
                        
                        <!-- Category Name -->
                        <div class="form-group">
                            <label for="category_name">Category Name *</label>
                            <input type="text" 
                                   class="form-control @error('category_name') is-invalid @enderror" 
                                   id="category_name" 
                                   name="category_name" 
                                   value="{{ old('category_name') }}" 
                                   required>
                            @error('category_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Base Fee -->
                        <div class="form-group">
                            <label for="base_fee">Base Fee *</label>
                            <input type="number" 
                                   class="form-control @error('base_fee') is-invalid @enderror" 
                                   id="base_fee" 
                                   name="base_fee" 
                                   value="{{ old('base_fee') }}" 
                                   required step="0.01">
                            @error('base_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Certification Required -->
                        <div class="form-group">
                            <label for="requires_certification">Requires Certification?</label>
                            <select class="form-control @error('requires_certification') is-invalid @enderror" 
                                    id="requires_certification" 
                                    name="requires_certification">
                                <option value="1" {{ old('requires_certification') == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('requires_certification') == 0 ? 'selected' : '' }}>No</option>
                            </select>
                            @error('requires_certification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Save Permit Category</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
