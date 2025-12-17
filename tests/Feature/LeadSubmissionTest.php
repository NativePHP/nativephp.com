<?php

namespace Tests\Feature;

use App\Livewire\LeadSubmissionForm;
use App\Models\Lead;
use App\Notifications\LeadReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('leads:127.0.0.1');
    }

    #[Test]
    public function build_my_app_page_is_accessible(): void
    {
        $this->get(route('build-my-app'))
            ->assertOk()
            ->assertSeeLivewire(LeadSubmissionForm::class);
    }

    #[Test]
    public function lead_can_be_submitted_successfully(): void
    {
        Notification::fake();

        Livewire::test(LeadSubmissionForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('company', 'Acme Corp')
            ->set('description', 'I need a mobile app for my business.')
            ->set('budget', 'less_than_5k')
            ->set('turnstileToken', 'test-token')
            ->call('submit')
            ->assertSet('submitted', true)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('leads', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'company' => 'Acme Corp',
            'description' => 'I need a mobile app for my business.',
            'budget' => 'less_than_5k',
        ]);

        Notification::assertSentTo(
            Lead::first(),
            LeadReceived::class
        );
    }

    #[Test]
    public function all_fields_are_required(): void
    {
        Livewire::test(LeadSubmissionForm::class)
            ->set('name', '')
            ->set('email', '')
            ->set('company', '')
            ->set('description', '')
            ->set('budget', '')
            ->set('turnstileToken', 'test-token')
            ->call('submit')
            ->assertHasErrors(['name', 'email', 'company', 'description', 'budget']);
    }

    #[Test]
    public function email_must_be_valid(): void
    {
        Livewire::test(LeadSubmissionForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'not-an-email')
            ->set('company', 'Acme Corp')
            ->set('description', 'I need a mobile app.')
            ->set('budget', 'less_than_5k')
            ->set('turnstileToken', 'test-token')
            ->call('submit')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function budget_must_be_a_valid_option(): void
    {
        Livewire::test(LeadSubmissionForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('company', 'Acme Corp')
            ->set('description', 'I need a mobile app.')
            ->set('budget', 'invalid-budget')
            ->set('turnstileToken', 'test-token')
            ->call('submit')
            ->assertHasErrors(['budget']);
    }

    #[Test]
    public function rate_limiting_is_enforced(): void
    {
        Notification::fake();

        for ($i = 0; $i < 5; $i++) {
            Livewire::test(LeadSubmissionForm::class)
                ->set('name', 'John Doe '.$i)
                ->set('email', "john{$i}@example.com")
                ->set('company', 'Acme Corp')
                ->set('description', 'I need a mobile app.')
                ->set('budget', 'less_than_5k')
                ->set('turnstileToken', 'test-token')
                ->call('submit')
                ->assertSet('submitted', true);
        }

        $this->assertDatabaseCount('leads', 5);

        Livewire::test(LeadSubmissionForm::class)
            ->set('name', 'Rate Limited User')
            ->set('email', 'limited@example.com')
            ->set('company', 'Acme Corp')
            ->set('description', 'I need a mobile app.')
            ->set('budget', 'less_than_5k')
            ->set('turnstileToken', 'test-token')
            ->call('submit')
            ->assertHasErrors(['form']);

        $this->assertDatabaseCount('leads', 5);
    }

    #[Test]
    public function turnstile_validation_passes_when_secret_key_is_not_configured(): void
    {
        Notification::fake();

        config(['services.turnstile.secret_key' => null]);

        Livewire::test(LeadSubmissionForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('company', 'Acme Corp')
            ->set('description', 'I need a mobile app.')
            ->set('budget', 'less_than_5k')
            ->set('turnstileToken', '')
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertDatabaseCount('leads', 1);
    }

    #[Test]
    public function turnstile_validation_fails_with_invalid_token(): void
    {
        config(['services.turnstile.secret_key' => 'test-secret']);

        Http::fake([
            'challenges.cloudflare.com/*' => Http::response([
                'success' => false,
            ]),
        ]);

        Livewire::test(LeadSubmissionForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('company', 'Acme Corp')
            ->set('description', 'I need a mobile app.')
            ->set('budget', 'less_than_5k')
            ->set('turnstileToken', 'invalid-token')
            ->call('submit')
            ->assertHasErrors(['turnstileToken']);

        $this->assertDatabaseCount('leads', 0);
    }

    #[Test]
    public function turnstile_validation_passes_with_valid_token(): void
    {
        Notification::fake();

        config(['services.turnstile.secret_key' => 'test-secret']);

        Http::fake([
            'challenges.cloudflare.com/*' => Http::response([
                'success' => true,
            ]),
        ]);

        Livewire::test(LeadSubmissionForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('company', 'Acme Corp')
            ->set('description', 'I need a mobile app.')
            ->set('budget', 'less_than_5k')
            ->set('turnstileToken', 'valid-token')
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertDatabaseCount('leads', 1);
    }

    #[Test]
    public function budgets_are_passed_to_the_view(): void
    {
        Livewire::test(LeadSubmissionForm::class)
            ->assertViewHas('budgets', Lead::BUDGETS);
    }

    #[Test]
    public function ip_address_is_recorded_with_submission(): void
    {
        Notification::fake();

        Livewire::test(LeadSubmissionForm::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('company', 'Acme Corp')
            ->set('description', 'I need a mobile app.')
            ->set('budget', 'less_than_5k')
            ->set('turnstileToken', 'test-token')
            ->call('submit');

        $lead = Lead::first();
        $this->assertNotNull($lead->ip_address);
    }
}
