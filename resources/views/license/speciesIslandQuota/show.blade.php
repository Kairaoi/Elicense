@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Species Island Quota Details</h2>

    <div class="card">

        <div class="card-body">

            {{-- Check if species relation exists --}}
            <h4 class="card-title">Species: {{ $speciesIslandQuota->species->name ?? 'N/A' }}</h4>

            {{-- Check if island relation exists --}}
            <p><strong>Island: </strong>{{ $speciesIslandQuota->island->name ?? 'N/A' }}</p>

            <p><strong>Island Quota: </strong>{{ $speciesIslandQuota->island_quota }}</p>

            <p><strong>Remaining Quota: </strong>{{ $speciesIslandQuota->remaining_quota }}</p>

            <p><strong>Year: </strong>{{ $speciesIslandQuota->year }}</p>

            {{-- Check if createdBy relation exists --}}
            <p><strong>Created By: </strong>{{ $speciesIslandQuota->createdBy->name ?? 'N/A' }}</p>

            {{-- Check if updatedBy relation exists --}}
            <p><strong>Updated By: </strong>{{ $speciesIslandQuota->updatedBy->name ?? 'N/A' }}</p>

            <p><strong>Created At: </strong>{{ $speciesIslandQuota->created_at }}</p>

            <p><strong>Updated At: </strong>{{ $speciesIslandQuota->updated_at }}</p>

            <p><strong>Deleted At: </strong>{{ $speciesIslandQuota->deleted_at ?? 'N/A' }}</p>

        </div>
    </div>

</div>

@endsection
