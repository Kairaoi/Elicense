@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Country</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('pfps.countries.update', $country->country_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Country Name -->
                        <div class="mb-3">
                            <label for="country_name" class="form-label">Country Name</label>
                            <input type="text" id="country_name" name="country_name" 
                                   class="form-control @error('country_name') is-invalid @enderror" 
                                   value="{{ old('country_name', $country->country_name) }}" required>
                            @error('country_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ISO Code -->
                        <div class="mb-3">
                            <label for="iso_code" class="form-label">ISO Code</label>
                            <input type="text" id="iso_code" name="iso_code" 
                                   class="form-control @error('iso_code') is-invalid @enderror" 
                                   value="{{ old('iso_code', $country->iso_code) }}" required>
                            @error('iso_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Update</button>
                            <a href="{{ route('pfps.countries.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
