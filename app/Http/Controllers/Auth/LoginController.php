<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\UserLoginLog;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // Override the authenticated method from AuthenticatesUsers trait
    protected function authenticated(Request $request, $user)
    {
        // Log successful login
        UserLoginLog::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_at' => now(),
            'status' => 'success'
        ]);

        return redirect()->intended($this->redirectPath());
    }

    // Override the logout method from AuthenticatesUsers trait
    public function logout(Request $request)
    {
        // Find and update the latest login record
        $loginLog = UserLoginLog::where('user_id', auth()->id())
            ->whereNull('logout_at')
            ->latest()
            ->first();

        if ($loginLog) {
            $loginLog->update([
                'logout_at' => now()
            ]);
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->loggedOut($request) ?: redirect('/');
    }

    // Override the failed login attempt method
    protected function sendFailedLoginResponse(Request $request)
    {
        // Log failed login attempt
        UserLoginLog::create([
            'user_id' => null, // Since login failed, we might not have a user ID
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_at' => now(),
            'status' => 'failed',
            'notes' => 'Failed login attempt for email: ' . $request->email
        ]);

        return redirect()
            ->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => [trans('auth.failed')],
            ]);
    }

    
}