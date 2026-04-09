<?php

namespace Tests\Feature\Livewire\Customer;

use App\Enums\PluginActivityType;
use App\Enums\PluginStatus;
use App\Enums\PluginTier;
use App\Enums\PluginType;
use App\Features\AllowPaidPlugins;
use App\Livewire\Customer\Plugins\Show;
use App\Models\Plugin;
use App\Models\User;
use App\Notifications\PluginSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Laravel\Pennant\Feature;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class PluginStatusTransitionsTest extends TestCase
{
    use RefreshDatabase;

    private function createGitHubUser(): User
    {
        return User::factory()->create([
            'github_id' => '12345',
            'github_username' => 'testuser',
            'github_token' => encrypt('fake-token'),
        ]);
    }

    private function createDraftPlugin(User $user, ?string $supportChannel = 'support@test.io'): Plugin
    {
        return Plugin::factory()->draft()->for($user)->create([
            'name' => 'testuser/my-plugin-'.fake()->unique()->numberBetween(100, 999999),
            'repository_url' => 'https://github.com/testuser/my-plugin-'.fake()->unique()->numberBetween(100, 999999),
            'support_channel' => $supportChannel,
        ]);
    }

    private function fakeGitHubForSubmission(Plugin $plugin): void
    {
        $repoInfo = $plugin->getRepositoryOwnerAndName();
        $base = "https://api.github.com/repos/{$repoInfo['owner']}/{$repoInfo['repo']}";

        Http::fake([
            "{$base}/hooks" => Http::response(['id' => 1], 201),
            $base => Http::response(['default_branch' => 'main']),
            "{$base}/git/trees/main*" => Http::response([
                'tree' => [
                    ['path' => 'src/ServiceProvider.php', 'type' => 'blob'],
                ],
            ]),
            "{$base}/contents/composer.json*" => Http::response([
                'content' => base64_encode(json_encode(['name' => $plugin->name])),
                'encoding' => 'base64',
            ]),
            "{$base}/contents/LICENSE*" => Http::response([], 404),
            "{$base}/releases/latest" => Http::response([], 404),
            "{$base}/tags*" => Http::response([]),
            "{$base}/readme" => Http::response([
                'content' => base64_encode('# Plugin'),
                'encoding' => 'base64',
            ]),
            "{$base}/contents/README.md*" => Http::response([
                'content' => base64_encode('# Plugin'),
                'encoding' => 'base64',
            ]),
            "{$base}/contents/nativephp.json*" => Http::response([], 404),
            'https://raw.githubusercontent.com/*' => Http::response('', 404),
        ]);
    }

    private function mountShowComponent(User $user, Plugin $plugin): Testable
    {
        [$vendor, $package] = explode('/', $plugin->name);

        return Livewire::actingAs($user)->test(Show::class, [
            'vendor' => $vendor,
            'package' => $package,
        ]);
    }

    // ========================================
    // Submit for Review (Draft → Pending)
    // ========================================

    public function test_submit_draft_for_review(): void
    {
        Notification::fake();
        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user);
        $this->fakeGitHubForSubmission($plugin);

        $this->mountShowComponent($user, $plugin)
            ->call('submitForReview');

        $plugin->refresh();
        $this->assertEquals(PluginStatus::Pending, $plugin->status);
        $this->assertNotNull($plugin->reviewed_at);

        Notification::assertSentTo($user, PluginSubmitted::class);
    }

    public function test_submit_requires_support_channel(): void
    {
        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user, supportChannel: null);

        $this->mountShowComponent($user, $plugin)
            ->call('submitForReview');

        $plugin->refresh();
        $this->assertEquals(PluginStatus::Draft, $plugin->status);
    }

    public function test_cannot_submit_non_draft_plugin(): void
    {
        $user = $this->createGitHubUser();
        $plugin = Plugin::factory()->pending()->for($user)->create([
            'name' => 'testuser/pending-plugin',
            'support_channel' => 'support@test.io',
        ]);

        $this->mountShowComponent($user, $plugin)
            ->call('submitForReview');

        $plugin->refresh();
        $this->assertEquals(PluginStatus::Pending, $plugin->status);
    }

    public function test_resubmit_after_rejection_logs_resubmitted_activity(): void
    {
        Notification::fake();
        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user);

        // Simulate a rejection in the activity history
        $plugin->activities()->create([
            'type' => PluginActivityType::Rejected,
            'from_status' => 'pending',
            'to_status' => 'rejected',
            'note' => 'Test rejection',
            'causer_id' => $user->id,
        ]);

        $this->fakeGitHubForSubmission($plugin);

        $this->mountShowComponent($user, $plugin)
            ->call('submitForReview');

        $plugin->refresh();
        $this->assertEquals(PluginStatus::Pending, $plugin->status);

        // Should log Resubmitted, not Submitted
        $latestActivity = $plugin->activities()->latest()->first();
        $this->assertEquals(PluginActivityType::Resubmitted, $latestActivity->type);
    }

    // ========================================
    // Withdraw from Review (Pending → Draft)
    // ========================================

    public function test_withdraw_from_review(): void
    {
        $user = $this->createGitHubUser();
        $plugin = Plugin::factory()->pending()->for($user)->create([
            'name' => 'testuser/withdraw-plugin',
        ]);

        $this->mountShowComponent($user, $plugin)
            ->call('withdrawFromReview');

        $plugin->refresh();
        $this->assertEquals(PluginStatus::Draft, $plugin->status);

        $latestActivity = $plugin->activities()->latest()->first();
        $this->assertEquals(PluginActivityType::Withdrawn, $latestActivity->type);
    }

    public function test_cannot_withdraw_non_pending_plugin(): void
    {
        $user = $this->createGitHubUser();
        $plugin = Plugin::factory()->draft()->for($user)->create([
            'name' => 'testuser/draft-plugin',
        ]);

        $this->mountShowComponent($user, $plugin)
            ->call('withdrawFromReview');

        $plugin->refresh();
        $this->assertEquals(PluginStatus::Draft, $plugin->status);
        $this->assertCount(0, $plugin->activities);
    }

    // ========================================
    // Return to Draft (Rejected → Draft)
    // ========================================

    public function test_return_rejected_to_draft(): void
    {
        $user = $this->createGitHubUser();
        $plugin = Plugin::factory()->rejected()->for($user)->create([
            'name' => 'testuser/rejected-plugin',
        ]);

        $this->mountShowComponent($user, $plugin)
            ->call('returnToDraft');

        $plugin->refresh();
        $this->assertEquals(PluginStatus::Draft, $plugin->status);

        $latestActivity = $plugin->activities()->latest()->first();
        $this->assertEquals(PluginActivityType::ReturnedToDraft, $latestActivity->type);
    }

    public function test_cannot_return_non_rejected_to_draft(): void
    {
        $user = $this->createGitHubUser();
        $plugin = Plugin::factory()->pending()->for($user)->create([
            'name' => 'testuser/pending-for-return',
        ]);

        $this->mountShowComponent($user, $plugin)
            ->call('returnToDraft');

        $plugin->refresh();
        $this->assertEquals(PluginStatus::Pending, $plugin->status);
    }

    // ========================================
    // Edit Guards
    // ========================================

    public function test_draft_plugin_details_editable(): void
    {
        $user = $this->createGitHubUser();
        $plugin = Plugin::factory()->draft()->for($user)->create([
            'name' => 'testuser/details-draft',
        ]);

        $this->mountShowComponent($user, $plugin)
            ->set('displayName', 'My Awesome Plugin')
            ->set('description', 'New description')
            ->set('supportChannel', 'new@support.io')
            ->set('notes', 'Updated notes')
            ->call('save');

        $plugin->refresh();
        $this->assertEquals('My Awesome Plugin', $plugin->display_name);
        $this->assertEquals('New description', $plugin->description);
        $this->assertEquals('new@support.io', $plugin->support_channel);
        $this->assertEquals('Updated notes', $plugin->notes);
    }

    public function test_pending_plugin_details_not_editable(): void
    {
        $user = $this->createGitHubUser();
        $plugin = Plugin::factory()->pending()->for($user)->create([
            'name' => 'testuser/details-pending',
            'display_name' => 'Original Name',
            'description' => 'Original',
            'support_channel' => 'original@support.io',
            'notes' => 'Original notes',
        ]);

        $this->mountShowComponent($user, $plugin)
            ->set('displayName', 'Changed Name')
            ->set('description', 'Changed')
            ->set('supportChannel', 'changed@support.io')
            ->set('notes', 'Changed notes')
            ->call('save');

        $plugin->refresh();
        $this->assertEquals('Original Name', $plugin->display_name);
        $this->assertEquals('Original', $plugin->description);
        $this->assertEquals('original@support.io', $plugin->support_channel);
        $this->assertEquals('Original notes', $plugin->notes);
    }

    public function test_approved_plugin_details_editable(): void
    {
        $user = $this->createGitHubUser();
        $plugin = Plugin::factory()->approved()->for($user)->create([
            'name' => 'testuser/details-approved',
            'notes' => 'Old notes',
        ]);

        $this->mountShowComponent($user, $plugin)
            ->set('description', 'Updated approved description')
            ->set('supportChannel', 'updated@support.io')
            ->call('save');

        $plugin->refresh();
        $this->assertEquals('Updated approved description', $plugin->description);
        $this->assertEquals('updated@support.io', $plugin->support_channel);
        // Notes should not change for approved plugins
        $this->assertEquals('Old notes', $plugin->notes);
    }

    // ========================================
    // Toggle Listing (Approved only)
    // ========================================

    public function test_toggle_listing_on_approved_plugin(): void
    {
        $user = $this->createGitHubUser();
        $plugin = Plugin::factory()->approved()->for($user)->create([
            'name' => 'testuser/toggle-approved',
            'is_active' => true,
        ]);

        $this->mountShowComponent($user, $plugin)
            ->call('toggleListing');

        $plugin->refresh();
        $this->assertFalse($plugin->is_active);

        // Toggle back
        $this->mountShowComponent($user, $plugin)
            ->call('toggleListing');

        $plugin->refresh();
        $this->assertTrue($plugin->is_active);
    }

    public function test_cannot_toggle_listing_on_non_approved_plugin(): void
    {
        $user = $this->createGitHubUser();
        $plugin = Plugin::factory()->draft()->for($user)->create([
            'name' => 'testuser/toggle-draft',
            'is_active' => true,
        ]);

        $this->mountShowComponent($user, $plugin)
            ->call('toggleListing');

        $plugin->refresh();
        $this->assertTrue($plugin->is_active);
    }

    // ========================================
    // Plugin Type & Tier on Submission
    // ========================================

    public function test_submit_free_plugin_does_not_require_tier(): void
    {
        Notification::fake();
        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user);
        $this->fakeGitHubForSubmission($plugin);

        $this->mountShowComponent($user, $plugin)
            ->set('description', 'A test plugin')
            ->set('pluginType', 'free')
            ->call('save');

        $plugin->refresh();
        $this->assertEquals(PluginType::Free, $plugin->type);
        $this->assertNull($plugin->tier);

        $this->mountShowComponent($user, $plugin)
            ->call('submitForReview');

        $plugin->refresh();
        $this->assertEquals(PluginStatus::Pending, $plugin->status);
    }

    public function test_submit_paid_plugin_requires_tier(): void
    {
        Feature::define(AllowPaidPlugins::class, true);

        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user);

        $this->mountShowComponent($user, $plugin)
            ->set('description', 'A test plugin')
            ->set('pluginType', 'paid')
            ->call('save');

        $plugin->refresh();

        $this->mountShowComponent($user, $plugin)
            ->call('submitForReview');

        $plugin->refresh();
        $this->assertEquals(PluginStatus::Draft, $plugin->status);
    }

    public function test_submit_paid_plugin_with_tier_saves_type_and_tier(): void
    {
        Notification::fake();
        Feature::define(AllowPaidPlugins::class, true);

        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user);
        $this->fakeGitHubForSubmission($plugin);

        $this->mountShowComponent($user, $plugin)
            ->set('description', 'A test plugin')
            ->set('pluginType', 'paid')
            ->set('tier', 'gold')
            ->call('save');

        $plugin->refresh();
        $this->assertEquals(PluginType::Paid, $plugin->type);
        $this->assertEquals(PluginTier::Gold, $plugin->tier);

        $this->mountShowComponent($user, $plugin)
            ->call('submitForReview');

        $plugin->refresh();
        $this->assertEquals(PluginStatus::Pending, $plugin->status);
    }

    public function test_submit_free_plugin_clears_tier(): void
    {
        Notification::fake();
        $user = $this->createGitHubUser();
        $plugin = $this->createDraftPlugin($user);
        $plugin->update(['tier' => PluginTier::Silver]);
        $this->fakeGitHubForSubmission($plugin);

        $this->mountShowComponent($user, $plugin)
            ->set('description', 'A test plugin')
            ->set('pluginType', 'free')
            ->call('save');

        $plugin->refresh();
        $this->assertEquals(PluginType::Free, $plugin->type);
        $this->assertNull($plugin->tier);

        $this->mountShowComponent($user, $plugin)
            ->call('submitForReview');

        $plugin->refresh();
        $this->assertEquals(PluginStatus::Pending, $plugin->status);
    }
}
