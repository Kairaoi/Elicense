@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Permit Category Details</h1>
                <p>View details for the selected permit category.</p>
            </div>

            <!-- Details Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <!-- Permit Category Information -->
                    <h5 class="section-title">Permit Category Information</h5>
                    <p><strong>Category Name:</strong> {{ $permitCategory->category_name }}</p>
                    <p><strong>Description:</strong> {{ $permitCategory->description ?? 'Not Available' }}</p>
                    <p><strong>Base Fee:</strong> ${{ number_format($permitCategory->base_fee, 2) }}</p>
                    <p><strong>Certification Required:</strong> {{ $permitCategory->requires_certification ? 'Yes' : 'No' }}</p>

                    <!-- Action Buttons -->
                    <div class="text-center mt-5">
                        <a href="{{ route('pfps.permit-categories.edit', $permitCategory->category_id) }}" class="btn btn-custom-primary">Edit Permit Category</a>
                        <a href="{{ route('pfps.permit-categories.index') }}" class="btn btn-custom-secondary ms-3">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
