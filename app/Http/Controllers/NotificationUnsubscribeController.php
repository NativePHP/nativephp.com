<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationUnsubscribeController extends Controller
{
    public function unsubscribe(Request $request, User $user): RedirectResponse|View
    {
        $user->update(['receives_new_plugin_notifications' => false]);

        if ($request->user()?->is($user)) {
            return redirect()
                ->route('customer.settings', ['tab' => 'notifications'])
                ->with('new-plugin-notifications-disabled', true);
        }

        return view('notifications.unsubscribed', [
            'maskedEmail' => $this->maskEmail($user->email),
            'resubscribeUrl' => $this->signedResubscribeUrl($user),
        ]);
    }

    public function resubscribe(Request $request, User $user): RedirectResponse|View
    {
        $user->update(['receives_new_plugin_notifications' => true]);

        if ($request->user()?->is($user)) {
            return redirect()
                ->route('customer.settings', ['tab' => 'notifications'])
                ->with('new-plugin-notifications-enabled', true);
        }

        return view('notifications.resubscribed', [
            'maskedEmail' => $this->maskEmail($user->email),
        ]);
    }

    public static function signedUnsubscribeUrl(User $user): string
    {
        return url()->signedRoute('notifications.unsubscribe', ['user' => $user]);
    }

    private function signedResubscribeUrl(User $user): string
    {
        return url()->signedRoute('notifications.resubscribe', ['user' => $user]);
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email);

        if (strlen($local) <= 2) {
            $maskedLocal = $local[0].str_repeat('*', max(1, strlen($local) - 1));
        } else {
            $maskedLocal = $local[0].str_repeat('*', strlen($local) - 2).$local[strlen($local) - 1];
        }

        return $maskedLocal.'@'.$domain;
    }
}
