<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CreateUserFromStripeCustomer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Stripe\Customer;
use Tests\TestCase;

class CreateUserFromStripeCustomerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_user_with_correct_attributes()
    {
        $customer = Customer::constructFrom([
            'id' => 'cus_minimal123',
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $job = new CreateUserFromStripeCustomer($customer);
        $job->handle();

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('cus_minimal123', $user->stripe_id);

        $this->assertNotNull($user->password);
        $this->assertTrue(Hash::isHashed($user->password));
    }

    /** @test */
    public function it_fails_when_a_user_with_the_same_stripe_id_already_exists()
    {
        $existingUser = User::factory()->create([
            'stripe_id' => 'cus_existing123',
        ]);

        $customer = Customer::constructFrom([
            'id' => 'cus_existing123',
            'name' => 'Another User',
            'email' => 'another@example.com',
        ]);

        $job = new CreateUserFromStripeCustomer($customer);

        $job->handle();

        $this->assertDatabaseCount('users', 1);
        $this->assertEquals($existingUser->id, User::first()->id);
    }

    /** @test */
    public function it_fails_when_a_user_with_the_same_email_already_exists()
    {
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $customer = Customer::constructFrom([
            'id' => 'cus_existing123',
            'name' => 'Another User',
            'email' => 'existing@example.com',
        ]);

        $job = new CreateUserFromStripeCustomer($customer);

        $job->handle();

        $this->assertDatabaseCount('users', 1);
        $this->assertEquals($existingUser->id, User::first()->id);
    }

    /** @test */
    public function it_handles_a_null_name_in_stripe_customer()
    {
        $customer = Customer::constructFrom([
            'id' => 'cus_noname123',
            'name' => null,
            'email' => 'noname@example.com',
        ]);

        $job = new CreateUserFromStripeCustomer($customer);
        $job->handle();

        $this->assertDatabaseHas('users', [
            'name' => null,
            'email' => 'noname@example.com',
            'stripe_id' => 'cus_noname123',
        ]);
    }

    /** @test */
    public function it_fails_when_customer_has_no_email()
    {
        $customer = Customer::constructFrom([
            'id' => 'cus_noemail123',
            'name' => 'No Email',
            'email' => '',
        ]);

        $job = new CreateUserFromStripeCustomer($customer);

        $this->expectException(ValidationException::class);

        $job->handle();

        $this->assertDatabaseCount('users', 0);
    }
}
