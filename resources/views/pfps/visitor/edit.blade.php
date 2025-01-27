@extends('layouts.app')

@push('styles')
<style>
    /* Custom styles reused for lodge forms */
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
                <h1>Edit Visitor</h1>
                <p>Update the details for the visitor.</p>
            </div>

            <!-- Form Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('pfps.visitors.update', $visitor->visitor_id) }}">
                        @csrf
                        @method('PUT')  <!-- Change the method to PUT for updating -->

                        <!-- First Name -->
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" 
                                   class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name', $visitor->first_name) }}" 
                                   required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" 
                                   class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name', $visitor->last_name) }}" 
                                   required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="form-group">
                            <label for="gender">Gender *</label>
                            <select class="form-control @error('gender') is-invalid @enderror" 
                                    id="gender" name="gender" required>
                                <option value="male" {{ old('gender', $visitor->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $visitor->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $visitor->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Home Address -->
                        <div class="form-group">
                            <label for="home_address">Home Address</label>
                            <textarea class="form-control @error('home_address') is-invalid @enderror" 
                                      id="home_address" name="home_address">{{ old('home_address', $visitor->home_address) }}</textarea>
                            @error('home_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Passport Number -->
                        <div class="form-group">
                            <label for="passport_number">Passport Number *</label>
                            <input type="text" 
                                   class="form-control @error('passport_number') is-invalid @enderror" 
                                   id="passport_number" 
                                   name="passport_number" 
                                   value="{{ old('passport_number', $visitor->passport_number) }}" 
                                   required>
                            @error('passport_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div class="form-group">
                            <label for="country_id">Country *</label>
                            <select class="form-control @error('country_id') is-invalid @enderror" 
                                    id="country_id" name="country_id" required>
                                <option value="">Select Country</option>
                                @foreach ($countries as $id => $country)
                                    <option value="{{ $id }}" 
                                            {{ old('country_id', $visitor->country_id) == $id ? 'selected' : '' }}>
                                        {{ $country }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Organization -->
                        <div class="form-group">
                            <label for="organization_id">Organization</label>
                            <select class="form-control @error('organization_id') is-invalid @enderror" 
                                    id="organization_id" name="organization_id">
                                <option value="">Select Organization</option>
                                @foreach ($organizations as $id => $organization)
                                    <option value="{{ $id }}" 
                                            {{ old('organization_id', $visitor->organization_id) == $id ? 'selected' : '' }}>
                                        {{ $organization }}
                                    </option>
                                @endforeach
                            </select>
                            @error('organization_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Arrival Date -->
                        <div class="form-group">
                            <label for="arrival_date">Arrival Date *</label>
                            <input type="date" 
                                   class="form-control @error('arrival_date') is-invalid @enderror" 
                                   id="arrival_date" 
                                   name="arrival_date" 
                                   value="{{ old('arrival_date', $visitor->arrival_date) }}" 
                                   required>
                            @error('arrival_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Departure Date -->
                        <div class="form-group">
                            <label for="departure_date">Departure Date *</label>
                            <input type="date" 
                                   class="form-control @error('departure_date') is-invalid @enderror" 
                                   id="departure_date" 
                                   name="departure_date" 
                                   value="{{ old('departure_date', $visitor->departure_date) }}" 
                                   required>
                            @error('departure_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Lodge -->
                        <div class="form-group">
                            <label for="lodge_id">Lodge *</label>
                            <select class="form-control @error('lodge_id') is-invalid @enderror" 
                                    id="lodge_id" name="lodge_id" required>
                                <option value="">Select Lodge</option>
                                @foreach ($lodges as $id => $lodge)
                                    <option value="{{ $id }}" 
                                            {{ old('lodge_id', $visitor->lodge_id) == $id ? 'selected' : '' }}>
                                        {{ $lodge }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lodge_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Emergency Contact -->
                        <div class="form-group">
                            <label for="emergency_contact">Emergency Contact</label>
                            <input type="text" 
                                   class="form-control @error('emergency_contact') is-invalid @enderror" 
                                   id="emergency_contact" 
                                   name="emergency_contact" 
                                   value="{{ old('emergency_contact', $visitor->emergency_contact) }}">
                            @error('emergency_contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Certification Number -->
                        <div class="form-group">
                            <label for="certification_number">Certification Number</label>
                            <input type="text" 
                                   class="form-control @error('certification_number') is-invalid @enderror" 
                                   id="certification_number" 
                                   name="certification_number" 
                                   value="{{ old('certification_number', $visitor->certification_number) }}">
                            @error('certification_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Certification Type -->
                        <div class="form-group">
                            <label for="certification_type">Certification Type</label>
                            <input type="text" 
                                   class="form-control @error('certification_type') is-invalid @enderror" 
                                   id="certification_type" 
                                   name="certification_type" 
                                   value="{{ old('certification_type', $visitor->certification_type) }}">
                            @error('certification_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Certification Expiry -->
                        <div class="form-group">
                            <label for="certification_expiry">Certification Expiry</label>
                            <input type="date" 
                                   class="form-control @error('certification_expiry') is-invalid @enderror" 
                                   id="certification_expiry" 
                                   name="certification_expiry" 
                                   value="{{ old('certification_expiry', $visitor->certification_expiry) }}">
                            @error('certification_expiry')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Update Visitor</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
