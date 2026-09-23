<?php

namespace Tests\Feature;

use App\Livewire\Customer\Support\Create;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SupportFormPlaceholderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function reproduction_steps_placeholder_does_not_render_literal_html_entities(): void
    {
        config(['subscriptions.plans.max.stripe_price_id' => 'price_test_max_yearly']);
        $user = User::factory()->create();
        License::factory()->max()->active()->create(['user_id' => $user->id]);
        Subscription::factory()->for($user)->active()->create([
            'stripe_price' => 'price_test_max_yearly',
        ]);

        $html = Livewire::actingAs($user)
            ->test(Create::class)
            ->set('selectedProduct', 'mobile')
            ->call('nextStep')
            ->html();

        $this->assertStringNotContainsString('&#10;', $html);
        $this->assertStringContainsString("1. Open the app\n2. Navigate to...\n3. Click on...", $html);
    }
}
