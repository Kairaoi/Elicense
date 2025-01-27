@extends('layouts.app')

@push('styles')
<style>
    /* Copy all your existing styles here */
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <!-- Header -->
            <div class="page-header">
                <h1>Edit Applicant Details</h1>
                <p>Update the information below</p>
            </div>

            <!-- Form Card -->
            <div class="card custom-card">
                <div class="card-body p-5">
                <form action="{{ route('applicant.applicants.update', $applicant->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">

                        <!-- Personal Information -->
                        <h5 class="section-title">Personal Information</h5>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">First Name <span class="required-mark">*</span></label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                                       required value="{{ old('first_name', $applicant->first_name) }}">
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name <span class="required-mark">*</span></label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                                       required value="{{ old('last_name', $applicant->last_name) }}">
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Company Details -->
                        <h5 class="section-title">Company Details</h5>
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <label class="form-label">Company Name <span class="required-mark">*</span></label>
                                <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" 
                                       required value="{{ old('company_name', $applicant->company_name) }}">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Registration Number <span class="required-mark">*</span></label>
                                <input type="text" name="local_registration_number" class="form-control @error('local_registration_number') is-invalid @enderror" 
                                       required value="{{ old('local_registration_number', $applicant->local_registration_number) }}">
                                @error('local_registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Company Type <span class="required-mark">*</span></label>
                                <select name="types_of_company" class="form-select @error('types_of_company') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="Corporation" {{ old('types_of_company', $applicant->types_of_company) == 'Corporation' ? 'selected' : '' }}>Corporation</option>
                                    <option value="Partnership" {{ old('types_of_company', $applicant->types_of_company) == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                    <option value="Single Private Company" {{ old('types_of_company', $applicant->types_of_company) == 'Single Private Company' ? 'selected' : '' }}>Single Private Company</option>
                                </select>
                                @error('types_of_company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date Established <span class="required-mark">*</span></label>
                                <input type="date" name="date_of_establishment" class="form-control @error('date_of_establishment') is-invalid @enderror" 
                                       required value="{{ old('date_of_establishment', $applicant->date_of_establishment) }}">
                                @error('date_of_establishment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Citizenship <span class="required-mark">*</span></label>
                                <input type="text" name="citizenship" class="form-control @error('citizenship') is-invalid @enderror" 
                                       required value="{{ old('citizenship', $applicant->citizenship) }}">
                                @error('citizenship')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <h5 class="section-title">Contact Information</h5>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Work Address <span class="required-mark">*</span></label>
                                <input type="text" name="work_address" class="form-control @error('work_address') is-invalid @enderror" 
                                       required value="{{ old('work_address', $applicant->work_address) }}">
                                @error('work_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Registered Address <span class="required-mark">*</span></label>
                                <input type="text" name="registered_address" class="form-control @error('registered_address') is-invalid @enderror" 
                                       required value="{{ old('registered_address', $applicant->registered_address) }}">
                                @error('registered_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Foreign Investment License</label>
                                <input type="text" name="foreign_investment_license" class="form-control @error('foreign_investment_license') is-invalid @enderror" 
                                       value="{{ old('foreign_investment_license', $applicant->foreign_investment_license) }}">
                                @error('foreign_investment_license')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number <span class="required-mark">*</span></label>
                                <input type="tel" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                                       required value="{{ old('phone_number', $applicant->phone_number) }}">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="required-mark">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       required value="{{ old('email', $applicant->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-custom-primary">Update Details</button>
                            <a href="{{ route('applicant.applicants.index') }}" class="btn btn-custom-secondary ms-3">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection