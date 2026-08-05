<?php

namespace Tests\Feature;

use App\Livewire\DiscordAccessBanner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Cashier\Subscription as CashierSubscription;
use Livewire\Livewire;
use Tests\TestCase;

class DiscordUltraAccessTest extends TestCase
{
    use RefreshDatabase;

    private const MAX_PRICE_ID = 'price_test_max_yearly';

    private const GUILD_ID = 'test_guild';

    private const ULTRA_ROLE_ID = 'test_ultra_role';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'subscriptions.plans.max.stripe_price_id' => self::MAX_PRICE_ID,
            'services.discord.bot_token' => 'test_token',
            'services.discord.guild_id' => self::GUILD_ID,
            'services.discord.ultra_role_id' => self::ULTRA_ROLE_ID,
        ]);

        Cache::flush();
    }

    private function ultraSubscriber(array $userAttributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'stripe_id' => 'cus_'.uniqid(),
        ], $userAttributes));

        CashierSubscription::factory()->for($user)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
        ]);

        return $user;
    }

    public function test_ultra_subscriber_sees_discord_access_banner(): void
    {
        Http::fake([
            'discord.com/api/v10/guilds/*/members/*' => Http::response([], 404),
        ]);

        $user = $this->ultraSubscriber();

        $response = $this->actingAs($user)->get('/dashboard/integrations');

        $response->assertStatus(200);
        $response->assertSeeLivewire('discord-access-banner');
    }

    public function test_user_without_max_or_ultra_does_not_see_discord_banner(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard/integrations');

        $response->assertStatus(200);
        $response->assertDontSeeLivewire('discord-access-banner');
    }

    public function test_ultra_subscriber_can_request_ultra_role(): void
    {
        Http::fake([
            'discord.com/api/v10/guilds/*/members/discord-123/roles/*' => Http::response([], 204),
            'discord.com/api/v10/guilds/*/members/discord-123' => Http::response(['roles' => []], 200),
        ]);

        $user = $this->ultraSubscriber([
            'discord_id' => 'discord-123',
            'discord_username' => 'ultra-user',
        ]);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->call('requestUltraRole');

        $this->assertNotNull($user->fresh()->discord_role_granted_at);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/members/discord-123/roles/'.self::ULTRA_ROLE_ID)
            && $request->method() === 'PUT');
    }

    public function test_request_ultra_role_rejected_without_max_or_ultra(): void
    {
        Http::fake();

        $user = User::factory()->create([
            'discord_id' => 'discord-999',
            'discord_username' => 'free-user',
        ]);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->call('requestUltraRole');

        $this->assertNull($user->fresh()->discord_role_granted_at);
        Http::assertNotSent(fn ($request) => $request->method() === 'PUT'
            && str_contains($request->url(), '/roles/'.self::ULTRA_ROLE_ID));
    }

    public function test_cleanup_command_retains_role_for_ultra_subscriber(): void
    {
        $user = $this->ultraSubscriber([
            'discord_id' => 'discord-456',
            'discord_username' => 'ultra-user',
            'discord_role_granted_at' => now()->subDays(10),
        ]);

        $this->artisan('discord:remove-expired-roles')
            ->assertExitCode(0);

        $this->assertNotNull($user->fresh()->discord_role_granted_at);
    }

    public function test_cleanup_command_removes_role_when_no_access(): void
    {
        Http::fake([
            'discord.com/api/v10/guilds/*/members/*/roles/*' => Http::response([], 204),
        ]);

        $user = User::factory()->create([
            'discord_id' => 'discord-789',
            'discord_username' => 'former-user',
            'discord_role_granted_at' => now()->subDays(10),
        ]);

        $this->artisan('discord:remove-expired-roles')
            ->assertExitCode(0);

        $this->assertNull($user->fresh()->discord_role_granted_at);
    }
}
