@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Country Details</h1>
                <p>View details for the selected country.</p>
            </div>

            <!-- Details Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <!-- Country Information -->
                    <h5 class="section-title">Country Information</h5>
                    <p><strong>Name:</strong> {{ $country->name }}</p>
                    <p><strong>ISO Code:</strong> {{ $country->iso_code }}</p>

                    <!-- Action Buttons -->
                    <div class="text-center mt-5">
                        <a href="{{ route('pfps.countries.edit', $country->id) }}" class="btn btn-custom-primary">Edit Country</a>
                        <a href="{{ route('pfps.countries.index') }}" class="btn btn-custom-secondary ms-3">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
