@extends('layouts.app')

@section('title', 'Create New Agent')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .form-group label {
        font-weight: 600;
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .card-title {
        margin-bottom: 0;
        font-size: 1.25rem;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Create New Agent</h4>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('license.agents.store') }}" id="agent-form">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                        class="form-control @error('first_name') is-invalid @enderror" 
                                        id="first_name" 
                                        name="first_name" 
                                        value="{{ old('first_name') }}" 
                                        required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                        class="form-control @error('last_name') is-invalid @enderror" 
                                        id="last_name" 
                                        name="last_name" 
                                        value="{{ old('last_name') }}" 
                                        required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" 
                                        class="form-control @error('phone_number') is-invalid @enderror" 
                                        id="phone_number" 
                                        name="phone_number" 
                                        value="{{ old('phone_number') }}" 
                                        required>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" 
                                        class="form-control @error('email') is-invalid @enderror" 
                                        id="email" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="applicant_id">Applicant <span class="text-danger">*</span></label>
                                    <select class="form-control @error('applicant_id') is-invalid @enderror" 
                                        id="applicant_id" 
                                        name="applicant_id" 
                                        required>
                                        <option value="">Select Applicant</option>
                                        @foreach($applicants as $id => $name)
                                            <option value="{{ $id }}" {{ old('applicant_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('applicant_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="islands">Islands <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('islands') is-invalid @enderror" 
                                        id="islands" 
                                        name="islands[]" 
                                        multiple 
                                        required>
                                        @foreach($islands as $id => $name)
                                            <option value="{{ $id }}" {{ collect(old('islands'))->contains($id) ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('islands')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" 
                                        class="form-control @error('start_date') is-invalid @enderror" 
                                        id="start_date" 
                                        name="start_date" 
                                        value="{{ old('start_date') }}" 
                                        required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" 
                                        class="form-control @error('end_date') is-invalid @enderror" 
                                        id="end_date" 
                                        name="end_date" 
                                        value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                        id="notes" 
                                        name="notes" 
                                        rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">Create Agent</button>
                                <a href="{{ route('license.agents.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select Islands',
            allowClear: true
        });

        $('#agent-form').on('submit', function(e) {
            if (!$('#islands').val().length) {
                e.preventDefault();
                alert('Please select at least one island');
                return false;
            }
        });

        $('#end_date').on('change', function() {
            const startDate = $('#start_date').val();
            const endDate = $(this).val();

            if (startDate && endDate && endDate < startDate) {
                alert('End date must be after start date');
                $(this).val('');
            }
        });
    });
</script>
@endsection
