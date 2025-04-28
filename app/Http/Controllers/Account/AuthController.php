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

        if (auth()->attempt($credentials, $request->boolean('remember'))) {
            session()->regenerate();

            return redirect()->intended('/account');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
    }
}
