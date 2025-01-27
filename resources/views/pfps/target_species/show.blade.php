@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Target Species Details</h1>
                <p>View details for the selected species.</p>
            </div>

            <!-- Details Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <!-- Species Information -->
                    <h5 class="section-title">Species Information</h5>
                    <p><strong>Species Name:</strong> {{ $species->species_name }}</p>
                    <p><strong>Species Category:</strong> {{ $species->species_category }}</p>
                    <p><strong>Description:</strong> {{ $species->description ?? 'No description available.' }}</p>

                    <!-- Action Buttons -->
                    <div class="text-center mt-5">
                        <a href="{{ route('pfps.target_species.edit', $species->species_id) }}" class="btn btn-custom-primary">Edit Species</a>
                        <a href="{{ route('pfps.target_species.index') }}" class="btn btn-custom-secondary ms-3">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
