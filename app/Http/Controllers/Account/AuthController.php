<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('account.auth.login');
    }

    public function logout()
    {
        auth()->logout();
        session()->regenerateToken();

        return redirect()->route('account.login');
    }

    public function processLogin(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $key = 'login-attempt:' . $request->ip();
        $attemptsPerHour = 5;

        if (\RateLimiter::tooManyAttempts($key, $attemptsPerHour)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Too many login attempts. Please try again later.',
                ]);
        }

        if (auth()->attempt($credentials, $request->boolean('remember'))) {
            session()->regenerate();

            \RateLimiter::clear($key);

            return redirect()->intended('/account');
        }

        \RateLimiter::increment($key, 3600);

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
    }
}
