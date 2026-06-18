<?php

namespace App\Http\Controllers\Auth;

use App\Enums\TeamUserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Plugin;
use App\Models\TeamUser;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function showLogin(): View
    {
        return view('auth.login');
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

        event(new Registered($user));

        Auth::login($user);

        // Transfer guest cart to user
        $this->cartService->transferGuestCartToUser($user);

        // Check for pending team invitation
        $this->acceptPendingTeamInvitation($user);

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

        // Check for pending team invitation
        $this->acceptPendingTeamInvitation($user);

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

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === PasswordBroker::RESET_LINK_SENT
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

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password): void {
                $attributes = ['password' => $password];

                // Proving control of the inbox + setting a password is sufficient
                // to consider the email verified. This also lets the same flow
                // serve as the "claim your account" path for users created via
                // checkout.
                if (! $user->email_verified_at) {
                    $attributes['email_verified_at'] = now();
                }

                $user->forceFill($attributes);

                $user->save();
            }
        );

        return $status === PasswordBroker::PASSWORD_RESET
            ? to_route('customer.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    private function acceptPendingTeamInvitation(User $user): void
    {
        $token = session()->pull('pending_team_invitation_token');

        if (! $token) {
            return;
        }

        $teamUser = TeamUser::where('invitation_token', $token)
            ->where('email', $user->email)
            ->where('status', TeamUserStatus::Pending)
            ->first();

        if ($teamUser) {
            $teamUser->accept($user);
            session()->flash('success', "You've joined {$teamUser->team->name}!");
        }
    }
}
