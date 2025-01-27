@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="elegant-heading">Island Details</h1>
        <a href="{{ route('reference.islands.index') }}" class="btn btn-secondary elegant-back-btn">Back to List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Island: {{ $island->name }}</h5>
            <p class="card-text">Created At: {{ $island->created_at->format('d M Y H:i') }}</p>
            <p class="card-text">Updated At: {{ $island->updated_at ? $island->updated_at->format('d M Y H:i') : 'N/A' }}</p>
            <p class="card-text">Created By: {{ $island->createdBy->name ?? 'Unknown' }}</p>
            <p class="card-text">Updated By: {{ $island->updatedBy->name ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('reference.islands.edit', $island->id) }}" class="btn btn-primary">Edit</a>
    </div>
</div>
@endsection
