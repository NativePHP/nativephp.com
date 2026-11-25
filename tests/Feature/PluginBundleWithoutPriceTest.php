<?php

namespace Tests\Feature;

use App\Models\PluginBundle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PluginBundleWithoutPriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_bundle_without_any_price_has_no_discount_or_savings(): void
    {
        $bundle = PluginBundle::factory()->create(['price' => null]);

        $this->assertSame(0, $bundle->discount_percent);
        $this->assertSame(0, $bundle->savings);
        $this->assertSame(0, $bundle->getDiscountPercentForUser(null));
        $this->assertSame(0, $bundle->getSavingsForUser(User::factory()->create()));
    }
}
