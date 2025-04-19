<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CreateAnystackLicenseJob;
use App\Notifications\LicenseKeyGenerated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CreateAnystackLicenseJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'https://api.anystack.sh/v1/contacts' => Http::response([
                'data' => [
                    'id' => 'contact-123',
                    'email' => 'test@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                ],
            ], 201),

            'https://api.anystack.sh/v1/products/*/licenses' => Http::response([
                'data' => [
                    'id' => 'license-123',
                    'key' => 'test-license-key-12345',
                    'contact_id' => 'contact-123',
                    'policy_id' => 'policy-123',
                ],
            ], 201),
        ]);

        Notification::fake();
    }

    /** @test */
    public function it_creates_contact_and_license_on_anystack()
    {
        $job = new CreateAnystackLicenseJob(
            'test@example.com',
            'product-123',
            'policy-123',
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

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.anystack.sh/v1/products/product-123/licenses' &&
                   $request->method() === 'POST' &&
                   $request->data() === [
                       'policy_id' => 'policy-123',
                       'contact_id' => 'contact-123',
                   ];
        });
    }

    /** @test */
    public function it_stores_license_key_in_cache()
    {
        $job = new CreateAnystackLicenseJob(
            'test@example.com',
            'product-123',
            'policy-123',
            'John',
            'Doe'
        );

        $job->handle();

        $this->assertEquals('test-license-key-12345', Cache::get('test@example.com.license_key'));
    }

    /** @test */
    public function it_sends_license_key_notification()
    {
        $job = new CreateAnystackLicenseJob(
            'test@example.com',
            'product-123',
            'policy-123',
            'John',
            'Doe'
        );

        $job->handle();

        Notification::assertSentOnDemand(
            LicenseKeyGenerated::class,
            function (LicenseKeyGenerated $notification, array $channels, object $notifiable) {
                return $notifiable->routes['mail'] === 'test@example.com' &&
                        $notification->licenseKey === 'test-license-key-12345' &&
                        $notification->firstName === 'John';
            }
        );
    }

    /** @test */
    public function it_handles_missing_name_components()
    {
        // Create and run the job with missing name components
        $job = new CreateAnystackLicenseJob(
            'test@example.com',
            'product-123',
            'policy-123'
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
        Notification::assertSentOnDemand(
            LicenseKeyGenerated::class,
            function (LicenseKeyGenerated $notification, array $channels, object $notifiable) {
                return $notifiable->routes['mail'] === 'test@example.com' &&
                       $notification->licenseKey === 'test-license-key-12345' &&
                       $notification->firstName === null;
            }
        );
    }
}
