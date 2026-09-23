<?php

namespace Tests\Feature\Filament;

use App\Enums\PluginActivityType;
use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Models\Plugin;
use App\Models\User;
use App\Notifications\PluginMessageReceived;
use Filament\Actions\Action;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class PluginMessageDeveloperTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_message_developer_action_logs_activity_and_notifies_developer(): void
    {
        Notification::fake();

        $developer = User::factory()->create();
        $plugin = Plugin::factory()->pending()->for($developer)->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->callAction('messageDeveloper', ['message' => 'Could you add a README example?'])
            ->assertHasNoActionErrors()
            ->assertNotified();

        $activity = $plugin->activities()->first();

        $this->assertSame(PluginActivityType::MessageToDeveloper, $activity->type);
        $this->assertSame('Could you add a README example?', $activity->note);
        $this->assertSame($this->admin->id, $activity->causer_id);
        $this->assertNull($activity->from_status);
        $this->assertSame($plugin->status, $activity->to_status);

        Notification::assertSentTo(
            $developer,
            PluginMessageReceived::class,
            fn (PluginMessageReceived $notification) => $notification->plugin->is($plugin)
        );
    }

    public function test_message_developer_action_does_not_change_plugin_status(): void
    {
        Notification::fake();

        $plugin = Plugin::factory()->pending()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->callAction('messageDeveloper', ['message' => 'Just checking in.']);

        $this->assertTrue($plugin->fresh()->isPending());
        $this->assertNull($plugin->fresh()->rejection_reason);
        $this->assertNull($plugin->fresh()->approved_at);
    }

    public function test_message_developer_action_requires_a_message(): void
    {
        Notification::fake();

        $plugin = Plugin::factory()->pending()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->callAction('messageDeveloper', ['message' => ''])
            ->assertHasActionErrors(['message' => 'required']);

        $this->assertSame(0, $plugin->activities()->count());
        Notification::assertNothingSent();
    }

    public function test_message_developer_action_is_available_for_every_status(): void
    {
        foreach (['draft', 'pending', 'approved', 'rejected'] as $state) {
            $plugin = Plugin::factory()->{$state}()->create();

            Livewire::actingAs($this->admin)
                ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
                ->assertActionVisible('messageDeveloper');
        }
    }

    public function test_developer_email_does_not_reveal_the_message_contents(): void
    {
        $developer = User::factory()->create();
        $plugin = Plugin::factory()->pending()->for($developer)->create();

        $plugin->messageDeveloper('Top secret reviewer feedback', $this->admin->id);

        $mail = (new PluginMessageReceived($plugin))->toMail($developer);
        $rendered = (string) $mail->render();

        $this->assertStringNotContainsString('Top secret reviewer feedback', $rendered);
        $this->assertStringContainsString('Please sign in to read it and reply.', $rendered);
        $this->assertStringContainsString(
            route('customer.plugins.show', $plugin->routeParams()),
            $rendered
        );
    }

    public function test_view_listing_action_is_a_top_level_header_action(): void
    {
        $plugin = Plugin::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionVisible('viewListing');

        $actions = Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->instance()
            ->getCachedHeaderActions();

        $topLevelNames = collect($actions)
            ->filter(fn ($action) => $action instanceof Action)
            ->map(fn (Action $action) => $action->getName())
            ->all();

        $this->assertContains('viewListing', $topLevelNames);
        $this->assertContains('messageDeveloper', $topLevelNames);
    }

    public function test_view_listing_action_is_hidden_for_draft_plugins(): void
    {
        $plugin = Plugin::factory()->draft()->create();

        Livewire::actingAs($this->admin)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionHidden('viewListing');
    }
}
