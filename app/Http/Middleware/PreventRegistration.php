<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventRegistration
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Block te registration routes ao methods
        if ($this->isRegistrationAttempt($request)) {
            return $this->handleBlockedAccess($request);
        }

        return $next($request);
    }

    /**
     * Check if this is a registration attempt
     */
    private function isRegistrationAttempt(Request $request): bool
    {
        $registrationPatterns = [
            '/register',
            'auth/register',
            'signup',
            '/new-user',
        ];

        // Check URL pattern
        foreach ($registrationPatterns as $pattern) {
            if ($request->is($pattern) || $request->is("*$pattern*")) {
                return true;
            }
        }

        // Check te POST data for registration indicators
        if ($request->isMethod('post')) {
            $registrationIndicators = ['register', 'signup', 'new_user', 'create_account'];
            foreach ($registrationIndicators as $indicator) {
                if ($request->has($indicator)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Handle blocked access attempts
     */
    private function handleBlockedAccess(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Registration is disabled.',
                'status' => 'error'
            ], 403);
        }

        abort(403, 'Registration is disabled.');
    }
}