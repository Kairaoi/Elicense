@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Annual Trip Fee Details</h1>
                <p>View details of the fee for the year {{ $fee->year }}.</p>
            </div>

            <!-- Details Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <h5 class="section-title">Fee Information</h5>
                    <p><strong>Amount:</strong> {{ $fee->amount }}</p>
                    <p><strong>Currency:</strong> {{ $fee->currency }}</p>
                    <p><strong>Year:</strong> {{ $fee->year }}</p>

                    <!-- Action Buttons -->
                    <div class="text-center mt-5">
                        <a href="{{ route('pfps.annual_trip_fees.edit', $fee->fee_id) }}" class="btn btn-primary">Edit Fee</a>
                        <a href="{{ route('pfps.annual_trip_fees.index') }}" class="btn btn-secondary ms-3">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
