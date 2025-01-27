@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Species Island Quota Details</h2>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Species: {{ $speciesIslandQuota->species->name }}</h4>
            <p><strong>Island: </strong>{{ $speciesIslandQuota->island->name }}</p>
            <p><strong>Island Quota: </strong>{{ $speciesIslandQuota->island_quota }}</p>
            <p><strong>Remaining Quota: </strong>{{ $speciesIslandQuota->remaining_quota }}</p>
            <p><strong>Year: </strong>{{ $speciesIslandQuota->year }}</p>
            <p><strong>Created By: </strong>{{ $speciesIslandQuota->createdBy->name }}</p>
            <p><strong>Updated By: </strong>{{ $speciesIslandQuota->updatedBy->name }}</p>
            <p><strong>Created At: </strong>{{ $speciesIslandQuota->created_at }}</p>
            <p><strong>Updated At: </strong>{{ $speciesIslandQuota->updated_at }}</p>
            <p><strong>Deleted At: </strong>{{ $speciesIslandQuota->deleted_at ?? 'N/A' }}</p>
        </div>
    </div>

    <a href="{{ route('species-island-quotas.quota.index') }}" class="btn btn-secondary mt-3">Back to List</a>
</div>
@endsection
