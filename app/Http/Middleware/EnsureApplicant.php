<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureApplicant
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated and has the 'Applicant' role
        if (Auth::check() && Auth::user()->hasRole('Applicant')) {
            return $next($request);
        }

        // Redirect to login or unauthorized page
        return redirect()->route('login')->with('error', 'Access denied for non-applicants.');
    }
}
