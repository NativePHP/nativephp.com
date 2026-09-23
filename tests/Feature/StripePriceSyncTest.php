<?php

namespace Tests\Feature;

use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\RelationManagers\PricesRelationManager;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Exception\InvalidRequestException;
use Stripe\Price;
use Stripe\StripeClient;
use Tests\TestCase;

class StripePriceSyncTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Bind a StripeClient whose prices->retrieve returns the given Price data,
     * or throws to simulate a missing/unknown price ID.
     */
    private function mockStripePrice(?array $price): void
    {
        $mockPrices = new class($price)
        {
            public function __construct(private ?array $price) {}

            public function retrieve($id): Price
            {
                if ($this->price === null) {
                    throw new InvalidRequestException('No such price: '.$id);
                }

                return Price::constructFrom(['id' => $id] + $this->price);
            }
        };

        $mockStripeClient = $this->createMock(StripeClient::class);
        $mockStripeClient->prices = $mockPrices;

        $this->app->bind(StripeClient::class, fn () => $mockStripeClient);
    }

    #[Test]
    public function details_from_stripe_price_returns_amount_and_currency(): void
    {
        $this->mockStripePrice(['active' => true, 'unit_amount' => 29900, 'currency' => 'usd']);

        $details = ProductPrice::detailsFromStripePrice('price_test123');

        $this->assertSame(['amount' => 29900, 'currency' => 'USD'], $details);
    }

    #[Test]
    public function details_from_stripe_price_rejects_archived_price(): void
    {
        $this->mockStripePrice(['active' => false, 'unit_amount' => 29900, 'currency' => 'usd']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('archived');

        ProductPrice::detailsFromStripePrice('price_test123');
    }

    #[Test]
    public function details_from_stripe_price_rejects_price_without_fixed_amount(): void
    {
        $this->mockStripePrice(['active' => true, 'unit_amount' => null, 'currency' => 'usd']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('no fixed unit amount');

        ProductPrice::detailsFromStripePrice('price_test123');
    }

    #[Test]
    public function details_from_stripe_price_rejects_unknown_price(): void
    {
        $this->mockStripePrice(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to retrieve Stripe price');

        ProductPrice::detailsFromStripePrice('price_missing');
    }

    #[Test]
    public function admin_creating_price_with_stripe_price_id_syncs_amount_from_stripe(): void
    {
        $this->mockStripePrice(['active' => true, 'unit_amount' => 29900, 'currency' => 'usd']);

        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $product = Product::factory()->create();

        Livewire::actingAs($admin)
            ->test(PricesRelationManager::class, [
                'ownerRecord' => $product,
                'pageClass' => EditProduct::class,
            ])
            ->callTableAction('create', data: [
                'tier' => 'regular',
                'stripe_price_id' => 'price_masterclass',
                'is_active' => true,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('product_prices', [
            'product_id' => $product->id,
            'stripe_price_id' => 'price_masterclass',
            'amount' => 29900,
            'currency' => 'USD',
        ]);
    }

    #[Test]
    public function admin_creating_price_with_invalid_stripe_price_id_is_halted(): void
    {
        $this->mockStripePrice(null);

        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $product = Product::factory()->create();

        Livewire::actingAs($admin)
            ->test(PricesRelationManager::class, [
                'ownerRecord' => $product,
                'pageClass' => EditProduct::class,
            ])
            ->callTableAction('create', data: [
                'tier' => 'regular',
                'stripe_price_id' => 'price_missing',
                'is_active' => true,
            ]);

        $this->assertDatabaseMissing('product_prices', [
            'product_id' => $product->id,
            'stripe_price_id' => 'price_missing',
        ]);
    }

    #[Test]
    public function admin_editing_price_to_add_stripe_price_id_syncs_amount(): void
    {
        $this->mockStripePrice(['active' => true, 'unit_amount' => 15000, 'currency' => 'usd']);

        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $product = Product::factory()->create();
        $price = ProductPrice::factory()->for($product)->regular()->amount(9900)->create();

        Livewire::actingAs($admin)
            ->test(PricesRelationManager::class, [
                'ownerRecord' => $product,
                'pageClass' => EditProduct::class,
            ])
            ->callTableAction('edit', $price, data: [
                'tier' => 'regular',
                'stripe_price_id' => 'price_new',
                'is_active' => true,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('product_prices', [
            'id' => $price->id,
            'stripe_price_id' => 'price_new',
            'amount' => 15000,
        ]);
    }
}
