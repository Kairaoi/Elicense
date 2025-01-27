@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Species Details') }}</div>

                <div class="card-body">
                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end">{{ __('Species Name') }}</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ $species->name }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end">{{ __('License Type') }}</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ $species->licenseType->name }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end">{{ __('Quota') }}</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ $species->quota }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end">{{ __('Unit Price') }}</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ $species->unit_price }}</p>
                        </div>
                    </div>

                    

                    <div class="row mb-3">
                        <label class="col-md-4 col-form-label text-md-end">{{ __('Created At') }}</label>
                        <div class="col-md-6">
                            <p class="form-control-static">{{ $species->created_at }}</p>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{ route('reference.species.edit', $species->id) }}" class="btn btn-primary">
                                {{ __('Edit') }}
                            </a>
                            <a href="{{ route('reference.species.index') }}" class="btn btn-secondary">
                                {{ __('Back') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection