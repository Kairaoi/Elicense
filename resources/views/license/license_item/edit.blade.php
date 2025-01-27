@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit License Item</h1>
    
    <form action="{{ route('license-items.update', $licenseItem->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="license_id" class="form-label">License</label>
            <select name="license_id" id="license_id" class="form-control" required>
                <option value="">Select a license</option>
                @foreach($licenses as $license)
                    <option value="{{ $license->id }}" {{ $license->id == $licenseItem->license_id ? 'selected' : '' }}>{{ $license->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="species_id" class="form-label">Species</label>
            <select name="species_id" id="species_id" class="form-control" required>
                <option value="">Select a species</option>
                @foreach($species as $specie)
                    <option value="{{ $specie->id }}" {{ $specie->id == $licenseItem->species_id ? 'selected' : '' }}>{{ $specie->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" step="0.01" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', $licenseItem->quantity) }}" required>
        </div>

        <div class="mb-3">
            <label for="unit_price" class="form-label">Unit Price</label>
            <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control" value="{{ old('unit_price', $licenseItem->unit_price) }}" required>
        </div>

        <div class="mb-3">
            <label for="total_price" class="form-label">Total Price</label>
            <input type="number" step="0.01" name="total_price" id="total_price" class="form-control" value="{{ old('total_price', $licenseItem->total_price) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update License Item</button>
        <a href="{{ route('license-items.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
