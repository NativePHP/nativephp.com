<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductLicense;
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function masterclass_product_page_redirects_to_course(): void
    {
        $product = Product::where('slug', 'nativephp-masterclass')->first();

        $this
            ->get(route('products.show', $product))
            ->assertRedirect(route('course'));
    }

    #[Test]
    public function non_masterclass_product_page_loads_normally(): void
    {
        $product = Product::factory()->active()->create([
            'slug' => 'plugin-dev-kit',
        ]);

        ProductPrice::factory()->for($product)->create();

        $this
            ->withoutVite()
            ->get(route('products.show', $product))
            ->assertStatus(200)
            ->assertSee($product->name);
    }

    #[Test]
    public function course_page_shows_purchase_form_for_guests(): void
    {
        $this
            ->withoutVite()
            ->get(route('course'))
            ->assertStatus(200)
            ->assertSee('Get Early Bird Access')
            ->assertDontSee('You Own This Course');
    }

    #[Test]
    public function course_page_shows_purchase_form_for_users_without_purchase(): void
    {
        $user = User::factory()->create();

        $this
            ->withoutVite()
            ->actingAs($user)
            ->get(route('course'))
            ->assertStatus(200)
            ->assertSee('Get Early Bird Access')
            ->assertDontSee('You Own This Course');
    }

    #[Test]
    public function course_page_shows_owned_state_for_purchasers(): void
    {
        $user = User::factory()->create();
        $product = Product::where('slug', 'nativephp-masterclass')->first();

        ProductLicense::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this
            ->withoutVite()
            ->actingAs($user)
            ->get(route('course'))
            ->assertStatus(200)
            ->assertSee('You Own This Course')
            ->assertDontSee('Get Early Bird Access');
    }
}
