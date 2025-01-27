@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Lodge Details</h1>
                <p>View details for the selected lodge.</p>
            </div>

            <!-- Details Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <!-- Lodge Information -->
                    <h5 class="section-title">Lodge Information</h5>
                    <p><strong>Lodge Name:</strong> {{ $lodge->lodge_name }}</p>
                    <p><strong>Location:</strong> {{ $lodge->location ?? 'Not Available' }}</p>

                    <!-- Action Buttons -->
                    <div class="text-center mt-5">
                        <a href="{{ route('pfps.lodges.edit', $lodge->lodge_id) }}" class="btn btn-custom-primary">Edit Lodge</a>
                        <a href="{{ route('pfps.lodges.index') }}" class="btn btn-custom-secondary ms-3">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
