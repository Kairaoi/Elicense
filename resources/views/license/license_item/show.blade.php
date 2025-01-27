@extends('layouts.app')

@section('content')
<div class="container">
    <h1>License Item Details</h1>

    <div class="mb-3">
        <strong>License:</strong> {{ $licenseItem->license->name }}
    </div>
    <div class="mb-3">
        <strong>Species:</strong> {{ $licenseItem->species->name }}
    </div>
    <div class="mb-3">
        <strong>Quantity:</strong> {{ $licenseItem->quantity }}
    </div>
    <div class="mb-3">
        <strong>Unit Price:</strong> {{ number_format($licenseItem->unit_price, 2) }}
    </div>
    <div class="mb-3">
        <strong>Total Price:</strong> {{ number_format($licenseItem->total_price, 2) }}
    </div>
    <div class="mb-3">
        <strong>Created At:</strong> {{ $licenseItem->created_at->format('d-m-Y H:i') }}
    </div>
    <div class="mb-3">
        <strong>Updated At:</strong> {{ $licenseItem->updated_at->format('d-m-Y H:i') }}
    </div>

    <a href="{{ route('license-items.index') }}" class="btn btn-secondary">Back to License Items</a>
    <a href="{{ route('license-items.edit', $licenseItem->id) }}" class="btn btn-primary">Edit License Item</a>
</div>
@endsection
