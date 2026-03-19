<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\OpenCollectiveDonation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
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
        $exceptPaths = $property->getValue(resolve(VerifyCsrfToken::class));

        $this->assertContains('opencollective/contribution', $exceptPaths);
    }

    #[Test]
    public function it_stores_donation_for_order_processed_webhook(): void
    {
        $payload = $this->getOrderProcessedPayload();

        $response = $this->postJson('/opencollective/contribution', $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('opencollective_donations', [
            'webhook_id' => 335409,
            'order_id' => 51763,
            'order_idv2' => '88rzownx-l9e50pxj-z836ymvb-dgk7j43a',
            'amount' => 2000,
            'currency' => 'USD',
            'interval' => null,
            'from_collective_id' => 54797,
            'from_collective_name' => 'Testing User',
            'from_collective_slug' => 'sudharaka',
        ]);
    }

    #[Test]
    public function it_does_not_store_duplicate_orders(): void
    {
        $payload = $this->getOrderProcessedPayload();

        // First webhook
        $this->postJson('/opencollective/contribution', $payload);

        // Second webhook with same order
        $response = $this->postJson('/opencollective/contribution', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseCount('opencollective_donations', 1);
    }

    #[Test]
    public function it_handles_missing_order_id_gracefully(): void
    {
        $payload = [
            'id' => 335409,
            'type' => 'order.processed',
            'data' => [
                'order' => [],
            ],
        ];

        $response = $this->postJson('/opencollective/contribution', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseCount('opencollective_donations', 0);
    }

    #[Test]
    public function it_verifies_webhook_signature_when_secret_is_configured(): void
    {
        Config::set('services.opencollective.webhook_secret', 'test-secret');

        $payload = $this->getOrderProcessedPayload();

        $response = $this->postJson('/opencollective/contribution', $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_accepts_valid_webhook_signature(): void
    {
        Config::set('services.opencollective.webhook_secret', 'test-secret');

        $payload = $this->getOrderProcessedPayload();

        $payloadJson = json_encode($payload);
        $signature = hash_hmac('sha256', $payloadJson, 'test-secret');

        $response = $this->postJson('/opencollective/contribution', $payload, [
            'X-OpenCollective-Signature' => $signature,
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function it_handles_unhandled_webhook_types(): void
    {
        $payload = [
            'id' => 12345,
            'type' => 'some.other.event',
            'data' => [],
        ];

        $response = $this->postJson('/opencollective/contribution', $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    #[Test]
    public function donation_can_be_marked_as_claimed(): void
    {
        $donation = OpenCollectiveDonation::factory()->create();
        $user = \App\Models\User::factory()->create();

        $this->assertFalse($donation->isClaimed());

        $donation->markAsClaimed($user);

        $this->assertTrue($donation->fresh()->isClaimed());
        $this->assertEquals($user->id, $donation->fresh()->user_id);
        $this->assertNotNull($donation->fresh()->claimed_at);
    }

    protected function getOrderProcessedPayload(): array
    {
        return [
            'createdAt' => '2025-12-04T16:20:34.260Z',
            'id' => 335409,
            'type' => 'order.processed',
            'CollectiveId' => 20206,
            'data' => [
                'firstPayment' => true,
                'order' => [
                    'idV2' => '88rzownx-l9e50pxj-z836ymvb-dgk7j43a',
                    'id' => 51763,
                    'totalAmount' => 2000,
                    'currency' => 'USD',
                    'description' => 'Financial contribution to BackYourStack',
                    'tags' => null,
                    'interval' => null,
                    'createdAt' => '2025-12-04T16:20:31.861Z',
                    'quantity' => 1,
                    'FromCollectiveId' => 54797,
                    'TierId' => null,
                    'formattedAmount' => '$20.00',
                    'formattedAmountWithInterval' => '$20.00',
                ],
                'host' => [
                    'idV2' => '8a47byg9-nxozdp80-xm6mjlv0-3rek5w8k',
                    'id' => 11004,
                    'type' => 'ORGANIZATION',
                    'slug' => 'opensource',
                    'name' => 'Open Source Collective',
                ],
                'collective' => [
                    'idV2' => 'rvedj9wr-oz3a56d3-d35p7blg-8x4m0ykn',
                    'id' => 20206,
                    'type' => 'COLLECTIVE',
                    'slug' => 'backyourstack',
                    'name' => 'BackYourStack',
                ],
                'fromCollective' => [
                    'idV2' => 'eeng0kzd-yvor4pz7-37gqbma8-37xlw95j',
                    'id' => 54797,
                    'type' => 'USER',
                    'slug' => 'sudharaka',
                    'name' => 'Testing User',
                    'twitterHandle' => null,
                    'githubHandle' => 'SudharakaP',
                    'repositoryUrl' => 'https://github.com/test',
                ],
            ],
        ];
    }
}
