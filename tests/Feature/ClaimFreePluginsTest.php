<?php

namespace Tests\Feature;

use App\Models\License;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ClaimFreePluginsTest extends TestCase
{
    use RefreshDatabase;

    public function test_eligible_user_can_claim_free_plugins(): void
    {
        $this->travelTo(Carbon::parse('2026-03-15'));

        $user = User::factory()->create();
        License::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2025-12-01',
        ]);

        foreach (User::FREE_PLUGINS_OFFER as $name) {
            Plugin::factory()->approved()->create(['name' => $name]);
        }

        $response = $this->actingAs($user)
            ->post(route('customer.claim-free-plugins'));

        $response->assertRedirectToRoute('dashboard');
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('plugin_licenses', count(User::FREE_PLUGINS_OFFER));
    }

    public function test_claim_is_rejected_after_offer_expires(): void
    {
        $this->travelTo(Carbon::parse('2026-06-01'));

        $user = User::factory()->create();
        License::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2025-12-01',
        ]);

        foreach (User::FREE_PLUGINS_OFFER as $name) {
            Plugin::factory()->approved()->create(['name' => $name]);
        }

        $response = $this->actingAs($user)
            ->post(route('customer.claim-free-plugins'));

        $response->assertRedirectToRoute('dashboard');
        $response->assertSessionHas('error', 'This offer has expired.');

        $this->assertDatabaseCount('plugin_licenses', 0);
    }

    public function test_claim_is_allowed_on_last_day_of_offer(): void
    {
        $this->travelTo(Carbon::parse('2026-05-31 23:00:00'));

        $user = User::factory()->create();
        License::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2025-12-01',
        ]);

        foreach (User::FREE_PLUGINS_OFFER as $name) {
            Plugin::factory()->approved()->create(['name' => $name]);
        }

        $response = $this->actingAs($user)
            ->post(route('customer.claim-free-plugins'));

        $response->assertRedirectToRoute('dashboard');
        $response->assertSessionHas('success');
    }

    public function test_ineligible_user_cannot_claim_free_plugins(): void
    {
        $this->travelTo(Carbon::parse('2026-03-15'));

        $user = User::factory()->create();

        foreach (User::FREE_PLUGINS_OFFER as $name) {
            Plugin::factory()->approved()->create(['name' => $name]);
        }

        $response = $this->actingAs($user)
            ->post(route('customer.claim-free-plugins'));

        $response->assertRedirectToRoute('dashboard');
        $response->assertSessionHas('error', 'You are not eligible for this offer.');
    }

    public function test_user_cannot_claim_plugins_twice(): void
    {
        $this->travelTo(Carbon::parse('2026-03-15'));

        $user = User::factory()->create();
        License::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2025-12-01',
        ]);

        foreach (User::FREE_PLUGINS_OFFER as $name) {
            $plugin = Plugin::factory()->approved()->create(['name' => $name]);
            PluginLicense::factory()->create([
                'user_id' => $user->id,
                'plugin_id' => $plugin->id,
            ]);
        }

        $response = $this->actingAs($user)
            ->post(route('customer.claim-free-plugins'));

        $response->assertRedirectToRoute('dashboard');
        $response->assertSessionHas('message', 'You have already claimed all the free plugins.');
    }
}
