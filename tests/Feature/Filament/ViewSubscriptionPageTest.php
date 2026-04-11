<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\SubscriptionItemResource\Pages\ViewSubscriptionItem;
use App\Filament\Resources\SubscriptionResource\Pages\ViewSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\SubscriptionItem;
use Livewire\Livewire;
use Tests\TestCase;

class ViewSubscriptionPageTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_view_subscription_page_renders_successfully(): void
    {
        $subscription = Subscription::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(ViewSubscription::class, ['record' => $subscription->id])
            ->assertSuccessful();
    }

    public function test_view_subscription_item_page_renders_successfully(): void
    {
        $subscription = Subscription::factory()->create();
        $item = SubscriptionItem::factory()->create([
            'subscription_id' => $subscription->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewSubscriptionItem::class, ['record' => $item->id])
            ->assertSuccessful();
    }
}
