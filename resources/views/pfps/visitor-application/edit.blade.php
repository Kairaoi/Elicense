@extends('layouts.app')

@push('styles')
<style>
    /* Custom styles reused for forms */
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
                <h1>Edit Visitor Application</h1>
                <p>Update details for the selected visitor application.</p>
            </div>

            <!-- Form Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                    <form method="POST" action="{{ route('pfps.visitor-applications.update', $application->application_id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Visitor -->
                        <div class="form-group">
                            <label for="visitor_id">Visitor *</label>
                            <select class="form-control @error('visitor_id') is-invalid @enderror" 
                                    id="visitor_id" 
                                    name="visitor_id" 
                                    required>
                                <option value="">Select Visitor</option>
                                @foreach($visitors as $id => $visitor)
                                    <option value="{{ $id }}" 
                                            {{ old('visitor_id', $application->visitor_id) == $id ? 'selected' : '' }}>
                                        {{ $visitor }}
                                    </option>
                                @endforeach
                            </select>
                            @error('visitor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="form-group">
                            <label for="category_id">Category *</label>
                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                    id="category_id" 
                                    name="category_id" 
                                    required>
                                <option value="">Select Category</option>
                                @foreach($categories as $id => $category)
                                    <option value="{{ $id }}" 
                                            {{ old('category_id', $application->category_id) == $id ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Activity Type -->
                        <div class="form-group">
                            <label for="activity_type_id">Activity Type *</label>
                            <select class="form-control @error('activity_type_id') is-invalid @enderror" 
                                    id="activity_type_id" 
                                    name="activity_type_id" 
                                    required>
                                <option value="">Select Activity Type</option>
                                @foreach($activityTypes as $id => $activityType)
                                    <option value="{{ $id }}" 
                                            {{ old('activity_type_id', $application->activity_type_id) == $id ? 'selected' : '' }}>
                                        {{ $activityType }}
                                    </option>
                                @endforeach
                            </select>
                            @error('activity_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div class="form-group">
                            <label for="duration_id">Duration *</label>
                            <select class="form-control @error('duration_id') is-invalid @enderror" 
                                    id="duration_id" 
                                    name="duration_id" 
                                    required>
                                <option value="">Select Duration</option>
                                @foreach($durations as $id => $duration)
                                    <option value="{{ $id }}" 
                                            {{ old('duration_id', $application->duration_id) == $id ? 'selected' : '' }}>
                                        {{ $duration }}
                                    </option>
                                @endforeach
                            </select>
                            @error('duration_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="">Select Status</option>
                                <option value="pending" {{ old('status', $application->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('status', $application->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ old('status', $application->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Application Date -->
                        <div class="form-group">
                            <label for="application_date">Application Date *</label>
                            <input type="date" 
                                   class="form-control @error('application_date') is-invalid @enderror" 
                                   id="application_date" 
                                   name="application_date" 
                                   value="{{ old('application_date', $application->application_date) }}" 
                                   required>
                            @error('application_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Rejection Reason -->
                        <div class="form-group">
                            <label for="rejection_reason">Rejection Reason</label>
                            <textarea class="form-control @error('rejection_reason') is-invalid @enderror" 
                                      id="rejection_reason" 
                                      name="rejection_reason" 
                                      rows="3">{{ old('rejection_reason', $application->rejection_reason) }}</textarea>
                            @error('rejection_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Update Application</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
