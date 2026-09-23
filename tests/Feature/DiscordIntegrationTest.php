<?php

namespace Tests\Feature;

use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DiscordIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.discord.client_id' => 'test-client-id',
            'services.discord.client_secret' => 'test-client-secret',
            'services.discord.bot_token' => 'test-bot-token',
            'services.discord.guild_id' => 'test-guild-id',
            'services.discord.ultra_role_id' => 'ultra-role-id',
            'services.discord.early_adopter_role_id' => 'early-adopter-role-id',
        ]);
    }

    #[Test]
    public function callback_assigns_early_adopter_role_for_eap_customer(): void
    {
        $user = User::factory()->create();

        License::factory()
            ->for($user)
            ->eapEligible()
            ->active()
            ->withoutSubscriptionItem()
            ->create();

        Http::fake([
            'discord.com/api/oauth2/token' => Http::response([
                'access_token' => 'test-access-token',
            ]),
            'discord.com/api/v10/users/@me' => Http::response([
                'id' => '999888777',
                'username' => 'eapuser',
            ]),
            'discord.com/api/v10/guilds/test-guild-id/members/999888777' => Http::response([
                'roles' => [],
            ], 200),
            'discord.com/api/v10/guilds/test-guild-id/members/999888777/roles/early-adopter-role-id' => Http::response([], 204),
        ]);

        $response = $this->actingAs($user)
            ->get('/auth/discord/callback?code=test-code');

        $response->assertRedirect(route('customer.integrations'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('999888777', $user->discord_id);
        $this->assertEquals('eapuser', $user->discord_username);
        $this->assertNotNull($user->discord_early_adopter_role_granted_at);
    }

    #[Test]
    public function callback_assigns_both_roles_for_eap_customer_with_max_access(): void
    {
        $user = User::factory()->create();

        License::factory()
            ->for($user)
            ->max()
            ->eapEligible()
            ->active()
            ->withoutSubscriptionItem()
            ->create();

        Http::fake([
            'discord.com/api/oauth2/token' => Http::response([
                'access_token' => 'test-access-token',
            ]),
            'discord.com/api/v10/users/@me' => Http::response([
                'id' => '999888777',
                'username' => 'maxeapuser',
            ]),
            'discord.com/api/v10/guilds/test-guild-id/members/999888777' => Http::response([
                'roles' => [],
            ], 200),
            'discord.com/api/v10/guilds/test-guild-id/members/999888777/roles/*' => Http::response([], 204),
        ]);

        $response = $this->actingAs($user)
            ->get('/auth/discord/callback?code=test-code');

        $response->assertRedirect(route('customer.integrations'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNotNull($user->discord_role_granted_at);
        $this->assertNotNull($user->discord_early_adopter_role_granted_at);
    }

    #[Test]
    public function callback_does_not_assign_early_adopter_role_for_non_eap_customer(): void
    {
        $user = User::factory()->create();

        License::factory()
            ->for($user)
            ->afterEap()
            ->active()
            ->withoutSubscriptionItem()
            ->create();

        Http::fake([
            'discord.com/api/oauth2/token' => Http::response([
                'access_token' => 'test-access-token',
            ]),
            'discord.com/api/v10/users/@me' => Http::response([
                'id' => '999888777',
                'username' => 'newuser',
            ]),
            'discord.com/api/v10/guilds/test-guild-id/members/999888777' => Http::response([
                'roles' => [],
            ], 200),
        ]);

        $response = $this->actingAs($user)
            ->get('/auth/discord/callback?code=test-code');

        $response->assertRedirect(route('customer.integrations'));

        $user->refresh();
        $this->assertNull($user->discord_early_adopter_role_granted_at);
    }

    #[Test]
    public function disconnect_removes_early_adopter_role(): void
    {
        $user = User::factory()->create([
            'discord_id' => '999888777',
            'discord_username' => 'testuser',
            'discord_role_granted_at' => now(),
            'discord_early_adopter_role_granted_at' => now(),
        ]);

        Http::fake([
            'discord.com/api/v10/guilds/test-guild-id/members/999888777/roles/*' => Http::response([], 204),
        ]);

        $response = $this->actingAs($user)
            ->delete('/dashboard/discord/disconnect');

        $response->assertSessionHas('success', 'Discord account disconnected successfully.');

        $user->refresh();
        $this->assertNull($user->discord_id);
        $this->assertNull($user->discord_username);
        $this->assertNull($user->discord_role_granted_at);
        $this->assertNull($user->discord_early_adopter_role_granted_at);
    }

    #[Test]
    public function disconnect_only_removes_early_adopter_role_when_max_role_was_not_granted(): void
    {
        $user = User::factory()->create([
            'discord_id' => '999888777',
            'discord_username' => 'testuser',
            'discord_role_granted_at' => null,
            'discord_early_adopter_role_granted_at' => now(),
        ]);

        Http::fake([
            'discord.com/api/v10/guilds/test-guild-id/members/999888777/roles/early-adopter-role-id' => Http::response([], 204),
        ]);

        $response = $this->actingAs($user)
            ->delete('/dashboard/discord/disconnect');

        $response->assertSessionHas('success', 'Discord account disconnected successfully.');

        $user->refresh();
        $this->assertNull($user->discord_id);
        $this->assertNull($user->discord_early_adopter_role_granted_at);

        Http::assertSentCount(1);
    }
}
