@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="page-header mb-4">
                <h1>Edit Annual Trip Fee</h1>
                <p>Update the details of the fee for the selected year.</p>
            </div>

            <!-- Form Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('pfps.annual_trip_fees.update', $fee->fee_id) }}">
                        @csrf
                        @method('PUT') <!-- Method spoofing -->

                        <!-- Amount -->
                        <div class="form-group mb-3">
                            <label for="amount">Amount *</label>
                            <input type="number" 
                                   class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" 
                                   name="amount" 
                                   value="{{ old('amount', $fee->amount) }}" 
                                   step="0.01" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Currency -->
                        <div class="form-group mb-3">
                            <label for="currency">Currency *</label>
                            <input type="text" 
                                   class="form-control @error('currency') is-invalid @enderror" 
                                   id="currency" 
                                   name="currency" 
                                   value="{{ old('currency', $fee->currency) }}" 
                                   maxlength="3" required>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Year -->
                        <div class="form-group mb-3">
                            <label for="year">Year *</label>
                            <input type="number" 
                                   class="form-control @error('year') is-invalid @enderror" 
                                   id="year" 
                                   name="year" 
                                   value="{{ old('year', $fee->year) }}" required>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Update Fee</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
