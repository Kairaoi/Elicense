@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Activity Site Details</h1>
                <p>View details for the selected fishing spot.</p>
            </div>

            <!-- Details Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <!-- Site Information -->
                    <h5 class="section-title">Site Information</h5>
                    <p><strong>Site Name:</strong> {{ $site->site_name }}</p>
                    <p><strong>Category:</strong> {{ $site->category->category_name }}</p>
                    <p><strong>Description:</strong> {{ $site->description ?? 'Not Available' }}</p>
                    <p><strong>Location:</strong> {{ $site->location ?? 'Not Available' }}</p>

                    <!-- Action Buttons -->
                    <div class="text-center mt-5">
                        <a href="{{ route('pfps.activity_sites.edit', $site->site_id) }}" class="btn btn-custom-primary">Edit Site</a>
                        <a href="{{ route('pfps.activity_sites.index') }}" class="btn btn-custom-secondary ms-3">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
