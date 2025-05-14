<?php

namespace Tests\Feature\Jobs;

use App\Enums\Subscription;
use App\Jobs\CreateAnystackLicenseJob;
use App\Models\User;
use App\Notifications\LicenseKeyGenerated;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateAnystackLicenseJobTest extends TestCase
{
    use RefreshDatabase;

    protected CarbonImmutable $now;

    protected function setUp(): void
    {
        parent::setUp();

        $this->now = now()->toImmutable();

        Http::fake([
            'https://api.anystack.sh/v1/contacts' => Http::response([
                'data' => [
                    'id' => 'contact-123',
                    'email' => 'test@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'created_at' => $this->now->toIso8601String(),
                    'updated_at' => $this->now->toIso8601String(),
                ],
            ], 201),

            'https://api.anystack.sh/v1/products/*/licenses' => Http::response([
                'data' => [
                    'id' => 'license-123',
                    'key' => 'test-license-key-12345',
                    'contact_id' => 'contact-123',
                    'policy_id' => 'policy-123',
                    'name' => null,
                    'activations' => 0,
                    'max_activations' => 10,
                    'suspended' => false,
                    'expires_at' => $this->now->addYear()->toIso8601String(),
                    'created_at' => $this->now->toIso8601String(),
                    'updated_at' => $this->now->toIso8601String(),
                ],
            ], 201),
        ]);

        Notification::fake();
    }

    /** @test */
    public function it_creates_a_contact_and_license_on_anystack_via_api()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'John Doe',
        ]);

        $job = new CreateAnystackLicenseJob(
            $user,
            Subscription::Max,
            null,
            'John',
            'Doe'
        );

        $job->handle();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.anystack.sh/v1/contacts' &&
                   $request->method() === 'POST' &&
                   $request->data() === [
                       'first_name' => 'John',
                       'last_name' => 'Doe',
                       'email' => 'test@example.com',
                   ];
        });

        $productId = Subscription::Max->anystackProductId();

        Http::assertSent(function ($request) use ($productId) {
            return $request->url() === "https://api.anystack.sh/v1/products/$productId/licenses" &&
                   $request->method() === 'POST' &&
                   $request->data() === [
                       'policy_id' => Subscription::Max->anystackPolicyId(),
                       'contact_id' => 'contact-123',
                   ];
        });
    }

    /** @test */
    public function it_does_not_create_a_contact_when_the_user_already_has_a_contact_id()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'John Doe',
            'anystack_contact_id' => 'contact-123',
        ]);

        $job = new CreateAnystackLicenseJob(
            $user,
            Subscription::Max,
            null,
            'John',
            'Doe'
        );

        $job->handle();

        Http::assertNotSent(function ($request) {
            return Str::contains($request->url(), 'https://api.anystack.sh/v1/contacts');
        });
    }

    /** @test */
    public function it_stores_the_license_key_in_database()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'John Doe',
        ]);

        $job = new CreateAnystackLicenseJob(
            $user,
            Subscription::Max,
            null,
            'John',
            'Doe'
        );

        $job->handle();

        $this->assertDatabaseHas('licenses', [
            'user_id' => $user->id,
            'subscription_item_id' => null,
            'policy_name' => 'max',
            'key' => 'test-license-key-12345',
            'expires_at' => $this->now->addYear(),
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);
    }

    /** @test */
    public function the_subscription_item_id_is_filled_when_provided()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'John Doe',
        ]);

        $job = new CreateAnystackLicenseJob(
            $user,
            Subscription::Max,
            123,
            'John',
            'Doe'
        );

        $job->handle();

        $this->assertDatabaseHas('licenses', [
            'user_id' => $user->id,
            'subscription_item_id' => 123,
            'policy_name' => 'max',
            'key' => 'test-license-key-12345',
            'expires_at' => $this->now->addYear(),
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);
    }

    /** @test */
    public function it_sends_a_license_key_notification()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'John Doe',
        ]);

        $job = new CreateAnystackLicenseJob(
            $user,
            Subscription::Max,
            null,
            'John',
            'Doe'
        );

        $job->handle();

        Notification::assertSentTo(
            $user,
            function (LicenseKeyGenerated $notification, array $channels, object $notifiable) {
                return $notification->licenseKey === 'test-license-key-12345' &&
                        $notification->subscription === Subscription::Max &&
                        $notification->firstName === 'John';
            }
        );
    }

    /** @test */
    public function it_handles_missing_name_components()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => null,
        ]);

        // Create and run the job with missing name components
        $job = new CreateAnystackLicenseJob(
            $user,
            Subscription::Max,
        );

        $job->handle();

        // Assert HTTP request was made with correct data (no name components)
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.anystack.sh/v1/contacts' &&
                   $request->method() === 'POST' &&
                   $request->data() === [
                       'email' => 'test@example.com',
                   ];
        });

        // Assert notification was sent with null firstName
        Notification::assertSentTo(
            $user,
            function (LicenseKeyGenerated $notification, array $channels, object $notifiable) {
                return $notification->licenseKey === 'test-license-key-12345' &&
                       $notification->subscription === Subscription::Max &&
                       $notification->firstName === null;
            }
        );
    }
}
