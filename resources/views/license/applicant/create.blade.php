@extends('layouts.app')

@push('styles')
<style>
    /* Custom styling for modern look */
    .form-control, .form-select {
        padding: 0.85rem 1.2rem;
        border-radius: 12px;
        border: 2px solid #d1d3e2;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 10px rgba(78, 115, 223, 0.25);
    }

    .section-title {
        color: #2c3e50;
        font-size: 1.25rem;
        font-weight: 600;
        padding-bottom: 10px;
        border-bottom: 2px solid #4e73df;
        margin-bottom: 30px;
    }

    .required-mark {
        color: #e74a3b;
        margin-left: 5px;
    }

    .custom-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
        background: #fff;
    }

    .btn-custom-primary {
        background: linear-gradient(45deg, #4e73df, #224abe);
        border: none;
        padding: 14px 40px;
        border-radius: 10px;
        font-weight: 500;
        color: #fff;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.05rem;
    }

    .btn-custom-primary:hover {
        background: #2e59d9;
        transform: translateY(-3px);
        box-shadow: 0 7px 20px rgba(78, 115, 223, 0.4);
    }

    .btn-custom-secondary {
        background: #858796;
        border: none;
        padding: 14px 40px;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.05rem;
    }

    .btn-custom-secondary:hover {
        background: #717384;
        transform: translateY(-3px);
        box-shadow: 0 7px 20px rgba(133, 135, 150, 0.4);
    }

    .page-header {
        background: linear-gradient(45deg, #4e73df, #224abe);
        padding: 50px 0;
        border-radius: 15px;
        text-align: center;
        margin-bottom: 50px;
        color: #fff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    /* Phone Input Styling */
    .iti {
        width: 100%;
    }
    .iti__flag {
        background-image: url("https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/img/flags.png");
    }
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .iti__flag {
            background-image: url("https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/img/flags@2x.png");
        }
    }
    .iti__selected-flag {
        border-radius: 10px 0 0 10px;
        background-color: #f8f9fc;
    }
    .phone-input-container .form-control {
        padding-left: 90px !important;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <!-- Header -->
            <div class="page-header">
                <h1>Register as an Applicant</h1>
                <p>Complete the form below to submit your application</p>
            </div>

            <div class="card custom-card">
                <div class="card-body p-5">
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('license.applicants.store') }}" method="POST" novalidate>
                        @csrf

                        <!-- Personal Information -->
                        <h5 class="section-title">Personal Information</h5>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">First Name <span class="required-mark">*</span></label>
                                <input type="text" name="first_name" 
                                    class="form-control @error('first_name') is-invalid @enderror"
                                    value="{{ old('first_name') }}">
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name <span class="required-mark">*</span></label>
                                <input type="text" name="last_name" 
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    value="{{ old('last_name') }}">
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
                                <input type="text" name="company_name" 
                                    class="form-control @error('company_name') is-invalid @enderror"
                                    value="{{ old('company_name') }}">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Registration Number <span class="required-mark">*</span></label>
                                <input type="text" name="local_registration_number" 
                                    class="form-control @error('local_registration_number') is-invalid @enderror"
                                    value="{{ old('local_registration_number') }}">
                                @error('local_registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Company Type <span class="required-mark">*</span></label>
                                <select name="types_of_company" 
                                    class="form-select @error('types_of_company') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    <option value="Corporation" {{ old('types_of_company') == 'Corporation' ? 'selected' : '' }}>Corporation</option>
                                    <option value="Partnership" {{ old('types_of_company') == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                    <option value="Single Private Company" {{ old('types_of_company') == 'Single Private Company' ? 'selected' : '' }}>Single Private Company</option>
                                </select>
                                @error('types_of_company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date Established <span class="required-mark">*</span></label>
                                <input type="date" name="date_of_establishment" 
                                    class="form-control @error('date_of_establishment') is-invalid @enderror"
                                    value="{{ old('date_of_establishment') }}">
                                @error('date_of_establishment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Citizenship <span class="required-mark">*</span></label>
                                <input type="text" name="citizenship" 
                                    class="form-control @error('citizenship') is-invalid @enderror"
                                    value="{{ old('citizenship') }}">
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
                                <input type="text" name="work_address" 
                                    class="form-control @error('work_address') is-invalid @enderror"
                                    value="{{ old('work_address') }}">
                                @error('work_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Registered Address <span class="required-mark">*</span></label>
                                <input type="text" name="registered_address" 
                                    class="form-control @error('registered_address') is-invalid @enderror"
                                    value="{{ old('registered_address') }}">
                                @error('registered_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Foreign Investment License</label>
                                <input type="text" name="foreign_investment_license" 
                                    class="form-control @error('foreign_investment_license') is-invalid @enderror"
                                    value="{{ old('foreign_investment_license') }}">
                                @error('foreign_investment_license')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number <span class="required-mark">*</span></label>
                                <div class="phone-input-container">
                                    <input type="tel" id="phone" name="phone_number" 
                                        class="form-control @error('phone_number') is-invalid @enderror"
                                        value="{{ old('phone_number') }}">
                                </div>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="required-mark">*</span></label>
                                <input type="email" name="email" 
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Math Captcha -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                @php
                                    $num1 = rand(1, 9);
                                    $num2 = rand(1, 9);
                                    session(['captcha_result' => $num1 + $num2]);
                                @endphp
                                <label class="form-label">Security Check: What is {{ $num1 }} + {{ $num2 }}? <span class="required-mark">*</span></label>
                                <input type="number" name="captcha_answer" 
                                    class="form-control @error('captcha_answer') is-invalid @enderror">
                                @error('captcha_answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-custom-primary">Submit Application</button>
                            <a href="{{ route('applicant.applicants.index') }}" class="btn btn-custom-secondary ms-3">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize phone input
        var phoneInput = document.querySelector("#phone");
        var iti = window.intlTelInput(phoneInput, {
            initialCountry: "ki",
            preferredCountries: ['ki', 'au', 'nz', 'fj'],
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                return "e.g. " + selectedCountryPlaceholder;
            }
        });

        // Validation on input
        phoneInput.addEventListener('blur', function() {
            if (phoneInput.value.trim()) {
                if (iti.isValidNumber()) {
                    phoneInput.classList.remove("is-invalid");
                    phoneInput.classList.add("is-valid");
                } else {
                    phoneInput.classList.remove("is-valid");
                    phoneInput.classList.add("is-invalid");
                }
            }
        });

        // Update hidden input before form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            var fullNumber = iti.getNumber();
            phoneInput.value = fullNumber;
        });

        // Reset validation when country changes
        phoneInput.addEventListener('countrychange', function() {
            phoneInput.value = '';
            phoneInput.classList.remove('is-valid', 'is-invalid');
        });
    });
</script>
@endpush
