<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Models\Product;
use App\Models\ProductLicense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class PurchaseHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
    }

    public function test_guest_cannot_view_purchase_history(): void
    {
        $response = $this->get('/customer/purchase-history');

        $response->assertRedirect('/login');
    }

    public function test_purchase_history_shows_product_licenses(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['name' => 'Plugin Dev Kit']);

        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price_paid' => 4900,
            'currency' => 'USD',
            'purchased_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->get('/customer/purchase-history');

        $response->assertStatus(200);
        $response->assertSee('Plugin Dev Kit');
        $response->assertSee('Product');
        $response->assertSee('$49.00');
    }

    public function test_purchase_history_shows_multiple_product_types(): void
    {
        $user = User::factory()->create();

        $devKit = Product::factory()->create(['name' => 'Plugin Dev Kit']);
        $masterclass = Product::factory()->create(['name' => 'The NativePHP Masterclass']);

        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $devKit->id,
            'price_paid' => 4900,
            'purchased_at' => now()->subDays(2),
        ]);

        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $masterclass->id,
            'price_paid' => 10100,
            'purchased_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->get('/customer/purchase-history');

        $response->assertStatus(200);
        $response->assertSee('Plugin Dev Kit');
        $response->assertSee('The NativePHP Masterclass');
    }

    public function test_dashboard_total_purchases_includes_product_licenses(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('totalPurchases', 1);
    }

    public function test_purchase_history_shows_empty_state_when_no_purchases(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/customer/purchase-history');

        $response->assertStatus(200);
        $response->assertSee('No purchases yet');
    }

    public function test_product_purchases_show_as_active(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['name' => 'Plugin Dev Kit']);

        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'purchased_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/customer/purchase-history');

        $response->assertStatus(200);
        $response->assertSee('Active');
    }
}
