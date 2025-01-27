@extends('layouts.app')

@push('styles')
<style>
    /* Custom styles reused for country forms */
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Create New Country</h1>
                <p>Add details for a new country.</p>
            </div>

            <!-- Form Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                <form method="POST" action="{{ route('pfps.countries.store') }}">
    @csrf
    <div class="form-group">
        <label for="country_name">Country Name *</label>
        <input type="text" 
               class="form-control @error('country_name') is-invalid @enderror" 
               id="country_name" 
               name="country_name" 
               value="{{ old('country_name') }}" 
               required>
        @error('country_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="iso_code">ISO Code *</label>
        <input type="text" 
               class="form-control @error('iso_code') is-invalid @enderror" 
               id="iso_code" 
               name="iso_code" 
               value="{{ old('iso_code') }}" 
               maxlength="2" 
               style="text-transform:uppercase" 
               required>
        <small class="form-text text-muted">Enter 2-letter ISO country code (e.g., KI for Kiribati)</small>
        @error('iso_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Save Country</button>
</form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
