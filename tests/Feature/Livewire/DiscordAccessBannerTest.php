<?php

namespace Tests\Feature\Livewire;

use App\Enums\PluginActivityType;
use App\Enums\PluginStatus;
use App\Livewire\DiscordAccessBanner;
use App\Models\License;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DiscordAccessBannerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.discord.bot_token' => 'test-bot-token',
            'services.discord.guild_id' => 'test-guild-id',
            'services.discord.ultra_role_id' => 'ultra-role-id',
            'services.discord.early_adopter_role_id' => 'early-adopter-role-id',
            'services.discord.plugin_developer_role_id' => 'plugin-developer-role-id',
        ]);
    }

    #[Test]
    public function it_shows_not_connected_state_when_discord_is_not_linked(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertSee('Connect your Discord account to receive your roles.');
    }

    #[Test]
    public function it_shows_connected_username_when_discord_is_linked(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertSee('testuser');
    }

    #[Test]
    public function it_shows_not_in_server_when_user_is_not_a_guild_member(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        $this->fakeDiscordApi(isGuildMember: false);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertSee('Not in Server');
    }

    #[Test]
    public function it_shows_ultra_role_active_when_user_has_ultra_role(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        $this->fakeDiscordApi(isGuildMember: true, roles: ['ultra-role-id']);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertSee('Ultra Role Active');
    }

    #[Test]
    public function it_shows_early_adopter_active_when_user_has_early_adopter_role(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        $this->fakeDiscordApi(isGuildMember: true, roles: ['early-adopter-role-id']);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertSee('Early Adopter Active');
    }

    #[Test]
    public function it_shows_early_adopter_eligible_for_eap_customer_without_role(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        License::factory()
            ->for($user)
            ->eapEligible()
            ->active()
            ->withoutSubscriptionItem()
            ->create();

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertSee('Early Adopter Eligible');
    }

    #[Test]
    public function it_shows_request_early_adopter_role_button_for_eligible_user(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        License::factory()
            ->for($user)
            ->eapEligible()
            ->active()
            ->withoutSubscriptionItem()
            ->create();

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertSee('Request Early Adopter Role');
    }

    #[Test]
    public function it_assigns_early_adopter_role_on_request(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        License::factory()
            ->for($user)
            ->eapEligible()
            ->active()
            ->withoutSubscriptionItem()
            ->create();

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->call('requestEarlyAdopterRole')
            ->assertHasNoErrors();

        $this->assertNotNull($user->fresh()->discord_early_adopter_role_granted_at);
    }

    #[Test]
    public function it_does_not_assign_early_adopter_role_for_non_eap_customer(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        // License created after EAP cutoff
        License::factory()
            ->for($user)
            ->afterEap()
            ->active()
            ->withoutSubscriptionItem()
            ->create();

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->call('requestEarlyAdopterRole');

        $this->assertNull($user->fresh()->discord_early_adopter_role_granted_at);
    }

    #[Test]
    public function it_does_not_assign_early_adopter_role_when_not_guild_member(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        License::factory()
            ->for($user)
            ->eapEligible()
            ->active()
            ->withoutSubscriptionItem()
            ->create();

        $this->fakeDiscordApi(isGuildMember: false);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->call('requestEarlyAdopterRole');

        $this->assertNull($user->fresh()->discord_early_adopter_role_granted_at);
    }

    #[Test]
    public function it_shows_both_roles_active_when_user_has_both(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        $this->fakeDiscordApi(isGuildMember: true, roles: ['ultra-role-id', 'early-adopter-role-id']);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertSee('Ultra Role Active')
            ->assertSee('Early Adopter Active');
    }

    #[Test]
    public function it_shows_plugin_developer_eligible_for_developer_with_approved_plugin(): void
    {
        $user = $this->connectedUser();

        Plugin::factory()->for($user)->approved()->create();

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertSee('Plugin Developer Eligible')
            ->assertSee('Claim Plugin Developer Role');
    }

    #[Test]
    public function it_does_not_offer_plugin_developer_role_without_an_approved_plugin(): void
    {
        $user = $this->connectedUser();

        Plugin::factory()->for($user)->pending()->create();
        Plugin::factory()->approved()->create();

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertDontSee('Plugin Developer Eligible')
            ->assertDontSee('Claim Plugin Developer Role');
    }

    #[Test]
    public function it_shows_plugin_developer_active_when_user_has_the_role(): void
    {
        $user = $this->connectedUser();

        $this->fakeDiscordApi(isGuildMember: true, roles: ['plugin-developer-role-id']);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertSee('Plugin Developer Active')
            ->assertDontSee('Claim Plugin Developer Role');

        $this->assertNotNull($user->fresh()->discord_plugin_developer_role_granted_at);
    }

    #[Test]
    public function it_assigns_plugin_developer_role_on_request(): void
    {
        $user = $this->connectedUser();

        Plugin::factory()->for($user)->approved()->create();

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->call('requestPluginDeveloperRole')
            ->assertHasNoErrors();

        $this->assertNotNull($user->fresh()->discord_plugin_developer_role_granted_at);

        Http::assertSent(fn ($request) => $request->method() === 'PUT'
            && str_ends_with($request->url(), '/members/123456789/roles/plugin-developer-role-id'));
    }

    #[Test]
    public function it_assigns_plugin_developer_role_for_an_inactive_approved_plugin(): void
    {
        $user = $this->connectedUser();

        Plugin::factory()->for($user)->approved()->inactive()->create();

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->call('requestPluginDeveloperRole');

        $this->assertNotNull($user->fresh()->discord_plugin_developer_role_granted_at);
    }

    #[Test]
    public function it_assigns_plugin_developer_role_when_the_only_approved_plugin_was_later_rejected(): void
    {
        $user = $this->connectedUser();
        $admin = User::factory()->create();

        $plugin = Plugin::factory()->for($user)->approved()->create();
        $plugin->activities()->create([
            'type' => PluginActivityType::Approved,
            'from_status' => PluginStatus::Pending->value,
            'to_status' => PluginStatus::Approved->value,
            'causer_id' => $admin->id,
        ]);
        $plugin->reject('No longer maintained', $admin->id);

        $this->assertTrue($plugin->fresh()->isRejected());

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertSee('Plugin Developer Eligible')
            ->call('requestPluginDeveloperRole');

        $this->assertNotNull($user->fresh()->discord_plugin_developer_role_granted_at);
    }

    #[Test]
    public function it_does_not_assign_plugin_developer_role_without_an_approved_plugin(): void
    {
        $user = $this->connectedUser();

        Plugin::factory()->for($user)->rejected()->create();
        Plugin::factory()->for($user)->draft()->create();

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->call('requestPluginDeveloperRole');

        $this->assertNull($user->fresh()->discord_plugin_developer_role_granted_at);

        Http::assertNotSent(fn ($request) => $request->method() === 'PUT');
    }

    #[Test]
    public function it_does_not_assign_plugin_developer_role_when_not_guild_member(): void
    {
        $user = $this->connectedUser();

        Plugin::factory()->for($user)->approved()->create();

        $this->fakeDiscordApi(isGuildMember: false);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->call('requestPluginDeveloperRole');

        $this->assertNull($user->fresh()->discord_plugin_developer_role_granted_at);
    }

    #[Test]
    public function it_does_not_assign_plugin_developer_role_when_discord_is_not_connected(): void
    {
        $user = User::factory()->create();

        Plugin::factory()->for($user)->approved()->create();

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->call('requestPluginDeveloperRole');

        $this->assertNull($user->fresh()->discord_plugin_developer_role_granted_at);

        Http::assertNothingSent();
    }

    private function connectedUser(): User
    {
        return User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);
    }

    /**
     * @param  array<string>  $roles
     */
    private function fakeDiscordApi(bool $isGuildMember, array $roles = []): void
    {
        Cache::flush();

        $memberResponse = $isGuildMember
            ? Http::response(['roles' => $roles], 200)
            : Http::response([], 404);

        Http::fake([
            'discord.com/api/v10/guilds/test-guild-id/members/*/roles/*' => Http::response([], 204),
            'discord.com/api/v10/guilds/test-guild-id/members/*' => $memberResponse,
        ]);
    }
}
