@extends('layouts.app')

@push('styles')
<style>
    /* Custom styles reused for equipment rental forms */
    .form-group label {
        font-weight: bold;
    }
    .form-text {
        font-size: 0.9em;
    }
    .custom-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="page-header mb-4">
                <h1>Edit Equipment Rental</h1>
                <p>Update details for the selected equipment rental.</p>
            </div>

            <!-- Form Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('pfps.equipment_rentals.update', $equipmentRental->rental_id) }}">
                        @csrf
                        @method('PUT')  <!-- Method Spoofing to send PUT request -->
                        
                        <!-- Permit ID -->
                        <div class="form-group">
                            <label for="permit_id">Permit ID *</label>
                            <select class="form-control @error('permit_id') is-invalid @enderror" 
                                    id="permit_id" 
                                    name="permit_id" 
                                    required>
                                <option value="" disabled>Select a Permit</option>
                                @foreach($permits as $id => $permit)
                                    <option value="{{ $id }}" 
                                            {{ old('permit_id', $equipmentRental->permit_id) == $id ? 'selected' : '' }}>
                                        {{ $permit }} <!-- Replace with the appropriate field -->
                                    </option>
                                @endforeach
                            </select>
                            @error('permit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Equipment Type -->
                        <div class="form-group">
                            <label for="equipment_type">Equipment Type *</label>
                            <input type="text" 
                                   class="form-control @error('equipment_type') is-invalid @enderror" 
                                   id="equipment_type" 
                                   name="equipment_type" 
                                   value="{{ old('equipment_type', $equipmentRental->equipment_type) }}" 
                                   required>
                            @error('equipment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Rental Fee -->
                        <div class="form-group">
                            <label for="rental_fee">Rental Fee *</label>
                            <input type="number" step="0.01" 
                                   class="form-control @error('rental_fee') is-invalid @enderror" 
                                   id="rental_fee" 
                                   name="rental_fee" 
                                   value="{{ old('rental_fee', $equipmentRental->rental_fee) }}" 
                                   required>
                            @error('rental_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Currency -->
                        <div class="form-group">
                            <label for="currency">Currency *</label>
                            <input type="text" maxlength="3"
                                   class="form-control @error('currency') is-invalid @enderror" 
                                   id="currency" 
                                   name="currency" 
                                   value="{{ old('currency', $equipmentRental->currency) }}" 
                                   required>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Rental Date -->
                        <div class="form-group">
                            <label for="rental_date">Rental Date *</label>
                            <input type="date" 
                                   class="form-control @error('rental_date') is-invalid @enderror" 
                                   id="rental_date" 
                                   name="rental_date" 
                                   value="{{ old('rental_date', $equipmentRental->rental_date) }}" 
                                   required>
                            @error('rental_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Return Date -->
                        <div class="form-group">
                            <label for="return_date">Return Date *</label>
                            <input type="date" 
                                   class="form-control @error('return_date') is-invalid @enderror" 
                                   id="return_date" 
                                   name="return_date" 
                                   value="{{ old('return_date', $equipmentRental->return_date) }}" 
                                   required>
                            @error('return_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Update Rental</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
