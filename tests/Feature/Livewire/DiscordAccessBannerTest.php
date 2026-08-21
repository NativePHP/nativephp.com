<?php

namespace Tests\Feature\Livewire;

use App\Livewire\DiscordAccessBanner;
use App\Models\License;
use App\Models\Product;
use App\Models\ProductLicense;
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
            'services.discord.master_role_id' => 'master-role-id',
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
    public function it_shows_master_role_active_when_user_has_master_role(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        $this->fakeDiscordApi(isGuildMember: true, roles: ['master-role-id']);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertSee('Master Role Active');

        $this->assertNotNull($user->fresh()->discord_master_role_granted_at);
    }

    #[Test]
    public function it_shows_master_eligible_for_masterclass_owner_without_role(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        $this->grantMasterclass($user);

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertSee('Master Eligible');
    }

    #[Test]
    public function it_shows_request_master_role_button_for_masterclass_owner(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        $this->grantMasterclass($user);

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertSee('Request Master Role');
    }

    #[Test]
    public function it_does_not_show_request_master_role_button_without_the_masterclass(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->assertDontSee('Request Master Role')
            ->assertDontSee('Master Eligible');
    }

    #[Test]
    public function it_assigns_master_role_on_request(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        $this->grantMasterclass($user);

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->call('requestMasterRole')
            ->assertHasNoErrors();

        $this->assertNotNull($user->fresh()->discord_master_role_granted_at);

        Http::assertSent(fn ($request) => $request->method() === 'PUT'
            && str_contains($request->url(), '/members/123456789/roles/master-role-id'));
    }

    #[Test]
    public function it_does_not_assign_master_role_without_the_masterclass(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        $this->fakeDiscordApi(isGuildMember: true);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->call('requestMasterRole');

        $this->assertNull($user->fresh()->discord_master_role_granted_at);
    }

    #[Test]
    public function it_does_not_assign_master_role_when_not_guild_member(): void
    {
        $user = User::factory()->create([
            'discord_id' => '123456789',
            'discord_username' => 'testuser',
        ]);

        $this->grantMasterclass($user);

        $this->fakeDiscordApi(isGuildMember: false);

        Livewire::actingAs($user)
            ->test(DiscordAccessBanner::class)
            ->call('requestMasterRole');

        $this->assertNull($user->fresh()->discord_master_role_granted_at);
    }

    private function grantMasterclass(User $user): void
    {
        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => Product::where('slug', 'nativephp-masterclass')->firstOrFail()->id,
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
