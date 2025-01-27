@extends('layouts.app')

@section('content')
<div class="container">
    <h1>License Type Details</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Name: {{ $licenseType->name }}</h5>
            <p class="card-text">Created At: {{ $licenseType->created_at }}</p>
            <p class="card-text">Updated At: {{ $licenseType->updated_at }}</p>
            <p class="card-text">Created By: {{ $licenseType->createdBy->name ?? 'N/A' }}</p>
            <p class="card-text">Updated By: {{ $licenseType->updatedBy->name ?? 'N/A' }}</p>
        </div>
    </div>

    <a href="{{ route('license.licenses_types.index') }}" class="btn btn-secondary mt-3">Back</a>
    <a href="{{ route('license.licenses_types.edit', $licenseType->id) }}" class="btn btn-warning mt-3">Edit</a>
</div>
@endsection
