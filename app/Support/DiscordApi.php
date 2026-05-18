<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordApi
{
    private const BASE_URL = 'https://discord.com/api/v10';

    public function __construct(
        private ?string $botToken,
        private ?string $guildId,
        private ?string $ultraRoleId
    ) {}

    public static function make(): static
    {
        return new static(
            config('services.discord.bot_token', ''),
            config('services.discord.guild_id', ''),
            config('services.discord.ultra_role_id', '')
        );
    }

    public function isGuildMember(string $discordUserId): bool
    {
        $url = sprintf(
            '%s/guilds/%s/members/%s',
            self::BASE_URL,
            $this->guildId,
            $discordUserId
        );

        Log::debug('Checking Discord guild membership', [
            'url' => $url,
            'guild_id' => $this->guildId,
            'discord_user_id' => $discordUserId,
        ]);

        $response = Http::withToken($this->botToken, 'Bot')->get($url);

        if ($response->status() === 404) {
            Log::info('Discord user not found in guild', [
                'discord_user_id' => $discordUserId,
                'guild_id' => $this->guildId,
            ]);

            return false;
        }

        if ($response->failed()) {
            Log::error('Failed to check Discord guild membership', [
                'discord_user_id' => $discordUserId,
                'guild_id' => $this->guildId,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        }

        Log::info('Discord user is guild member', [
            'discord_user_id' => $discordUserId,
            'guild_id' => $this->guildId,
        ]);

        return true;
    }

    public function assignUltraRole(string $discordUserId): bool
    {
        $response = Http::withToken($this->botToken, 'Bot')
            ->put(sprintf(
                '%s/guilds/%s/members/%s/roles/%s',
                self::BASE_URL,
                $this->guildId,
                $discordUserId,
                $this->ultraRoleId
            ));

        if ($response->failed()) {
            Log::error('Failed to assign Discord Ultra role', [
                'discord_user_id' => $discordUserId,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        }

        return true;
    }

    public function removeUltraRole(string $discordUserId): bool
    {
        $response = Http::withToken($this->botToken, 'Bot')
            ->delete(sprintf(
                '%s/guilds/%s/members/%s/roles/%s',
                self::BASE_URL,
                $this->guildId,
                $discordUserId,
                $this->ultraRoleId
            ));

        if ($response->failed()) {
            Log::error('Failed to remove Discord Ultra role', [
                'discord_user_id' => $discordUserId,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        }

        return true;
    }

    public function hasUltraRole(string $discordUserId): bool
    {
        $response = Http::withToken($this->botToken, 'Bot')
            ->get(sprintf(
                '%s/guilds/%s/members/%s',
                self::BASE_URL,
                $this->guildId,
                $discordUserId
            ));

        if ($response->failed()) {
            Log::error('Failed to check Discord user roles', [
                'discord_user_id' => $discordUserId,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        }

        $member = $response->json();
        $roles = $member['roles'] ?? [];

        return in_array($this->ultraRoleId, $roles, true);
    }
}
