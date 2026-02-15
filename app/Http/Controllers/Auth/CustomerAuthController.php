<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Plugin;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function showLogin(): View
    {
        return view(\Illuminate\Auth\Events\Login::class);
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        // Transfer guest cart to user
        $this->cartService->transferGuestCartToUser($user);

        // Check for pending add-to-cart action
        $pendingPluginId = session()->pull('pending_add_to_cart');
        if ($pendingPluginId) {
            $plugin = Plugin::find($pendingPluginId);
            if ($plugin && $plugin->isPaid() && $plugin->activePrice) {
                $cart = $this->cartService->getCart($user);
                try {
                    $this->cartService->addPlugin($cart, $plugin);

                    return to_route('cart.show')
                        ->with('success', "{$plugin->name} has been added to your cart!");
                } catch (\Exception $e) {
                    // Plugin couldn't be added, continue to normal flow
                }
            }
        }

        return redirect()->intended(route('dashboard'));
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Transfer guest cart to user
        $this->cartService->transferGuestCartToUser($user);

        // Check for pending add-to-cart action
        $pendingPluginId = session()->pull('pending_add_to_cart');
        if ($pendingPluginId) {
            $plugin = Plugin::find($pendingPluginId);
            if ($plugin && $plugin->isPaid() && $plugin->activePrice) {
                $cart = $this->cartService->getCart($user);
                try {
                    $this->cartService->addPlugin($cart, $plugin);

                    return to_route('cart.show')
                        ->with('success', "{$plugin->name} has been added to your cart!");
                } catch (\Exception $e) {
                    // Plugin couldn't be added, continue to normal flow
                }
            }
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('customer.login');
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email:rfc,dns'],
        ]);

        $status = \Illuminate\Support\Facades\Password::sendResetLink(
            $request->only('email')
        );

        return $status === \Illuminate\Auth\Passwords\PasswordBroker::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email:rfc,dns'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password): void {
                $user->forceFill([
                    'password' => $password,
                ]);

                $user->save();
            }
        );

        return $status === \Illuminate\Auth\Passwords\PasswordBroker::PASSWORD_RESET
            ? to_route('customer.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
