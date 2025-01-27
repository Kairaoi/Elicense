<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        // Validate the login request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt to log the user in
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Check if the user has the "Applicant" role
            if (!$user->hasRole('Applicant')) {
                Auth::logout(); // Log the user out if they don't have the role
                return redirect()
                    ->route('login')
                    ->with('error', 'Only applicants can log in.');
            }

            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            // Redirect to the intended dashboard
            return redirect()->intended('applicant/dashboard');
        }

        // Return back with errors if authentication fails
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
