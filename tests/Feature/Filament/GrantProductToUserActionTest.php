<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\Product;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GrantProductToUserActionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_grant_to_user_action_can_be_called_with_user_id(): void
    {
        $product = Product::factory()->active()->create();
        $recipient = User::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(ListProducts::class)
            ->callAction(
                TestAction::make('grantToUser')->table($product),
                data: ['user_id' => $recipient->id],
            )
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('product_licenses', [
            'user_id' => $recipient->id,
            'product_id' => $product->id,
        ]);
    }
}
