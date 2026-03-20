<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductLicense;
use App\Models\User;
use App\Notifications\ProductGranted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class GrantProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_grants_product_to_user(): void
    {
        Notification::fake();

        $product = Product::factory()->active()->create();
        $user = User::factory()->create();

        $this->artisan('products:grant', [
            'product' => $product->slug,
            'user' => $user->email,
        ])->assertSuccessful();

        $this->assertDatabaseHas('product_licenses', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price_paid' => 0,
            'currency' => 'USD',
            'is_comped' => true,
        ]);

        Notification::assertSentTo($user, ProductGranted::class);
    }

    public function test_skips_user_who_already_has_the_product(): void
    {
        Notification::fake();

        $product = Product::factory()->active()->create();
        $user = User::factory()->create();

        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->artisan('products:grant', [
            'product' => $product->slug,
            'user' => $user->email,
        ])->assertSuccessful()
            ->expectsOutput("User {$user->email} already has a license for this product.");

        $this->assertDatabaseCount('product_licenses', 1);

        Notification::assertNothingSent();
    }

    public function test_dry_run_does_not_create_license_or_send_email(): void
    {
        Notification::fake();

        $product = Product::factory()->active()->create();
        $user = User::factory()->create();

        $this->artisan('products:grant', [
            'product' => $product->slug,
            'user' => $user->email,
            '--dry-run' => true,
        ])->assertSuccessful();

        $this->assertDatabaseMissing('product_licenses', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        Notification::assertNothingSent();
    }

    public function test_no_email_option_grants_without_sending_notification(): void
    {
        Notification::fake();

        $product = Product::factory()->active()->create();
        $user = User::factory()->create();

        $this->artisan('products:grant', [
            'product' => $product->slug,
            'user' => $user->email,
            '--no-email' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('product_licenses', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        Notification::assertNothingSent();
    }

    public function test_fails_with_invalid_product_slug(): void
    {
        $user = User::factory()->create();

        $this->artisan('products:grant', [
            'product' => 'nonexistent-product',
            'user' => $user->email,
        ])->assertFailed();
    }

    public function test_fails_with_invalid_user_email(): void
    {
        $product = Product::factory()->active()->create();

        $this->artisan('products:grant', [
            'product' => $product->slug,
            'user' => 'nobody@example.com',
        ])->assertFailed();
    }
}
