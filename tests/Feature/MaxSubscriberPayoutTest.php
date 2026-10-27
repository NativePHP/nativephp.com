<?php

namespace Tests\Feature;

use App\Enums\PayoutStatus;
use App\Enums\PriceTier;
use App\Jobs\HandleInvoicePaidJob;
use App\Models\BundlePrice;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Models\PluginPrice;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use PHPUnit\Framework\Attributes\Test;
use Stripe\Invoice;
use Tests\TestCase;

class MaxSubscriberPayoutTest extends TestCase
{
    use RefreshDatabase;

    private const MAX_PRICE_ID = 'price_1RoZk0AyFo6rlwXqjkLj4hZ0';

    private const PRO_PRICE_ID = 'price_1RoZeVAyFo6rlwXqtnOViUCf';

    protected function setUp(): void
    {
        parent::setUp();

        config(['subscriptions.plans.max.stripe_price_id' => self::MAX_PRICE_ID]);
    }

    /**
     * @param  array<int, int>  $cartItemIds
     * @param  array<string, mixed>  $attributes
     */
    private function createStripeInvoice(string $cartId, string $customerId, array $cartItemIds = [], array $attributes = []): Invoice
    {
        $invoice = Invoice::constructFrom([
            'id' => 'in_test_'.uniqid(),
            'billing_reason' => Invoice::BILLING_REASON_MANUAL,
            'customer' => $customerId,
            'payment_intent' => 'pi_test_'.uniqid(),
            'currency' => 'usd',
            'metadata' => array_filter([
                'cart_id' => $cartId,
                'cart_item_ids' => implode(',', $cartItemIds),
            ]),
            'lines' => [],
            ...$attributes,
        ]);

        return $invoice;
    }

    /**
     * An invoice line as Stripe reports it, with the discount taken off that line.
     *
     * @return array<string, mixed>
     */
    private function invoiceLine(string $description, int $amount, int $discount = 0): array
    {
        return [
            'object' => 'line_item',
            'description' => $description,
            'amount' => $amount,
            'discount_amounts' => $discount > 0 ? [['amount' => $discount, 'discount' => 'di_test']] : [],
        ];
    }

    private function createSubscription(User $user, string $priceId): Subscription
    {
        return Subscription::factory()
            ->for($user)
            ->active()
            ->create(['stripe_price' => $priceId]);
    }

    private function createThirdPartyPluginWithUltraPrice(DeveloperAccount $developerAccount): Plugin
    {
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);

        PluginPrice::factory()->regular()->amount(9900)->create(['plugin_id' => $plugin->id]);
        PluginPrice::factory()->ultra()->amount(7000)->create(['plugin_id' => $plugin->id]);

