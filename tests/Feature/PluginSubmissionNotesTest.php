<?php

namespace Tests\Feature;

use App\Livewire\Customer\Plugins\Create;
use App\Livewire\Customer\Plugins\Show;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PluginSubmissionNotesTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithGitHub(): User
    {
        $user = User::factory()->create([
            'github_id' => '12345',
            'github_token' => encrypt('fake-token'),
        ]);
        DeveloperAccount::factory()->withAcceptedTerms()->create([
            'user_id' => $user->id,
        ]);

        return $user;
    }

    /** @test */
    public function creating_a_plugin_does_not_accept_notes(): void
    {
        $user = $this->createUserWithGitHub();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->assertDontSee('Any notes for the review team');
    }

    /** @test */
    public function creating_a_plugin_does_not_accept_support_channel(): void
    {
        $user = $this->createUserWithGitHub();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->assertDontSee('Support Channel');
    }

    /** @test */
    public function draft_plugin_notes_can_be_updated_via_show_page(): void
    {
        $user = $this->createUserWithGitHub();
        $plugin = Plugin::factory()->draft()->for($user)->create([
            'support_channel' => 'support@example.com',
        ]);

        [$vendor, $package] = explode('/', $plugin->name);

        Livewire::actingAs($user)
            ->test(Show::class, ['vendor' => $vendor, 'package' => $package])
            ->set('notes', 'Please review this quickly, we have a launch deadline.')
            ->call('save');

        $plugin->refresh();
        $this->assertEquals('Please review this quickly, we have a launch deadline.', $plugin->notes);
    }

    /** @test */
    public function draft_plugin_support_channel_email_can_be_updated_via_show_page(): void
    {
        $user = $this->createUserWithGitHub();
        $plugin = Plugin::factory()->draft()->for($user)->create();

        [$vendor, $package] = explode('/', $plugin->name);

        Livewire::actingAs($user)
            ->test(Show::class, ['vendor' => $vendor, 'package' => $package])
            ->set('supportChannel', 'help@example.com')
            ->call('save');

        $plugin->refresh();
        $this->assertEquals('help@example.com', $plugin->support_channel);
    }

    /** @test */
    public function draft_plugin_support_channel_url_can_be_updated_via_show_page(): void
    {
        $user = $this->createUserWithGitHub();
        $plugin = Plugin::factory()->draft()->for($user)->create();

        [$vendor, $package] = explode('/', $plugin->name);

        Livewire::actingAs($user)
            ->test(Show::class, ['vendor' => $vendor, 'package' => $package])
            ->set('supportChannel', 'https://example.com/support')
            ->call('save');

        $plugin->refresh();
        $this->assertEquals('https://example.com/support', $plugin->support_channel);
    }

    /** @test */
    public function draft_plugin_rejects_invalid_support_channel(): void
    {
        $user = $this->createUserWithGitHub();
        $plugin = Plugin::factory()->draft()->for($user)->create();

        [$vendor, $package] = explode('/', $plugin->name);

        Livewire::actingAs($user)
            ->test(Show::class, ['vendor' => $vendor, 'package' => $package])
            ->set('supportChannel', 'not-an-email-or-url')
            ->call('save')
            ->assertHasErrors('supportChannel');
    }

    /** @test */
    public function draft_plugin_rejects_empty_support_channel_on_save(): void
    {
        $user = $this->createUserWithGitHub();
        $plugin = Plugin::factory()->draft()->for($user)->create(['support_channel' => 'old@example.com']);

        [$vendor, $package] = explode('/', $plugin->name);

        Livewire::actingAs($user)
            ->test(Show::class, ['vendor' => $vendor, 'package' => $package])
            ->set('supportChannel', '')
            ->call('save')
            ->assertHasErrors('supportChannel');
    }

    /** @test */
    public function draft_plugin_rejects_empty_description_on_save(): void
    {
        $user = $this->createUserWithGitHub();
        $plugin = Plugin::factory()->draft()->for($user)->create(['support_channel' => 'support@example.com']);

        [$vendor, $package] = explode('/', $plugin->name);

        Livewire::actingAs($user)
            ->test(Show::class, ['vendor' => $vendor, 'package' => $package])
            ->set('description', '')
            ->call('save')
            ->assertHasErrors('description');
    }

    /** @test */
    public function plugin_factory_with_notes_state_works(): void
    {
        $plugin = Plugin::factory()->withNotes('Custom note')->create();

        $this->assertEquals('Custom note', $plugin->notes);
    }

    /** @test */
    public function plugin_factory_with_support_channel_state_works(): void
    {
        $plugin = Plugin::factory()->withSupportChannel('support@myplugin.dev')->create();

        $this->assertEquals('support@myplugin.dev', $plugin->support_channel);
    }
}
