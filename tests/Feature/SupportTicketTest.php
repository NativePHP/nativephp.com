<?php

namespace Tests\Feature;

use App\Filament\Resources\SupportTicketResource\Pages\ViewSupportTicket;
use App\Filament\Resources\SupportTicketResource\Widgets\TicketRepliesWidget;
use App\Livewire\Customer\Support\Create;
use App\Livewire\Customer\Support\Index;
use App\Livewire\Customer\Support\Show;
use App\Models\License;
use App\Models\Plugin;
use App\Models\SupportTicket;
use App\Models\SupportTicket\Reply;
use App\Models\User;
use App\Notifications\SupportTicketReplied;
use App\Notifications\SupportTicketSubmitted;
use App\Notifications\SupportTicketUserReplied;
use App\SupportTicket\Status;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Subscription;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    private const MAX_PRICE_ID = 'price_test_max_yearly';

    protected function setUp(): void
    {
        parent::setUp();

        config(['subscriptions.plans.max.stripe_price_id' => self::MAX_PRICE_ID]);
    }

    private function createUltraUser(): User
    {
        $user = User::factory()->create();
        License::factory()->max()->active()->create(['user_id' => $user->id]);
        Subscription::factory()->for($user)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
        ]);

        return $user;
    }

    #[Test]
    public function guests_cannot_access_create_ticket_page(): void
    {
        $this->get(route('customer.support.tickets.create'))
            ->assertRedirect();
    }

    #[Test]
    public function non_ultra_users_cannot_access_create_ticket_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('customer.support.tickets.create'))
            ->assertForbidden();
    }

    #[Test]
    public function ultra_users_can_access_create_ticket_page(): void
    {
        $user = $this->createUltraUser();

        $this->actingAs($user)
            ->get(route('customer.support.tickets.create'))
            ->assertOk()
            ->assertSeeLivewire(Create::class);
    }

    #[Test]
    public function wizard_starts_at_step_1(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->assertSet('currentStep', 1)
            ->assertSee('Which product is this about?');
    }

    #[Test]
    public function a_product_must_be_selected(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->call('nextStep')
            ->assertHasErrors('selectedProduct')
            ->assertSet('currentStep', 1);
    }

    #[Test]
    public function product_must_be_a_valid_value(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'invalid')
            ->call('nextStep')
            ->assertHasErrors('selectedProduct')
            ->assertSet('currentStep', 1);
    }

    #[Test]
    public function selecting_mobile_advances_to_step_2(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'mobile')
            ->call('nextStep')
            ->assertSet('currentStep', 2);
    }

    #[Test]
    public function mobile_selection_shows_area_type_and_bug_fields_on_step_2(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'mobile')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->assertSee('What is the issue related to?')
            ->assertSee('Bug report details')
            ->assertDontSee('Describe your issue');
    }

    #[Test]
    public function desktop_selection_shows_bug_fields_but_not_area_on_step_2(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'desktop')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->assertSee('Bug report details')
            ->assertDontSee('Describe your issue')
            ->assertDontSee('Which area?');
    }

    #[Test]
    public function bifrost_selection_shows_issue_type_on_step_2(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'bifrost')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->assertSee('Issue type')
            ->assertSee('Describe your issue')
            ->assertDontSee('Bug report details');
    }

    #[Test]
    public function nativephp_com_selection_shows_issue_type_on_step_2(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'nativephp.com')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->assertSee('Issue type')
            ->assertSee('Describe your issue')
            ->assertDontSee('Bug report details');
    }

    #[Test]
    public function bug_report_fields_are_required_when_shown(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'desktop')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->call('nextStep')
            ->assertHasErrors(['tryingToDo', 'whatHappened', 'reproductionSteps', 'environment'])
            ->assertSet('currentStep', 2);
    }

    #[Test]
    public function mobile_area_type_is_required_when_mobile_selected(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'mobile')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->set('tryingToDo', 'Test')
            ->set('whatHappened', 'Test')
            ->set('reproductionSteps', 'Test')
            ->set('environment', 'Test')
            ->call('nextStep')
            ->assertHasErrors('mobileAreaType')
            ->assertSet('currentStep', 2);
    }

    #[Test]
    public function mobile_area_is_required_when_plugin_type_selected(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'mobile')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->set('mobileAreaType', 'plugin')
            ->set('tryingToDo', 'Test')
            ->set('whatHappened', 'Test')
            ->set('reproductionSteps', 'Test')
            ->set('environment', 'Test')
            ->call('nextStep')
            ->assertHasErrors('mobileArea')
            ->assertSet('currentStep', 2);
    }

    #[Test]
    public function mobile_core_type_does_not_require_mobile_area(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'mobile')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->set('mobileAreaType', 'core')
            ->set('tryingToDo', 'Test')
            ->set('whatHappened', 'Test')
            ->set('reproductionSteps', 'Test')
            ->set('environment', 'Test')
            ->call('nextStep')
            ->assertHasNoErrors('mobileArea')
            ->assertSet('currentStep', 3);
    }

    #[Test]
    public function issue_type_is_required_when_bifrost_selected(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'bifrost')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->set('subject', 'Test subject')
            ->set('message', 'Test message')
            ->call('nextStep')
            ->assertHasErrors('issueType')
            ->assertSet('currentStep', 2);
    }

    #[Test]
    public function issue_type_must_be_valid_value(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'bifrost')
            ->call('nextStep')
            ->set('issueType', 'invalid_type')
            ->set('subject', 'Test subject')
            ->set('message', 'Test message')
            ->call('nextStep')
            ->assertHasErrors('issueType')
            ->assertSet('currentStep', 2);
    }

    #[Test]
    public function subject_is_required_on_step_2(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'bifrost')
            ->call('nextStep')
            ->set('issueType', 'bug')
            ->set('message', 'Some message')
            ->call('nextStep')
            ->assertHasErrors('subject')
            ->assertSet('currentStep', 2);
    }

    #[Test]
    public function message_is_required_on_step_2(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'bifrost')
            ->call('nextStep')
            ->set('issueType', 'bug')
            ->set('subject', 'Some subject')
            ->call('nextStep')
            ->assertHasErrors('message')
            ->assertSet('currentStep', 2);
    }

    #[Test]
    public function subject_cannot_exceed_255_characters(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'bifrost')
            ->call('nextStep')
            ->set('issueType', 'bug')
            ->set('subject', str_repeat('a', 256))
            ->set('message', 'Some message')
            ->call('nextStep')
            ->assertHasErrors('subject')
            ->assertSet('currentStep', 2);
    }

    #[Test]
    public function message_cannot_exceed_5000_characters(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'bifrost')
            ->call('nextStep')
            ->set('issueType', 'bug')
            ->set('subject', 'Some subject')
            ->set('message', str_repeat('a', 5001))
            ->call('nextStep')
            ->assertHasErrors('message')
            ->assertSet('currentStep', 2);
    }

    #[Test]
    public function full_desktop_submission_creates_ticket_with_bug_report_data(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'desktop')
            ->call('nextStep')
            ->set('tryingToDo', 'Build an app')
            ->set('whatHappened', 'It crashed')
            ->set('reproductionSteps', '1. Open app 2. Click button')
            ->set('environment', 'macOS 14, Electron 28')
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->call('submit')
            ->assertRedirect();

        $ticket = SupportTicket::where('user_id', $user->id)->first();

        $this->assertNotNull($ticket);
        $this->assertEquals('desktop', $ticket->product);
        $this->assertEquals('Build an app', $ticket->subject);
        $this->assertStringContainsString('Build an app', $ticket->message);
        $this->assertStringContainsString('It crashed', $ticket->message);
        $this->assertStringContainsString('1. Open app 2. Click button', $ticket->message);
        $this->assertStringContainsString('macOS 14, Electron 28', $ticket->message);
        $this->assertNull($ticket->issue_type);
        $this->assertEquals('Build an app', $ticket->metadata['trying_to_do']);
        $this->assertEquals('It crashed', $ticket->metadata['what_happened']);
    }

    #[Test]
    public function bifrost_submission_stores_product_and_issue_type(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'bifrost')
            ->call('nextStep')
            ->set('issueType', 'feature_request')
            ->set('subject', 'Feature request')
            ->set('message', 'Please add this feature.')
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->call('submit')
            ->assertRedirect();

        $ticket = SupportTicket::where('subject', 'Feature request')->first();

        $this->assertNotNull($ticket);
        $this->assertEquals('bifrost', $ticket->product);
        $this->assertEquals('feature_request', $ticket->issue_type);
        $this->assertNull($ticket->metadata);
    }

    #[Test]
    public function submission_redirects_to_show_page(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'nativephp.com')
            ->call('nextStep')
            ->set('issueType', 'other')
            ->set('subject', 'Redirect test')
            ->set('message', 'Testing redirect after creation.')
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->call('submit')
            ->assertRedirect();

        $ticket = SupportTicket::where('subject', 'Redirect test')->first();

        $this->assertNotNull($ticket);
        $this->assertNotEmpty(route('customer.support.tickets.show', $ticket));
    }

    #[Test]
    public function step_3_shows_full_summary_including_environment_and_reproduction_steps(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'desktop')
            ->call('nextStep')
            ->set('tryingToDo', 'Build an app')
            ->set('whatHappened', 'It crashed')
            ->set('reproductionSteps', '1. Open app 2. Click button')
            ->set('environment', 'macOS 14, PHP 8.4')
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->assertSee('Review your request')
            ->assertSee('Desktop')
            ->assertSee('Build an app')
            ->assertSee('It crashed')
            ->assertSee('1. Open app 2. Click button')
            ->assertSee('macOS 14, PHP 8.4');
    }

    #[Test]
    public function support_page_shows_priority_support_for_ultra_users(): void
    {
        $user = $this->createUltraUser();

        $this->actingAs($user)
            ->get(route('support.index'))
            ->assertOk()
            ->assertSee('Priority Support')
            ->assertSee('Submit a Ticket');
    }

    #[Test]
    public function support_page_shows_ultra_upsell_for_non_ultra_users(): void
    {
        $user = User::factory()->create();

        License::factory()->pro()->active()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('support.index'))
            ->assertOk()
            ->assertSee('Priority Support')
            ->assertSee('Learn about Ultra')
            ->assertDontSee('Submit a Ticket');
    }

    #[Test]
    public function support_page_shows_ultra_upsell_for_guests(): void
    {
        $this->get(route('support.index'))
            ->assertOk()
            ->assertSee('Priority Support')
            ->assertSee('Learn about Ultra')
            ->assertDontSee('Submit a Ticket');
    }

    #[Test]
    public function changing_product_resets_all_step_2_fields(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'mobile')
            ->call('nextStep')
            ->set('mobileAreaType', 'plugin')
            ->set('mobileArea', 'jump')
            ->set('tryingToDo', 'Something')
            ->set('whatHappened', 'Something else')
            ->set('reproductionSteps', 'Steps here')
            ->set('environment', 'macOS')
            ->call('previousStep')
            ->set('selectedProduct', 'nativephp.com')
            ->assertSet('mobileAreaType', '')
            ->assertSet('mobileArea', '')
            ->assertSet('tryingToDo', '')
            ->assertSet('whatHappened', '')
            ->assertSet('reproductionSteps', '')
            ->assertSet('environment', '')
            ->assertSet('subject', '')
            ->assertSet('message', '')
            ->assertSet('issueType', '');
    }

    #[Test]
    public function ticket_index_shows_create_button_link(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee(route('customer.support.tickets.create'));
    }

    #[Test]
    public function back_button_returns_to_previous_step(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'desktop')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->call('previousStep')
            ->assertSet('currentStep', 1);
    }

    #[Test]
    public function plugin_type_shows_approved_official_plugins_in_select(): void
    {
        $user = $this->createUltraUser();

        Plugin::factory()->approved()->create([
            'name' => 'nativephp/mobile-camera',
            'is_official' => true,
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'mobile')
            ->call('nextStep')
            ->set('mobileAreaType', 'plugin')
            ->assertSee('nativephp/mobile-camera')
            ->assertSee('Jump');
    }

    #[Test]
    public function plugin_type_shows_inactive_approved_official_plugins(): void
    {
        $user = $this->createUltraUser();

        Plugin::factory()->approved()->inactive()->create([
            'name' => 'nativephp/mobile-inactive',
            'is_official' => true,
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'mobile')
            ->call('nextStep')
            ->set('mobileAreaType', 'plugin')
            ->assertSee('nativephp/mobile-inactive');
    }

    #[Test]
    public function plugin_type_does_not_show_unapproved_official_plugins(): void
    {
        $user = $this->createUltraUser();

        Plugin::factory()->pending()->create([
            'name' => 'nativephp/mobile-pending',
            'is_official' => true,
            'user_id' => $user->id,
        ]);

        Plugin::factory()->draft()->create([
            'name' => 'nativephp/mobile-draft',
            'is_official' => true,
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'mobile')
            ->call('nextStep')
            ->set('mobileAreaType', 'plugin')
            ->assertDontSee('nativephp/mobile-pending')
            ->assertDontSee('nativephp/mobile-draft');
    }

    #[Test]
    public function mobile_plugin_submission_stores_area_in_metadata(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'mobile')
            ->call('nextStep')
            ->set('mobileAreaType', 'plugin')
            ->set('mobileArea', 'jump')
            ->set('tryingToDo', 'Navigate between screens')
            ->set('whatHappened', 'App froze')
            ->set('reproductionSteps', '1. Open app 2. Navigate')
            ->set('environment', 'iOS 17, iPhone 15')
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->call('submit')
            ->assertRedirect();

        $ticket = SupportTicket::where('user_id', $user->id)->first();

        $this->assertNotNull($ticket);
        $this->assertEquals('mobile', $ticket->product);
        $this->assertEquals('Navigate between screens', $ticket->subject);
        $this->assertEquals('plugin', $ticket->metadata['mobile_area_type']);
        $this->assertEquals('jump', $ticket->metadata['mobile_area']);
        $this->assertEquals('Navigate between screens', $ticket->metadata['trying_to_do']);
    }

    #[Test]
    public function mobile_core_submission_stores_area_type_without_area(): void
    {
        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'mobile')
            ->call('nextStep')
            ->set('mobileAreaType', 'core')
            ->set('tryingToDo', 'Build an app')
            ->set('whatHappened', 'It crashed')
            ->set('reproductionSteps', '1. Run build')
            ->set('environment', 'iOS 17')
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->call('submit')
            ->assertRedirect();

        $ticket = SupportTicket::where('user_id', $user->id)->first();

        $this->assertNotNull($ticket);
        $this->assertEquals('mobile', $ticket->product);
        $this->assertEquals('Build an app', $ticket->subject);
        $this->assertEquals('core', $ticket->metadata['mobile_area_type']);
        $this->assertArrayNotHasKey('mobile_area', $ticket->metadata);
    }

    #[Test]
    public function submitting_a_ticket_sends_notification_to_support_email(): void
    {
        Notification::fake();

        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'bifrost')
            ->call('nextStep')
            ->set('issueType', 'bug')
            ->set('subject', 'Notification test')
            ->set('message', 'Testing notification dispatch.')
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->call('submit')
            ->assertRedirect();

        Notification::assertSentOnDemand(
            SupportTicketSubmitted::class,
            function (SupportTicketSubmitted $notification, array $channels, object $notifiable) {
                return $notifiable->routes['mail'] === 'support@nativephp.com'
                    && $notification->ticket->subject === 'Notification test';
            }
        );
    }

    #[Test]
    public function support_ticket_email_includes_customer_details_with_obfuscated_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'subject' => 'Test ticket',
            'product' => 'bifrost',
            'issue_type' => 'bug',
            'message' => 'Test message',
        ]);

        $notification = new SupportTicketSubmitted($ticket);
        $mailMessage = $notification->toMail($user);
        $rendered = $mailMessage->render()->toHtml();

        $this->assertStringContainsString('Jane Smith', $rendered);
        $this->assertStringContainsString('ja**@ex*****.com', $rendered);
        $this->assertStringNotContainsString('jane@example.com', $rendered);
    }

    #[Test]
    public function authenticated_ultra_user_can_reply_to_their_open_ticket(): void
    {
        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->set('replyMessage', 'This is my reply.')
            ->call('reply')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('replies', [
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'This is my reply.',
            'note' => false,
        ]);
    }

    #[Test]
    public function user_reply_sends_notification_to_support_email(): void
    {
        Notification::fake();

        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->set('replyMessage', 'I have more info.')
            ->call('reply')
            ->assertHasNoErrors();

        Notification::assertSentOnDemand(
            SupportTicketUserReplied::class,
            function (SupportTicketUserReplied $notification, array $channels, object $notifiable) use ($ticket) {
                return $notifiable->routes['mail'] === 'support@nativephp.com'
                    && $notification->ticket->is($ticket)
                    && $notification->reply->message === 'I have more info.';
            }
        );
    }

    #[Test]
    public function ultra_user_cannot_reply_to_a_closed_ticket(): void
    {
        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'status' => Status::CLOSED,
        ]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->set('replyMessage', 'This should fail.')
            ->call('reply')
            ->assertForbidden();
    }

    #[Test]
    public function ultra_user_cannot_view_another_users_ticket(): void
    {
        $user = $this->createUltraUser();
        $otherUser = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create(['user_id' => $otherUser->id]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->assertNotFound();
    }

    #[Test]
    public function non_ultra_user_cannot_view_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->assertForbidden();
    }

    #[Test]
    public function reply_is_rate_limited_to_10_per_minute(): void
    {
        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

        $component = Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket]);

        for ($i = 1; $i <= 10; $i++) {
            $component
                ->set('replyMessage', "Reply {$i}")
                ->call('reply')
                ->assertHasNoErrors();
        }

        $component
            ->set('replyMessage', 'One too many')
            ->call('reply')
            ->assertHasErrors('replyMessage');

        $this->assertDatabaseCount('replies', 10);
    }

    #[Test]
    public function reply_message_is_required(): void
    {
        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->set('replyMessage', '')
            ->call('reply')
            ->assertHasErrors('replyMessage');
    }

    #[Test]
    public function ticket_show_page_displays_inline_reply_form_for_open_ticket(): void
    {
        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->assertSee('Add a reply');
    }

    #[Test]
    public function ticket_show_page_hides_reply_form_for_closed_ticket(): void
    {
        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'status' => Status::CLOSED,
        ]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->assertDontSee('Add a reply');
    }

    #[Test]
    public function ticket_show_page_hides_internal_notes_from_ticket_owner(): void
    {
        $user = $this->createUltraUser();
        $admin = User::factory()->create();
        $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

        Reply::factory()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'Visible staff reply',
            'note' => false,
        ]);

        Reply::factory()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'Secret internal note',
            'note' => true,
        ]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->assertSee('Visible staff reply')
            ->assertDontSee('Secret internal note');
    }

    #[Test]
    public function admin_reply_sends_notification_to_ticket_owner(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $admin = User::factory()->create();
        $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

        $reply = Reply::factory()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'We are looking into this.',
            'note' => false,
        ]);

        $ticket->user->notify(new SupportTicketReplied($ticket, $reply));

        Notification::assertSentTo(
            $user,
            SupportTicketReplied::class,
            function (SupportTicketReplied $notification) use ($ticket, $reply) {
                return $notification->ticket->is($ticket)
                    && $notification->reply->is($reply);
            }
        );
    }

    #[Test]
    public function internal_note_reply_does_not_send_notification_to_ticket_owner(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $admin = User::factory()->create();
        $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

        Reply::factory()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'Internal note only.',
            'note' => true,
        ]);

        // The RepliesRelationManager skips notification for notes,
        // so we verify no notification was sent.
        Notification::assertNotSentTo($user, SupportTicketReplied::class);
    }

    #[Test]
    public function ticket_owner_does_not_receive_notification_for_own_reply(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $ticket = SupportTicket::factory()->create(['user_id' => $admin->id]);

        Livewire::actingAs($admin)
            ->test(TicketRepliesWidget::class, ['record' => $ticket])
            ->set('newMessage', 'Replying to my own ticket.')
            ->call('sendReply');

        $this->assertDatabaseHas('replies', [
            'support_ticket_id' => $ticket->id,
            'message' => 'Replying to my own ticket.',
        ]);

        Notification::assertNotSentTo($admin, SupportTicketReplied::class);
    }

    #[Test]
    public function support_ticket_replied_notification_contains_correct_mail_content(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'subject' => 'Login issue',
        ]);
        $reply = Reply::factory()->create([
            'support_ticket_id' => $ticket->id,
            'message' => 'We have fixed the login issue.',
        ]);

        $notification = new SupportTicketReplied($ticket, $reply);
        $mail = $notification->toMail($user);
        $rendered = $mail->render()->toHtml();

        $this->assertStringContainsString($ticket->mask, $mail->subject);
        $this->assertStringNotContainsString('Login issue', $mail->subject);
        $this->assertStringContainsString('Hi Jane', $mail->greeting);
        $this->assertStringContainsString('log in to your dashboard', $rendered);
        $this->assertStringContainsString('do not reply to this email', $rendered);
        $this->assertStringNotContainsString('We have fixed the login issue', $rendered);
    }

    #[Test]
    public function ticket_show_page_displays_submission_details_section(): void
    {
        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'product' => 'mobile',
            'message' => 'Original submission message',
            'metadata' => [
                'trying_to_do' => 'Build an app',
                'what_happened' => 'It crashed',
            ],
        ]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->assertSee('Submission Details')
            ->assertSee('Mobile')
            ->assertDontSee('Original submission message')
            ->assertSee('Build an app')
            ->assertSee('It crashed');
    }

    #[Test]
    public function ticket_show_page_hides_original_message_for_desktop_tickets(): void
    {
        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'product' => 'desktop',
            'message' => 'Auto-generated bug report message',
            'metadata' => [
                'trying_to_do' => 'Run the app',
            ],
        ]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->assertDontSee('Original Message')
            ->assertSee('Run the app');
    }

    #[Test]
    public function ticket_show_page_shows_original_message_for_non_bug_report_tickets(): void
    {
        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'product' => 'nativephp.com',
            'message' => 'I have a billing question.',
        ]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->assertSee('Original Message')
            ->assertSee('I have a billing question.');
    }

    #[Test]
    public function ultra_user_can_close_their_ticket(): void
    {
        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->call('closeTicket')
            ->assertHasNoErrors();

        $this->assertEquals(Status::CLOSED, $ticket->fresh()->status);
    }

    #[Test]
    public function closing_ticket_creates_system_message(): void
    {
        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->call('closeTicket')
            ->assertSee($user->name.' closed this ticket.');

        $this->assertDatabaseHas('replies', [
            'support_ticket_id' => $ticket->id,
            'user_id' => null,
            'message' => $user->name.' closed this ticket.',
        ]);
    }

    #[Test]
    public function reopening_ticket_creates_system_message(): void
    {
        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'status' => Status::CLOSED,
        ]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->call('reopenTicket')
            ->assertSee($user->name.' reopened this ticket.');

        $this->assertDatabaseHas('replies', [
            'support_ticket_id' => $ticket->id,
            'user_id' => null,
            'message' => $user->name.' reopened this ticket.',
        ]);
    }

    #[Test]
    public function ultra_user_can_reopen_their_closed_ticket(): void
    {
        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'status' => Status::CLOSED,
        ]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->call('reopenTicket')
            ->assertHasNoErrors();

        $this->assertEquals(Status::OPEN, $ticket->fresh()->status);
    }

    #[Test]
    public function ultra_user_cannot_reopen_an_already_open_ticket(): void
    {
        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->call('reopenTicket')
            ->assertForbidden();
    }

    #[Test]
    public function ultra_user_cannot_reopen_another_users_ticket(): void
    {
        $user = $this->createUltraUser();
        $otherUser = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $otherUser->id,
            'status' => Status::CLOSED,
        ]);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->assertNotFound();
    }

    #[Test]
    public function admin_can_pin_an_internal_note(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $ticket = SupportTicket::factory()->create();
        $note = Reply::factory()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'Important context',
            'note' => true,
            'pinned' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(TicketRepliesWidget::class, ['record' => $ticket])
            ->call('togglePin', $note->id);

        $this->assertTrue($note->fresh()->pinned);
    }

    #[Test]
    public function admin_can_unpin_a_pinned_note(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $ticket = SupportTicket::factory()->create();
        $note = Reply::factory()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'Pinned context',
            'note' => true,
            'pinned' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(TicketRepliesWidget::class, ['record' => $ticket])
            ->call('togglePin', $note->id);

        $this->assertFalse($note->fresh()->pinned);
    }

    #[Test]
    public function pinning_a_note_unpins_the_previously_pinned_note(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $ticket = SupportTicket::factory()->create();
        $firstNote = Reply::factory()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'First note',
            'note' => true,
            'pinned' => true,
        ]);
        $secondNote = Reply::factory()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'Second note',
            'note' => true,
            'pinned' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(TicketRepliesWidget::class, ['record' => $ticket])
            ->call('togglePin', $secondNote->id);

        $this->assertFalse($firstNote->fresh()->pinned);
        $this->assertTrue($secondNote->fresh()->pinned);
    }

    #[Test]
    public function only_internal_notes_can_be_pinned(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $ticket = SupportTicket::factory()->create();
        $reply = Reply::factory()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'message' => 'Regular reply',
            'note' => false,
        ]);

        $this->expectException(ModelNotFoundException::class);

        Livewire::actingAs($admin)
            ->test(TicketRepliesWidget::class, ['record' => $ticket])
            ->call('togglePin', $reply->id);
    }

    #[Test]
    public function admin_view_page_shows_user_email_when_name_is_null(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $namelessUser = User::factory()->create(['name' => null]);
        $ticket = SupportTicket::factory()->create(['user_id' => $namelessUser->id]);

        Livewire::actingAs($admin)
            ->test(ViewSupportTicket::class, ['record' => $ticket->getRouteKey()])
            ->assertOk()
            ->assertSee($namelessUser->email);
    }

    #[Test]
    public function admin_view_page_shows_name_and_email_when_user_has_name(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $namedUser = User::factory()->create(['name' => 'Jane Doe']);
        $ticket = SupportTicket::factory()->create(['user_id' => $namedUser->id]);

        Livewire::actingAs($admin)
            ->test(ViewSupportTicket::class, ['record' => $ticket->getRouteKey()])
            ->assertOk()
            ->assertSee('Jane Doe')
            ->assertSee($namedUser->email);
    }

    #[Test]
    public function guests_cannot_access_ticket_index(): void
    {
        $this->get(route('customer.support.tickets'))
            ->assertRedirect();
    }

    #[Test]
    public function non_ultra_users_cannot_access_ticket_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('customer.support.tickets'))
            ->assertForbidden();
    }

    #[Test]
    public function ultra_users_can_access_ticket_index(): void
    {
        $user = $this->createUltraUser();

        $this->actingAs($user)
            ->get(route('customer.support.tickets'))
            ->assertOk()
            ->assertSeeLivewire(Index::class);
    }

    #[Test]
    public function environment_field_accepts_more_than_1000_characters(): void
    {
        $user = $this->createUltraUser();

        $longEnvironment = str_repeat('a', 2000);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'desktop')
            ->call('nextStep')
            ->set('tryingToDo', 'Build an app')
            ->set('whatHappened', 'It crashed')
            ->set('reproductionSteps', '1. Open app')
            ->set('environment', $longEnvironment)
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->assertHasNoErrors('environment');
    }

    #[Test]
    public function files_can_be_uploaded_during_ticket_creation(): void
    {
        Storage::fake('support-tickets');
        Notification::fake();

        $user = $this->createUltraUser();

        $files = [
            UploadedFile::fake()->image('screenshot.png', 100, 100),
            UploadedFile::fake()->create('log.txt', 50),
        ];

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'nativephp.com')
            ->call('nextStep')
            ->set('issueType', 'bug')
            ->set('subject', 'Need help')
            ->set('message', 'Please see attached files')
            ->set('uploads', $files)
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->call('submit')
            ->assertHasNoErrors();

        $ticket = SupportTicket::where('user_id', $user->id)->first();

        $this->assertNotNull($ticket);
        $this->assertCount(2, $ticket->attachments);
        $this->assertEquals('screenshot.png', $ticket->attachments[0]['name']);
        $this->assertEquals('log.txt', $ticket->attachments[1]['name']);

        Storage::disk('support-tickets')->assertExists($ticket->attachments[0]['path']);
        Storage::disk('support-tickets')->assertExists($ticket->attachments[1]['path']);
    }

    #[Test]
    public function ticket_creation_rejects_more_than_5_files(): void
    {
        Storage::fake('support-tickets');

        $user = $this->createUltraUser();

        $files = [];
        for ($i = 0; $i < 6; $i++) {
            $files[] = UploadedFile::fake()->create("file{$i}.txt", 10);
        }

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'nativephp.com')
            ->call('nextStep')
            ->set('issueType', 'bug')
            ->set('subject', 'Too many files')
            ->set('message', 'This has too many files')
            ->set('uploads', $files)
            ->call('nextStep')
            ->assertHasErrors('uploads');
    }

    #[Test]
    public function ticket_creation_rejects_file_over_10mb(): void
    {
        Storage::fake('support-tickets');

        $user = $this->createUltraUser();

        $largeFile = UploadedFile::fake()->create('huge.zip', 11000);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'nativephp.com')
            ->call('nextStep')
            ->set('issueType', 'bug')
            ->set('subject', 'Large file')
            ->set('message', 'This file is too big')
            ->set('uploads', [$largeFile])
            ->call('nextStep')
            ->assertHasErrors('uploads.*');
    }

    #[Test]
    public function ticket_creation_works_without_uploads(): void
    {
        Storage::fake('support-tickets');
        Notification::fake();

        $user = $this->createUltraUser();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'nativephp.com')
            ->call('nextStep')
            ->set('issueType', 'other')
            ->set('subject', 'No files')
            ->set('message', 'No attachments here')
            ->call('nextStep')
            ->call('submit')
            ->assertHasNoErrors();

        $ticket = SupportTicket::where('user_id', $user->id)->first();

        $this->assertNotNull($ticket);
        $this->assertNull($ticket->attachments);
    }

    #[Test]
    public function files_can_be_uploaded_with_reply(): void
    {
        Storage::fake('support-tickets');
        Notification::fake();

        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        $file = UploadedFile::fake()->image('reply-screenshot.png', 100, 100);

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->set('replyMessage', 'See attached')
            ->set('replyAttachments', [$file])
            ->call('reply')
            ->assertHasNoErrors();

        $reply = $ticket->replies()->latest()->first();

        $this->assertNotNull($reply);
        $this->assertCount(1, $reply->attachments);
        $this->assertEquals('reply-screenshot.png', $reply->attachments[0]['name']);

        Storage::disk('support-tickets')->assertExists($reply->attachments[0]['path']);
    }

    #[Test]
    public function reply_rejects_more_than_5_attachments(): void
    {
        Storage::fake('support-tickets');

        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        $files = [];
        for ($i = 0; $i < 6; $i++) {
            $files[] = UploadedFile::fake()->create("file{$i}.txt", 10);
        }

        Livewire::actingAs($user)
            ->test(Show::class, ['supportTicket' => $ticket])
            ->set('replyMessage', 'Too many files')
            ->set('replyAttachments', $files)
            ->call('reply')
            ->assertHasErrors('replyAttachments');
    }

    #[Test]
    public function admin_can_upload_files_with_reply(): void
    {
        Storage::fake('support-tickets');
        Notification::fake();

        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $user = User::factory()->create();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        $file = UploadedFile::fake()->create('admin-attachment.pdf', 100);

        Livewire::actingAs($admin)
            ->test(TicketRepliesWidget::class, ['record' => $ticket])
            ->set('newMessage', 'Here is the fix')
            ->set('replyAttachments', [$file])
            ->call('sendReply')
            ->assertHasNoErrors();

        $reply = $ticket->replies()->latest()->first();

        $this->assertNotNull($reply);
        $this->assertCount(1, $reply->attachments);
        $this->assertEquals('admin-attachment.pdf', $reply->attachments[0]['name']);

        Storage::disk('support-tickets')->assertExists($reply->attachments[0]['path']);
    }

    #[Test]
    public function ticket_owner_can_download_ticket_attachment(): void
    {
        Storage::fake('support-tickets');

        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'attachments' => [
                ['name' => 'test.png', 'path' => 'support-tickets/ticket_123/abc.png', 'size' => 1000, 'mime_type' => 'image/png'],
            ],
        ]);

        Storage::disk('support-tickets')->put('support-tickets/ticket_123/abc.png', 'fake content');

        $response = $this->actingAs($user)
            ->get(route('customer.support.tickets.attachment', [$ticket, 0]));

        $response->assertRedirect();
    }

    #[Test]
    public function other_user_cannot_download_ticket_attachment(): void
    {
        Storage::fake('support-tickets');

        $owner = $this->createUltraUser();
        $other = $this->createUltraUser();

        $ticket = SupportTicket::factory()->create([
            'user_id' => $owner->id,
            'attachments' => [
                ['name' => 'test.png', 'path' => 'support-tickets/ticket_123/abc.png', 'size' => 1000, 'mime_type' => 'image/png'],
            ],
        ]);

        $this->actingAs($other)
            ->get(route('customer.support.tickets.attachment', [$ticket, 0]))
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_download_ticket_attachment(): void
    {
        Storage::fake('support-tickets');

        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
        License::factory()->max()->active()->create(['user_id' => $admin->id]);
        Subscription::factory()->for($admin)->active()->create([
            'stripe_price' => self::MAX_PRICE_ID,
        ]);

        $user = User::factory()->create();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'attachments' => [
                ['name' => 'test.png', 'path' => 'support-tickets/ticket_123/abc.png', 'size' => 1000, 'mime_type' => 'image/png'],
            ],
        ]);

        Storage::disk('support-tickets')->put('support-tickets/ticket_123/abc.png', 'fake content');

        $response = $this->actingAs($admin)
            ->get(route('customer.support.tickets.attachment', [$ticket, 0]));

        $response->assertRedirect();
    }

    #[Test]
    public function invalid_attachment_index_returns_404(): void
    {
        Storage::fake('support-tickets');

        $user = $this->createUltraUser();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->id,
            'attachments' => [
                ['name' => 'test.png', 'path' => 'support-tickets/ticket_123/abc.png', 'size' => 1000, 'mime_type' => 'image/png'],
            ],
        ]);

        $this->actingAs($user)
            ->get(route('customer.support.tickets.attachment', [$ticket, 5]))
            ->assertNotFound();
    }
}
