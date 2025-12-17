<?php

namespace Tests\Feature;

use App\Enums\LicenseSource;
use App\Enums\Subscription;
use App\Jobs\CreateAnystackLicenseJob;
use App\Livewire\ClaimDonationLicense;
use App\Models\License;
use App\Models\OpenCollectiveDonation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClaimDonationLicenseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function claim_page_is_accessible(): void
    {
        $response = $this->get('/opencollective/claim');

        $response->assertStatus(200);
        $response->assertSeeLivewire(ClaimDonationLicense::class);
    }

    #[Test]
    public function user_can_claim_a_valid_donation(): void
    {
        Queue::fake();

        $donation = OpenCollectiveDonation::factory()->create([
            'order_id' => 51763,
        ]);

        Livewire::test(ClaimDonationLicense::class)
            ->set('order_id', '51763')
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('claim')
            ->assertSet('claimed', true);

        // Verify user was created
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);

        // Verify donation was marked as claimed
        $donation->refresh();
        $this->assertTrue($donation->isClaimed());
        $this->assertNotNull($donation->user_id);

        // Verify license creation job was dispatched
        Queue::assertPushed(CreateAnystackLicenseJob::class, function ($job) {
            return $job->user->email === 'john@example.com'
                && $job->subscription === Subscription::Mini
                && $job->source === LicenseSource::OpenCollective
                && $job->firstName === 'John'
                && $job->lastName === 'Doe';
        });
    }

    #[Test]
    public function claim_fails_with_invalid_order_id(): void
    {
        Queue::fake();

        Livewire::test(ClaimDonationLicense::class)
            ->set('order_id', '99999')
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('claim')
            ->assertHasErrors(['order_id'])
            ->assertSet('claimed', false);

        Queue::assertNotPushed(CreateAnystackLicenseJob::class);
    }

    #[Test]
    public function claim_fails_for_already_claimed_donation(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $donation = OpenCollectiveDonation::factory()->claimed()->create([
            'order_id' => 51763,
            'user_id' => $user->id,
        ]);

        Livewire::test(ClaimDonationLicense::class)
            ->set('order_id', '51763')
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('claim')
            ->assertHasErrors(['order_id'])
            ->assertSet('claimed', false);

        Queue::assertNotPushed(CreateAnystackLicenseJob::class);
    }

    #[Test]
    public function claim_fails_if_contributor_already_claimed_another_donation(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        // First donation from this contributor - already claimed
        OpenCollectiveDonation::factory()->claimed()->create([
            'order_id' => 11111,
            'from_collective_id' => 99999,
            'user_id' => $user->id,
        ]);

        // Second donation from the same contributor - not yet claimed
        $donation = OpenCollectiveDonation::factory()->create([
            'order_id' => 22222,
            'from_collective_id' => 99999,
        ]);

        Livewire::test(ClaimDonationLicense::class)
            ->set('order_id', '22222')
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('claim')
            ->assertHasErrors(['order_id'])
            ->assertSet('claimed', false);

        Queue::assertNotPushed(CreateAnystackLicenseJob::class);

        // Verify the donation was not claimed
        $this->assertNull($donation->fresh()->claimed_at);
    }

    #[Test]
    public function existing_user_not_logged_in_is_told_to_log_in_first(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $donation = OpenCollectiveDonation::factory()->create([
            'order_id' => 51763,
        ]);

        Livewire::test(ClaimDonationLicense::class)
            ->set('order_id', '51763')
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('claim')
            ->assertHasErrors(['email'])
            ->assertSet('claimed', false);

        Queue::assertNotPushed(CreateAnystackLicenseJob::class);
    }

    #[Test]
    public function logged_in_user_cannot_claim_if_they_already_have_opencollective_license(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'email' => 'john@example.com',
        ]);

        License::factory()->create([
            'user_id' => $user->id,
            'source' => LicenseSource::OpenCollective,
        ]);

        $donation = OpenCollectiveDonation::factory()->create([
            'order_id' => 51763,
        ]);

        $this->actingAs($user);

        Livewire::test(ClaimDonationLicense::class)
            ->set('order_id', '51763')
            ->call('claim')
            ->assertHasErrors(['order_id'])
            ->assertSet('claimed', false);

        Queue::assertNotPushed(CreateAnystackLicenseJob::class);
    }

    #[Test]
    public function logged_in_user_without_opencollective_license_can_claim(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);

        // User has a Stripe license, not OpenCollective
        License::factory()->create([
            'user_id' => $user->id,
            'source' => LicenseSource::Stripe,
        ]);

        $donation = OpenCollectiveDonation::factory()->create([
            'order_id' => 51763,
        ]);

        $this->actingAs($user);

        Livewire::test(ClaimDonationLicense::class)
            ->set('order_id', '51763')
            ->call('claim')
            ->assertSet('claimed', true);

        Queue::assertPushed(CreateAnystackLicenseJob::class);

        // Verify donation was claimed by the existing user
        $donation->refresh();
        $this->assertEquals($user->id, $donation->user_id);
    }

    #[Test]
    public function logged_in_user_only_needs_order_id_to_claim(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        $donation = OpenCollectiveDonation::factory()->create([
            'order_id' => 51763,
        ]);

        $this->actingAs($user);

        Livewire::test(ClaimDonationLicense::class)
            ->set('order_id', '51763')
            ->call('claim')
            ->assertHasNoErrors()
            ->assertSet('claimed', true);

        Queue::assertPushed(CreateAnystackLicenseJob::class);
    }

    #[Test]
    public function validation_requires_all_fields_for_guests(): void
    {
        Livewire::test(ClaimDonationLicense::class)
            ->call('claim')
            ->assertHasErrors(['order_id', 'name', 'email', 'password']);
    }

    #[Test]
    public function validation_only_requires_order_id_for_logged_in_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(ClaimDonationLicense::class)
            ->call('claim')
            ->assertHasErrors(['order_id'])
            ->assertHasNoErrors(['name', 'email', 'password']);
    }

    #[Test]
    public function password_confirmation_must_match(): void
    {
        OpenCollectiveDonation::factory()->create([
            'order_id' => 51763,
        ]);

        Livewire::test(ClaimDonationLicense::class)
            ->set('order_id', '51763')
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different')
            ->call('claim')
            ->assertHasErrors(['password']);
    }

    #[Test]
    public function user_is_logged_in_after_claiming(): void
    {
        Queue::fake();

        $donation = OpenCollectiveDonation::factory()->create([
            'order_id' => 51763,
        ]);

        Livewire::test(ClaimDonationLicense::class)
            ->set('order_id', '51763')
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('claim');

        $this->assertAuthenticated();
    }
}
