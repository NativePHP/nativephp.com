<?php

namespace Tests\Feature;

use App\Livewire\MobilePricing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MobilePricingTest extends TestCase
{
    use RefreshDatabase;

    protected string $testPriceId;

    protected function setUp(): void
    {
        parent::setUp();

        // Make sure we're using Stripe test keys
        $this->assertStringStartsWith('sk_test_', config('cashier.secret'));

        // Create a test price in Stripe for our tests
        $product = Cashier::stripe()->products->create([
            'name' => 'Test Product',
            'description' => 'Created for testing',
        ]);

        $price = Cashier::stripe()->prices->create([
            'product' => $product->id,
            'unit_amount' => 5000, // $50.00
            'currency' => 'usd',
            'recurring' => [
                'interval' => 'year',
            ],
        ]);

        $this->testPriceId = $price->id;

        // Configure our test price ID in the app
        Config::set('subscriptions.plans.mini.stripe_price_id', $this->testPriceId);
    }

    protected function tearDown(): void
    {
        // Clean up Stripe resources
        if (isset($this->testPriceId)) {
            $price = Cashier::stripe()->prices->retrieve($this->testPriceId);
            Cashier::stripe()->products->delete($price->product, []);
            // Prices cannot be deleted in Stripe, but the product can be
        }

        parent::tearDown();
    }

    #[Test]
    public function authenticated_users_can_directly_create_checkout_session()
    {
        // Create and authenticate a user
        $user = User::factory()->create();
        Auth::login($user);

        // Test the component with real Stripe integration
        $component = Livewire::test(MobilePricing::class);

        // Call the method and assert the redirect
        $response = $component->call('createCheckoutSession', 'mini', $user);

        // The response should be a redirect to Stripe checkout
        $response->assertRedirect();
        $redirectUrl = $response->effects['redirect'];

        // Verify it's a Stripe checkout URL
        $this->assertStringContainsString('checkout.stripe.com', $redirectUrl);

        // Verify the user has a Stripe customer ID
        $user->refresh();
        $this->assertNotNull($user->stripe_id);

        // Verify the customer exists in Stripe
        $customer = Cashier::stripe()->customers->retrieve($user->stripe_id);
        $this->assertEquals($user->email, $customer->email);
    }

    #[Test]
    public function guest_users_see_purchase_modal_component()
    {
        // Make sure no user is authenticated
        Auth::logout();

        $response = $this->get(route('early-adopter'));

        // Assert that the page contains the purchase modal component
        $response->assertSeeLivewire('purchase-modal');
    }

    #[Test]
    public function it_can_find_or_create_user_by_email()
    {
        $email = 'test-find-create-'.Str::random(10).'@example.com';

        // Test with a new email
        $component = Livewire::test(MobilePricing::class);
        $method = new \ReflectionMethod(MobilePricing::class, 'findOrCreateUser');
        $method->setAccessible(true);

        $user = $method->invoke($component->instance(), $email);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($email, $user->email);
        $this->assertDatabaseHas('users', ['email' => $email]);

        // Test with an existing email
        $existingEmail = 'existing-'.Str::random(10).'@example.com';
        $existingUser = User::factory()->create(['email' => $existingEmail]);
        $foundUser = $method->invoke($component->instance(), $existingEmail);

        $this->assertEquals($existingUser->id, $foundUser->id);
    }

    #[Test]
    public function it_handles_email_submission_and_creates_checkout_session()
    {
        $email = 'test-email-submission-'.Str::random(10).'@example.com';

        // Test the component with real Stripe integration
        $component = Livewire::test(MobilePricing::class);

        // Call the method with test data
        $response = $component->call('handleEmailSubmitted', [
            'email' => $email,
            'plan' => 'mini',
        ]);

        // The response should be a redirect to Stripe checkout
        $response->assertRedirect();
        $redirectUrl = $response->effects['redirect'];

        // Verify it's a Stripe checkout URL
        $this->assertStringContainsString('checkout.stripe.com', $redirectUrl);

        // Verify a user was created with the email
        $this->assertDatabaseHas('users', ['email' => $email]);

        // Verify the user has a Stripe customer ID
        $user = User::where('email', $email)->first();
        $this->assertNotNull($user->stripe_id);

        // Verify the customer exists in Stripe
        $customer = Cashier::stripe()->customers->retrieve($user->stripe_id);
        $this->assertEquals($email, $customer->email);
    }

    #[Test]
    public function success_url_contains_checkout_session_id_placeholder()
    {
        $component = Livewire::test(MobilePricing::class);
        $method = new \ReflectionMethod(MobilePricing::class, 'successUrl');
        $method->setAccessible(true);

        $url = $method->invoke($component->instance());

        $this->assertStringContainsString('{CHECKOUT_SESSION_ID}', $url);
    }

    #[Test]
    public function it_creates_stripe_customer_for_new_users()
    {
        $email = 'new-stripe-customer-'.Str::random(10).'@example.com';

        // Create a new user
        $user = User::create([
            'email' => $email,
            'password' => Hash::make(Str::random(72)),
        ]);

        // Test the component with real Stripe integration
        $component = Livewire::test(MobilePricing::class);

        // Call the method and assert the redirect
        $response = $component->call('createCheckoutSession', 'mini', $user);

        // Refresh the user to get the updated stripe_id
        $user->refresh();

        // Verify the user has a Stripe customer ID
        $this->assertNotNull($user->stripe_id);

        // Verify the customer exists in Stripe
        $customer = Cashier::stripe()->customers->retrieve($user->stripe_id);
        $this->assertEquals($email, $customer->email);
    }

    #[Test]
    public function it_uses_existing_stripe_customer_for_existing_users()
    {
        $email = 'existing-stripe-customer-'.Str::random(10).'@example.com';

        // Create a new user
        $user = User::create([
            'email' => $email,
            'password' => Hash::make(Str::random(72)),
        ]);

        // Create a Stripe customer for this user
        $user->createAsStripeCustomer();
        $originalStripeId = $user->stripe_id;

        // Test the component with real Stripe integration
        $component = Livewire::test(MobilePricing::class);

        // Call the method and assert the redirect
        $response = $component->call('createCheckoutSession', 'mini', $user);

        // Refresh the user to get the updated stripe_id
        $user->refresh();

        // Verify the user still has the same Stripe customer ID
        $this->assertEquals($originalStripeId, $user->stripe_id);
    }
}