        return $plugin;
    }

    #[Test]
    public function max_subscriber_pays_normal_platform_fee_for_third_party_plugin(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $this->createSubscription($buyer, self::MAX_PRICE_ID);

        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $cart = Cart::factory()->for($buyer)->create();
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'plugin_price_id' => $plugin->prices->first()->id,
            'price_at_addition' => 2999,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(2999, $payout->gross_amount);
        $this->assertEquals(900, $payout->platform_fee);
        $this->assertEquals(2099, $payout->developer_amount);
        $this->assertEquals(PayoutStatus::Pending, $payout->status);
    }

    #[Test]
    public function max_subscriber_buying_at_the_ultra_price_pays_no_platform_fee(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $this->createSubscription($buyer, self::MAX_PRICE_ID);

        $plugin = $this->createThirdPartyPluginWithUltraPrice(DeveloperAccount::factory()->create());

        $cart = Cart::factory()->for($buyer)->create();
        $item = resolve(CartService::class)->addPlugin($cart, $plugin);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id, [$item->id]);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $license = PluginLicense::first();
        $this->assertNotNull($license);
        $this->assertEquals(7000, $license->price_paid);
        $this->assertEquals(PriceTier::Ultra, $license->price_tier);

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(7000, $payout->gross_amount);
        $this->assertEquals(0, $payout->platform_fee);
        $this->assertEquals(7000, $payout->developer_amount);
        $this->assertEquals(PayoutStatus::Pending, $payout->status);
    }

    #[Test]
    public function ultra_priced_sale_pays_the_developer_in_full_whatever_their_payout_percentage(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $this->createSubscription($buyer, self::MAX_PRICE_ID);

        $plugin = $this->createThirdPartyPluginWithUltraPrice(
            DeveloperAccount::factory()->create(['payout_percentage' => 80])
        );

        $cart = Cart::factory()->for($buyer)->create();
        $item = resolve(CartService::class)->addPlugin($cart, $plugin);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id, [$item->id]);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(7000, $payout->gross_amount);
        $this->assertEquals(0, $payout->platform_fee);
        $this->assertEquals(7000, $payout->developer_amount);
    }

    #[Test]
    public function buyer_without_ultra_pays_the_regular_price_and_the_normal_platform_fee(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $this->createSubscription($buyer, self::PRO_PRICE_ID);

        $plugin = $this->createThirdPartyPluginWithUltraPrice(DeveloperAccount::factory()->create());

        $cart = Cart::factory()->for($buyer)->create();
        $item = resolve(CartService::class)->addPlugin($cart, $plugin);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id, [$item->id]);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $license = PluginLicense::first();
        $this->assertNotNull($license);
        $this->assertEquals(9900, $license->price_paid);
        $this->assertEquals(PriceTier::Regular, $license->price_tier);

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(9900, $payout->gross_amount);
        $this->assertEquals(2970, $payout->platform_fee);
        $this->assertEquals(6930, $payout->developer_amount);
    }

    #[Test]
    public function promotion_code_on_an_ultra_priced_sale_pays_the_developer_what_the_buyer_paid(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $this->createSubscription($buyer, self::MAX_PRICE_ID);

        $plugin = $this->createThirdPartyPluginWithUltraPrice(DeveloperAccount::factory()->create());

        $cart = Cart::factory()->for($buyer)->create();
        $item = resolve(CartService::class)->addPlugin($cart, $plugin);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id, [$item->id], [
            'subtotal' => 7000,
            'total' => 6300,
            'total_discount_amounts' => [['amount' => 700, 'discount' => 'di_test']],
            'lines' => [
                'object' => 'list',
                'data' => [$this->invoiceLine($plugin->name, 7000, discount: 700)],
            ],
        ]);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $license = PluginLicense::first();
        $this->assertNotNull($license);
        $this->assertEquals(6300, $license->price_paid);
        $this->assertEquals(PriceTier::Ultra, $license->price_tier);

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(6300, $payout->gross_amount);
        $this->assertEquals(0, $payout->platform_fee);
        $this->assertEquals(6300, $payout->developer_amount);
    }

    #[Test]
    public function coupon_on_another_item_does_not_reduce_an_ultra_priced_payout(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $this->createSubscription($buyer, self::MAX_PRICE_ID);

        $plugin = $this->createThirdPartyPluginWithUltraPrice(DeveloperAccount::factory()->create());

        $product = Product::factory()->active()->create();
        ProductPrice::factory()->for($product)->regular()->amount(29900)->create();

        $cartService = resolve(CartService::class);
        $cart = Cart::factory()->for($buyer)->create();
        $pluginItem = $cartService->addPlugin($cart, $plugin);
        $productItem = $cartService->addProduct($cart, $product);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id, [$pluginItem->id, $productItem->id], [
            'subtotal' => 36900,
            'total' => 26900,
            'total_discount_amounts' => [['amount' => 10000, 'discount' => 'di_test']],
            'lines' => [
                'object' => 'list',
                'data' => [
                    $this->invoiceLine($plugin->name, 7000),
                    $this->invoiceLine($product->name, 29900, discount: 10000),
                ],
            ],
        ]);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $this->assertEquals(7000, PluginLicense::first()->price_paid);

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(7000, $payout->gross_amount);
        $this->assertEquals(0, $payout->platform_fee);
        $this->assertEquals(7000, $payout->developer_amount);
    }

    #[Test]
    public function discount_is_shared_out_when_the_ultra_priced_plugin_has_no_invoice_line(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $this->createSubscription($buyer, self::MAX_PRICE_ID);

        $plugin = $this->createThirdPartyPluginWithUltraPrice(DeveloperAccount::factory()->create());

        $cart = Cart::factory()->for($buyer)->create();
        $item = resolve(CartService::class)->addPlugin($cart, $plugin);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id, [$item->id], [
            'subtotal' => 10500,
            'total' => 9400,
            'total_discount_amounts' => [['amount' => 1100, 'discount' => 'di_test']],
        ]);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        // 7000 of a 10500 order that had 1100 taken off is 6266.67, rounded down
        $this->assertEquals(6266, PluginLicense::first()->price_paid);

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(6266, $payout->gross_amount);
        $this->assertEquals(0, $payout->platform_fee);
        $this->assertEquals(6266, $payout->developer_amount);
    }

    #[Test]
    public function ultra_priced_sale_fully_covered_by_a_promotion_code_creates_no_payout(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $this->createSubscription($buyer, self::MAX_PRICE_ID);

        $plugin = $this->createThirdPartyPluginWithUltraPrice(DeveloperAccount::factory()->create());

        $cart = Cart::factory()->for($buyer)->create();
        $item = resolve(CartService::class)->addPlugin($cart, $plugin);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id, [$item->id], [
            'subtotal' => 7000,
            'total' => 0,
            'total_discount_amounts' => [['amount' => 7000, 'discount' => 'di_test']],
            'lines' => [
                'object' => 'list',
                'data' => [$this->invoiceLine($plugin->name, 7000, discount: 7000)],
            ],
        ]);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $this->assertEquals(0, PluginLicense::first()->price_paid);
        $this->assertEquals(0, PluginPayout::count());
    }

    #[Test]
    public function promotion_code_on_a_regular_priced_sale_pays_the_developer_their_share_of_what_the_buyer_paid(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);

        $plugin = $this->createThirdPartyPluginWithUltraPrice(DeveloperAccount::factory()->create());

        $cart = Cart::factory()->for($buyer)->create();
        $item = resolve(CartService::class)->addPlugin($cart, $plugin);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id, [$item->id], [
            'subtotal' => 9900,
            'total' => 8910,
            'total_discount_amounts' => [['amount' => 990, 'discount' => 'di_test']],
            'lines' => [
                'object' => 'list',
                'data' => [$this->invoiceLine($plugin->name, 9900, discount: 990)],
            ],
        ]);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $license = PluginLicense::first();
        $this->assertEquals(8910, $license->price_paid);
        $this->assertEquals(PriceTier::Regular, $license->price_tier);

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(8910, $payout->gross_amount);
        $this->assertEquals(2673, $payout->platform_fee);
        $this->assertEquals(6237, $payout->developer_amount);
    }

    #[Test]
    public function promotion_code_on_a_bundle_is_shared_between_its_plugins(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);

        $firstPlugin = $this->createThirdPartyPluginWithUltraPrice(DeveloperAccount::factory()->create());
        $secondPlugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => false,
            'developer_account_id' => DeveloperAccount::factory()->create()->id,
        ]);
        PluginPrice::factory()->regular()->amount(4900)->create(['plugin_id' => $secondPlugin->id]);

        $bundle = PluginBundle::factory()->active()->create();
        $bundle->plugins()->attach([
            $firstPlugin->id => ['sort_order' => 1],
            $secondPlugin->id => ['sort_order' => 2],
        ]);
        BundlePrice::factory()->regular()->amount(10000)->create(['plugin_bundle_id' => $bundle->id]);

        $cart = Cart::factory()->for($buyer)->create();
        $item = resolve(CartService::class)->addBundle($cart, $bundle);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id, [$item->id], [
            'subtotal' => 10000,
            'total' => 9000,
            'total_discount_amounts' => [['amount' => 1000, 'discount' => 'di_test']],
            'lines' => [
                'object' => 'list',
                'data' => [$this->invoiceLine($bundle->name.' (Bundle)', 10000, discount: 1000)],
            ],
        ]);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        // The 9000 paid is split by regular price: 9900 and 4900 of 14800
        $firstLicense = PluginLicense::where('plugin_id', $firstPlugin->id)->sole();
        $secondLicense = PluginLicense::where('plugin_id', $secondPlugin->id)->sole();
        $this->assertEquals(6020, $firstLicense->price_paid);
        $this->assertEquals(2980, $secondLicense->price_paid);

        $this->assertEquals(6020, $firstLicense->payout->gross_amount);
        $this->assertEquals(4214, $firstLicense->payout->developer_amount);
        $this->assertEquals(2980, $secondLicense->payout->gross_amount);
        $this->assertEquals(2086, $secondLicense->payout->developer_amount);
    }

    #[Test]
    public function non_max_subscriber_gets_normal_platform_fee_for_third_party_plugin(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $this->createSubscription($buyer, self::PRO_PRICE_ID);

        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $cart = Cart::factory()->for($buyer)->create();
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'plugin_price_id' => $plugin->prices->first()->id,
            'price_at_addition' => 2999,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(2999, $payout->gross_amount);
        $this->assertEquals(900, $payout->platform_fee);
        $this->assertEquals(2099, $payout->developer_amount);
    }

    #[Test]
    public function non_subscriber_gets_normal_platform_fee_for_third_party_plugin(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);

        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $cart = Cart::factory()->for($buyer)->create();
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'plugin_price_id' => $plugin->prices->first()->id,
            'price_at_addition' => 2999,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(2999, $payout->gross_amount);
        $this->assertEquals(900, $payout->platform_fee);
        $this->assertEquals(2099, $payout->developer_amount);
    }

    #[Test]
    public function max_subscriber_gets_normal_platform_fee_for_official_plugin(): void
    {
        $buyer = User::factory()->create(['stripe_id' => 'cus_test_buyer_'.uniqid()]);
        $this->createSubscription($buyer, self::MAX_PRICE_ID);

        $developerAccount = DeveloperAccount::factory()->create();
        $plugin = Plugin::factory()->approved()->paid()->create([
            'is_active' => true,
            'is_official' => true,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
        PluginPrice::factory()->regular()->amount(2999)->create(['plugin_id' => $plugin->id]);

        $cart = Cart::factory()->for($buyer)->create();
        CartItem::create([
            'cart_id' => $cart->id,
            'plugin_id' => $plugin->id,
            'plugin_price_id' => $plugin->prices->first()->id,
            'price_at_addition' => 2999,
        ]);

        $invoice = $this->createStripeInvoice($cart->id, $buyer->stripe_id);
        $job = new HandleInvoicePaidJob($invoice);
        $job->handle();

        $payout = PluginPayout::first();
        $this->assertNotNull($payout);
        $this->assertEquals(2999, $payout->gross_amount);
        $this->assertEquals(900, $payout->platform_fee);
        $this->assertEquals(2099, $payout->developer_amount);
    }
}
