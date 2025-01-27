@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create New Species') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('reference.species.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Species Name') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                    name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="license_type_id" class="col-md-4 col-form-label text-md-end">{{ __('License Type') }}</label>
                            <div class="col-md-6">
                                <select id="license_type_id" class="form-control @error('license_type_id') is-invalid @enderror" 
                                    name="license_type_id" required>
                                    <option value="">Select License Type</option>
                                    @foreach($licenseTypes as $id => $type)
                                        <option value="{{ $id }}" {{ old('license_type_id') == $id ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('license_type_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="quota" class="col-md-4 col-form-label text-md-end">{{ __('Quota') }}</label>
                            <div class="col-md-6">
                                <input id="quota" type="number" step="0.01" class="form-control @error('quota') is-invalid @enderror" 
                                    name="quota" value="{{ old('quota') }}" required>
                                @error('quota')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="unit_price" class="col-md-4 col-form-label text-md-end">{{ __('Unit Price') }}</label>
                            <div class="col-md-6">
                                <input id="unit_price" type="number" step="0.01" class="form-control @error('unit_price') is-invalid @enderror" 
                                    name="unit_price" value="{{ old('unit_price') }}" required>
                                @error('unit_price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Create Species') }}
                                </button>
                                <a href="{{ route('reference.species.index') }}" class="btn btn-secondary">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection