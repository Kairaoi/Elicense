@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Equipment Rental Details</h1>
                <p>View details for the selected equipment rental.</p>
            </div>

            <!-- Details Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <!-- Equipment Rental Information -->
                    <h5 class="section-title">Rental Information</h5>
                    <p><strong>Equipment Type:</strong> {{ $equipmentRental->equipment_type }}</p>
                    <p><strong>Rental Fee:</strong> {{ $equipmentRental->rental_fee }} {{ $equipmentRental->currency }}</p>
                    <p><strong>Rental Date:</strong> {{ $equipmentRental->rental_date }}</p>
                    <p><strong>Return Date:</strong> {{ $equipmentRental->return_date }}</p>

                    <!-- Action Buttons -->
                    <div class="text-center mt-5">
                        <a href="{{ route('pfps.equipment_rentals.edit', $equipmentRental->rental_id) }}" class="btn btn-custom-primary">Edit Rental</a>
                        <a href="{{ route('pfps.equipment_rentals.index') }}" class="btn btn-custom-secondary ms-3">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
