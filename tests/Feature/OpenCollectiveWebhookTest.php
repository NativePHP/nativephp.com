<?php

namespace Tests\Feature;

use App\Enums\LicenseSource;
use App\Enums\Subscription;
use App\Http\Middleware\VerifyCsrfToken;
use App\Jobs\CreateAnystackLicenseJob;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OpenCollectiveWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable webhook signature verification for tests
        Config::set('services.opencollective.webhook_secret', null);
    }

    #[Test]
    public function opencollective_webhook_route_is_registered(): void
    {
        $response = $this->post('/opencollective/contribution');

        $this->assertNotEquals(404, $response->getStatusCode());
    }

    #[Test]
    public function opencollective_webhook_route_is_excluded_from_csrf_verification(): void
    {
        $reflection = new \ReflectionClass(VerifyCsrfToken::class);
        $property = $reflection->getProperty('except');
        $exceptPaths = $property->getValue(app(VerifyCsrfToken::class));

        $this->assertContains('opencollective/contribution', $exceptPaths);
    }

    #[Test]
    public function it_creates_a_mini_license_for_monthly_sponsor_above_ten_dollars(): void
    {
        Queue::fake();

        $payload = [
            'type' => 'collective.transaction.created',
            'data' => [
                'amount' => [
                    'value' => 1500, // $15 in cents
                    'currency' => 'USD',
                ],
                'order' => [
                    'frequency' => 'MONTHLY',
                    'fromAccount' => [
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/opencollective/contribution', $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Verify user was created
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);

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
    public function it_does_not_create_license_for_one_time_contributions(): void
    {
        Queue::fake();

        $payload = [
            'type' => 'collective.transaction.created',
            'data' => [
                'amount' => [
                    'value' => 5000, // $50 in cents
                    'currency' => 'USD',
                ],
                'order' => [
                    'frequency' => 'ONETIME',
                    'fromAccount' => [
                        'name' => 'Jane Doe',
                        'email' => 'jane@example.com',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/opencollective/contribution', $payload);

        $response->assertStatus(200);

        Queue::assertNotPushed(CreateAnystackLicenseJob::class);
    }

    #[Test]
    public function it_does_not_create_license_for_monthly_contributions_below_ten_dollars(): void
    {
        Queue::fake();

        $payload = [
            'type' => 'collective.transaction.created',
            'data' => [
                'amount' => [
                    'value' => 500, // $5 in cents
                    'currency' => 'USD',
                ],
                'order' => [
                    'frequency' => 'MONTHLY',
                    'fromAccount' => [
                        'name' => 'Bob Smith',
                        'email' => 'bob@example.com',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/opencollective/contribution', $payload);

        $response->assertStatus(200);

        Queue::assertNotPushed(CreateAnystackLicenseJob::class);
    }

    #[Test]
    public function it_does_not_create_duplicate_licenses_for_existing_opencollective_sponsors(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'name' => 'Existing User',
        ]);

        License::factory()->create([
            'user_id' => $user->id,
            'policy_name' => Subscription::Mini->value,
            'source' => LicenseSource::OpenCollective,
        ]);

        $payload = [
            'type' => 'collective.transaction.created',
            'data' => [
                'amount' => [
                    'value' => 1500,
                    'currency' => 'USD',
                ],
                'order' => [
                    'frequency' => 'MONTHLY',
                    'fromAccount' => [
                        'name' => 'Existing User',
                        'email' => 'existing@example.com',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/opencollective/contribution', $payload);

        $response->assertStatus(200);

        Queue::assertNotPushed(CreateAnystackLicenseJob::class);
    }

    #[Test]
    public function it_handles_missing_email_gracefully(): void
    {
        Queue::fake();

        $payload = [
            'type' => 'collective.transaction.created',
            'data' => [
                'amount' => [
                    'value' => 1500,
                    'currency' => 'USD',
                ],
                'order' => [
                    'frequency' => 'MONTHLY',
                    'fromAccount' => [
                        'name' => 'Anonymous',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/opencollective/contribution', $payload);

        $response->assertStatus(200);

        Queue::assertNotPushed(CreateAnystackLicenseJob::class);
    }

    #[Test]
    public function it_verifies_webhook_signature_when_secret_is_configured(): void
    {
        Config::set('services.opencollective.webhook_secret', 'test-secret');

        $payload = [
            'type' => 'collective.transaction.created',
            'data' => [],
        ];

        $response = $this->postJson('/opencollective/contribution', $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_accepts_valid_webhook_signature(): void
    {
        Queue::fake();

        Config::set('services.opencollective.webhook_secret', 'test-secret');

        $payload = [
            'type' => 'collective.transaction.created',
            'data' => [
                'amount' => [
                    'value' => 1500,
                    'currency' => 'USD',
                ],
                'order' => [
                    'frequency' => 'MONTHLY',
                    'fromAccount' => [
                        'name' => 'Test User',
                        'email' => 'test@example.com',
                    ],
                ],
            ],
        ];

        $payloadJson = json_encode($payload);
        $signature = hash_hmac('sha256', $payloadJson, 'test-secret');

        $response = $this->postJson('/opencollective/contribution', $payload, [
            'X-OpenCollective-Signature' => $signature,
        ]);

        $response->assertStatus(200);
    }
}
