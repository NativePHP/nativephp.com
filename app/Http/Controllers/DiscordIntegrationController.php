<?php

namespace App\Http\Controllers;

use App\Support\DiscordApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordIntegrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function redirectToDiscord(): RedirectResponse
    {
        $params = http_build_query([
            'client_id' => config('services.discord.client_id'),
            'redirect_uri' => config('services.discord.redirect'),
            'response_type' => 'code',
            'scope' => 'identify',
        ]);

        return redirect('https://discord.com/api/oauth2/authorize?'.$params);
    }

    public function handleCallback(): RedirectResponse
    {
        $code = request('code');

        if (! $code) {
            return to_route('customer.integrations')
                ->with('error', 'Discord authorization was cancelled.');
        }

        try {
            $tokenResponse = Http::asForm()->post('https://discord.com/api/oauth2/token', [
                'client_id' => config('services.discord.client_id'),
                'client_secret' => config('services.discord.client_secret'),
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('services.discord.redirect'),
            ]);

            if ($tokenResponse->failed()) {
                throw new \Exception('Failed to exchange code for token');
            }

            $accessToken = $tokenResponse->json('access_token');

            $userResponse = Http::withToken($accessToken)
                ->get('https://discord.com/api/v10/users/@me');

            if ($userResponse->failed()) {
                throw new \Exception('Failed to fetch Discord user');
            }

            $discordUser = $userResponse->json();

            $user = Auth::user();
            $user->update([
                'discord_id' => $discordUser['id'],
                'discord_username' => $discordUser['username'],
            ]);

            $discord = DiscordApi::make();

            if (! $discord->isGuildMember($discordUser['id'])) {
                return to_route('customer.integrations')
                    ->with('warning', 'Discord account connected! Please join the NativePHP Discord server to receive the Max role.');
            }

            if ($user->hasMaxAccess()) {
                $success = $discord->assignMaxRole($discordUser['id']);

                if ($success) {
                    $user->update([
                        'discord_role_granted_at' => now(),
                    ]);

                    return to_route('customer.integrations')
                        ->with('success', 'Discord account connected and Max role assigned!');
                }

                return to_route('customer.integrations')
                    ->with('warning', 'Discord account connected, but we could not assign the Max role. Please try again later.');
            }

            return to_route('customer.integrations')
                ->with('success', 'Discord account connected successfully!');
        } catch (\Exception $e) {
            Log::error('Discord OAuth callback failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return to_route('customer.integrations')
                ->with('error', 'Failed to connect Discord account. Please try again.');
        }
    }

    public function disconnect(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->discord_role_granted_at && $user->discord_id) {
            $discord = DiscordApi::make();
            $discord->removeMaxRole($user->discord_id);
        }

        $user->update([
            'discord_id' => null,
            'discord_username' => null,
            'discord_role_granted_at' => null,
        ]);

        return back()->with('success', 'Discord account disconnected successfully.');
    }
}
