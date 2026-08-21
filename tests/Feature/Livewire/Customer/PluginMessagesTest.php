<?php

namespace Tests\Feature\Livewire\Customer;

use App\Enums\PluginActivityType;
use App\Enums\PluginStatus;
use App\Livewire\Customer\Plugins\Show;
use App\Models\Plugin;
use App\Models\User;
use App\Notifications\PluginDeveloperReplied;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PluginMessagesTest extends TestCase
{
    use RefreshDatabase;

    private User $developer;

    private User $reviewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->developer = User::factory()->create();
        $this->reviewer = User::factory()->create(['name' => 'Reviewer Rita']);

        RateLimiter::clear('plugin-message-reply:'.$this->developer->id);
    }

    private function pluginWithMessage(string $message = 'Please add iOS support notes.'): Plugin
    {
        Notification::fake();

        $plugin = Plugin::factory()->pending()->for($this->developer)->create();
        $plugin->messageDeveloper($message, $this->reviewer->id);

        return $plugin->fresh();
    }

    private function testable(Plugin $plugin): Testable
    {
        return Livewire::actingAs($this->developer)->test(Show::class, [
            'vendor' => $plugin->routeParams()['vendor'],
            'package' => $plugin->routeParams()['package'],
        ]);
    }

    public function test_activity_tab_shows_reviewer_message_and_its_author(): void
    {
        $plugin = $this->pluginWithMessage('Please add iOS support notes.');

        $this->testable($plugin)
            ->assertSee('Activity')
            ->assertSee('Please add iOS support notes.')
            ->assertSee('Message from NativePHP')
            ->assertSee('Reviewer Rita');
    }

    public function test_activity_tab_lists_the_full_history_newest_first(): void
    {
        Notification::fake();

        $plugin = Plugin::factory()->draft()->for($this->developer)->create();
        $plugin->submit();
        $plugin->messageDeveloper('One tweak needed before approval.', $this->reviewer->id);
        $plugin->refresh();
        $plugin->messageAdmins('Tweaked and pushed.', $this->developer->id);

        $this->testable($plugin)
            ->assertSeeInOrder([
                'Tweaked and pushed.',
                'One tweak needed before approval.',
            ])
            ->assertSee('Your Reply')
            ->assertSee('Message from NativePHP')
            ->assertSee('Submitted')
            ->assertSee('Pending Review');
    }

    public function test_status_changes_appear_in_the_activity_tab_without_any_messages(): void
    {
        Notification::fake();

        $plugin = Plugin::factory()->draft()->for($this->developer)->create();
        $plugin->submit();
        $plugin->refresh();
        $plugin->withdraw();

        $this->testable($plugin)
            ->assertSee('Withdrawn')
            ->assertSee('Submitted')
            ->assertSee('by you');
    }

    public function test_message_form_is_hidden_until_the_admins_send_a_message(): void
    {
        $plugin = Plugin::factory()->pending()->for($this->developer)->create();

        $this->testable($plugin)
            ->assertSee('Activity')
            ->assertDontSee('Send a message to Marketplace admins');
    }

    public function test_message_form_is_hidden_on_drafts_even_after_an_admin_message(): void
    {
        Notification::fake();

        $plugin = Plugin::factory()->draft()->for($this->developer)->create();
        $plugin->messageDeveloper('Heads up before you submit.', $this->reviewer->id);
        $plugin->refresh();

        $this->testable($plugin)
            ->assertSee('Heads up before you submit.')
            ->assertDontSee('Send a message to Marketplace admins');
    }

    public function test_draft_plugins_reject_developer_messages(): void
    {
        Notification::fake();

        $plugin = Plugin::factory()->draft()->for($this->developer)->create();
        $plugin->messageDeveloper('Heads up before you submit.', $this->reviewer->id);
        $plugin->refresh();

        $this->testable($plugin)
            ->set('replyMessage', 'Can I ask something first?')
            ->call('sendMessage');

        $this->assertSame(0, $plugin->activities()
            ->where('type', PluginActivityType::MessageFromDeveloper)
            ->count());

        Notification::assertSentOnDemandTimes(PluginDeveloperReplied::class, 0);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function messageableStatuses(): array
    {
        return [
            'pending' => ['pending'],
            'approved' => ['approved'],
            'rejected' => ['rejected'],
        ];
    }

    #[DataProvider('messageableStatuses')]
    public function test_developer_can_message_admins_on_submitted_plugins(string $state): void
    {
        Notification::fake();

        $plugin = Plugin::factory()->{$state}()->for($this->developer)->create();
        $plugin->messageDeveloper('A question about your plugin.', $this->reviewer->id);
        $plugin->refresh();

        $this->testable($plugin)
            ->assertSee('Send a message to Marketplace admins')
            ->set('replyMessage', 'Here is my answer.')
            ->call('sendMessage')
            ->assertHasNoErrors();

        $this->assertSame(2, $plugin->messages()->count());
    }

    public function test_tab_query_parameter_opens_the_activity_tab(): void
    {
        $plugin = $this->pluginWithMessage();

        Livewire::actingAs($this->developer)
            ->withQueryParams(['tab' => 'activity'])
            ->test(Show::class, [
                'vendor' => $plugin->routeParams()['vendor'],
                'package' => $plugin->routeParams()['package'],
            ])
            ->assertSet('activeTab', 'activity');
    }

    public function test_every_activity_type_renders_with_a_badge_and_icon(): void
    {
        Notification::fake();

        $plugin = Plugin::factory()->pending()->for($this->developer)->create();

        foreach (PluginActivityType::cases() as $type) {
            $plugin->activities()->create([
                'type' => $type,
                'from_status' => PluginStatus::Draft,
                'to_status' => PluginStatus::Pending,
                'note' => "Note for {$type->value}",
                'causer_id' => $this->reviewer->id,
            ]);
        }

        $component = $this->testable($plugin);

        foreach (PluginActivityType::cases() as $type) {
            $component
                ->assertSee($type->developerLabel())
                ->assertSee("Note for {$type->value}");
        }
    }

    public function test_details_is_the_default_tab(): void
    {
        $plugin = $this->pluginWithMessage();

        $this->testable($plugin)->assertSet('activeTab', 'details');
    }

    public function test_developer_can_reply_and_the_team_is_notified(): void
    {
        $plugin = $this->pluginWithMessage();

        Notification::fake();

        $this->testable($plugin)
            ->set('replyMessage', 'Sure — added in v1.2.0.')
            ->call('sendMessage')
            ->assertHasNoErrors()
            ->assertSet('replyMessage', '');

        $reply = $plugin->messages()->latest('id')->first();

        $this->assertSame(PluginActivityType::MessageFromDeveloper, $reply->type);
        $this->assertSame('Sure — added in v1.2.0.', $reply->note);
        $this->assertSame($this->developer->id, $reply->causer_id);
        $this->assertSame($plugin->status, $reply->to_status);

        Notification::assertSentOnDemand(
            PluginDeveloperReplied::class,
            fn (PluginDeveloperReplied $notification, array $channels, object $notifiable) => $notifiable->routes['mail'] === 'support@nativephp.com'
                && $notification->plugin->is($plugin)
                && $notification->activity->is($reply)
        );
    }

    public function test_reply_appears_in_the_thread_after_sending(): void
    {
        $plugin = $this->pluginWithMessage();

        Notification::fake();

        $this->testable($plugin)
            ->set('replyMessage', 'Thanks for the review!')
            ->call('sendMessage')
            ->assertSee('Thanks for the review!');
    }

    public function test_reply_is_required(): void
    {
        $plugin = $this->pluginWithMessage();

        Notification::fake();

        $this->testable($plugin)
            ->set('replyMessage', '')
            ->call('sendMessage')
            ->assertHasErrors(['replyMessage' => 'required']);

        $this->assertSame(1, $plugin->messages()->count());
        Notification::assertNothingSent();
    }

    public function test_reply_is_rejected_when_there_is_no_conversation(): void
    {
        $plugin = Plugin::factory()->pending()->for($this->developer)->create();

        Notification::fake();

        $this->testable($plugin)
            ->set('replyMessage', 'Hello?')
            ->call('sendMessage');

        $this->assertSame(0, $plugin->messages()->count());
        Notification::assertNothingSent();
    }

    public function test_messages_are_only_visible_to_the_plugin_owner(): void
    {
        $plugin = $this->pluginWithMessage('Confidential review note.');

        $intruder = User::factory()->create();

        Livewire::actingAs($intruder)
            ->test(Show::class, [
                'vendor' => $plugin->routeParams()['vendor'],
                'package' => $plugin->routeParams()['package'],
            ])
            ->assertForbidden();
    }

    public function test_replies_are_rate_limited(): void
    {
        $plugin = $this->pluginWithMessage();

        Notification::fake();

        $component = $this->testable($plugin);

        for ($i = 0; $i < 10; $i++) {
            $component->set('replyMessage', "Reply {$i}")->call('sendMessage');
        }

        $component->set('replyMessage', 'One too many')->call('sendMessage')
            ->assertHasErrors('replyMessage');

        $this->assertSame(11, $plugin->messages()->count());
    }
}
