<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

    /**
     * Process the login request.
     *
     * @TODO Implement additional brute-force protection with custom blocked IPs model.
     *
     * @param LoginRequest $request
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processLogin(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $key = 'login-attempt:' . $request->ip();
        $attemptsPerHour = 5;

        if (\RateLimiter::tooManyAttempts($key, $attemptsPerHour)) {
            $blockedUntil = Carbon::now()
                ->addSeconds(\RateLimiter::availableIn($key))
                ->diffInMinutes(Carbon::now());

            return back()
                ->withInput($request->only(['email', 'remember']))
                ->withErrors([
                    'email' => 'Too many login attempts. Please try again in '
                        . $blockedUntil . ' minutes.',
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
