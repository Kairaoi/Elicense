@extends('layouts.app')

@section('content')
<style>
  

  .card {
        max-width: 400px; /* Set a maximum width for the form */
        margin: auto; /* Center the card */
    }

    .card-header {
        border-radius: 30px 30px 0 0; /* Rounded top corners */
        padding: 35px; /* Increased padding */
        text-transform: uppercase; /* Uppercase text for style */
        letter-spacing: 3px; /* Increased letter spacing */
        background-color: #008f9b; /* Darker shade for contrast */
        color: #ffffff; /* White text */
        font-family: 'Courier New', Courier, monospace; /* Monospace font for uniqueness */
    }
    .vh-100 {
        min-height: calc(100vh - 56px); /* Account for header/footer */
    }

    .form-label {
        font-weight: bold;
        color: #005f73; /* Change the label color */
        font-family: 'Georgia', serif; /* Serif font for a different style */
    }

    .form-control {
        border-radius: 20px; /* Increased rounding */
        border: 1px solid #005f73; /* Input border color */
        transition: border-color 0.3s ease, box-shadow 0.3s ease; /* Added transition for shadow */
        padding: 20px; /* Increased padding */
        background-color: #f1f1f1; /* Light input background */
    }

    .form-control:focus {
        border-color: #008f9b; /* Focus border color */
        box-shadow: 0 0 15px rgba(0, 143, 155, 0.5); /* More pronounced shadow effect */
        background-color: #ffffff; /* White background on focus */
    }

    .btn-primary {
        background-color: #006d77; /* Consistent button color */
        border: none;
        border-radius: 20px; /* Rounded button */
        transition: background-color 0.3s, transform 0.3s;
        padding: 20px; /* Increase button padding */
        font-family: 'Courier New', Courier, monospace; /* Monospace font for uniqueness */
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); /* Button shadow */
    }

    .btn-primary:hover {
        background-color: #008f9b;
        transform: translateY(-4px); /* Raise button higher on hover */
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3); /* Enhanced shadow on hover */
    }

    .btn-link {
        color: #005f73;
        font-weight: bold;
        text-decoration: underline; /* Underlined text for links */
        font-family: 'Georgia', serif; /* Serif font for a different style */
    }

    .btn-link:hover {
        color: #008f9b;
        text-decoration: none; /* Remove underline on hover */
    }

    /* Additional styles for spacing */
    .mb-4 {
        margin-bottom: 2rem; /* Increased margin */
    }

    .d-grid {
        margin-top: 1rem; /* Added margin to the button area */
    }
</style>

<div class="container">
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="col-md-6 col-lg-4"> <!-- Adjust the column width for smaller form -->
            <div class="card shadow-lg border-0 rounded">
                <div class="card-header text-center">
                    <h2 class="mb-0">{{ __('Login to Coastal Fisheries') }}</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <!-- Email Input -->
                        <div class="mb-4">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input id="email" type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" 
                                   required autocomplete="email" autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                        <!-- Password Input -->
                        <div class="mb-4">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                        <!-- Remember Me Checkbox -->
                        <div class="mb-4 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                        </div>
                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg shadow">{{ __('Login') }}</button>
                        </div>
                        <!-- Forgot Password Link -->
                        @if (Route::has('password.request'))
                            <div class="text-center">
                                <a class="btn btn-link" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
