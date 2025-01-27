@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="elegant-heading">Harvester Applicant Details</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $applicant->first_name }} {{ $applicant->last_name }}</h5>
            <p class="card-text"><strong>Phone Number:</strong> {{ $applicant->phone_number }}</p>
            <p class="card-text"><strong>Email:</strong> {{ $applicant->email }}</p>
            <p class="card-text"><strong>Is Group:</strong> {{ $applicant->is_group ? 'Yes' : 'No' }}</p>
            @if ($applicant->is_group)
                <p class="card-text"><strong>Group Size:</strong> {{ $applicant->group_size }}</p>
            @endif
            <p class="card-text"><strong>National ID:</strong> {{ $applicant->national_id }}</p>
            <p class="card-text"><strong>Created At:</strong> {{ $applicant->created_at->format('Y-m-d H:i') }}</p>
            <p class="card-text"><strong>Updated At:</strong> {{ $applicant->updated_at->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('harvester.applicants.edit', $applicant->id) }}" class="btn btn-secondary">Edit Applicant</a>
        <a href="{{ route('harvester.applicants.index') }}" class="btn btn-primary">Back to List</a>
    </div>
</div>
@endsection
