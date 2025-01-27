@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Activity Type Details</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Activity Name: {{ $activityType->activity_name }}</h5>
            <p class="card-text"><strong>Category:</strong> {{ $activityType->category->category_name ?? 'N/A' }}</p>
            <p class="card-text"><strong>Requirements:</strong> {{ $activityType->requirements ?? 'N/A' }}</p>
            <p class="card-text"><strong>Created At:</strong> {{ $activityType->created_at }}</p>
            <p class="card-text"><strong>Updated At:</strong> {{ $activityType->updated_at }}</p>
            <p class="card-text"><strong>Created By:</strong> {{ $activityType->createdBy->name ?? 'N/A' }}</p>
            <p class="card-text"><strong>Updated By:</strong> {{ $activityType->updatedBy->name ?? 'N/A' }}</p>
        </div>
    </div>

    <a href="{{ route('pfps.activity-types.index') }}" class="btn btn-secondary mt-3">Back</a>
    <a href="{{ route('pfps.activity-types.edit', $activityType->activity_type_id) }}" class="btn btn-warning mt-3">Edit</a>
</div>
@endsection
