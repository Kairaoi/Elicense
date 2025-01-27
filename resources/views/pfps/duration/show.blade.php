@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Fishing Duration Details</h1>
                <p>View details for the selected fishing duration.</p>
            </div>

            <!-- Details Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <!-- Duration Information -->
                    <h5 class="section-title">Fishing Duration Information</h5>
                    <p><strong>Duration Name:</strong> {{ $duration->duration_name }}</p>
                    <p><strong>Fee Amount:</strong> ${{ number_format($duration->fee_amount, 2) }}</p>

                    <!-- Action Buttons -->
                    <div class="text-center mt-5">
                        <a href="{{ route('pfps.durations.edit', $duration->duration_id) }}" class="btn btn-custom-primary">Edit Duration</a>
                        <a href="{{ route('pfps.durations.index') }}" class="btn btn-custom-secondary ms-3">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
