<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers\ProductLicensesRelationManager;
use App\Models\Product;
use App\Models\ProductLicense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductLicensesRelationManagerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $this->user = User::factory()->create();
    }

    public function test_it_lists_product_licenses_for_user(): void
    {
        $licenses = ProductLicense::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProductLicensesRelationManager::class, [
                'ownerRecord' => $this->user,
                'pageClass' => EditUser::class,
            ])
            ->assertCanSeeTableRecords($licenses)
            ->assertCountTableRecords(3);
    }

    public function test_it_does_not_show_other_users_licenses(): void
    {
        $otherUser = User::factory()->create();

        ProductLicense::factory()->create([
            'user_id' => $this->user->id,
        ]);

        ProductLicense::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProductLicensesRelationManager::class, [
                'ownerRecord' => $this->user,
                'pageClass' => EditUser::class,
            ])
            ->assertCountTableRecords(1);
    }

    public function test_it_shows_comped_status(): void
    {
        ProductLicense::factory()->create([
            'user_id' => $this->user->id,
            'is_comped' => true,
            'price_paid' => 0,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProductLicensesRelationManager::class, [
                'ownerRecord' => $this->user,
                'pageClass' => EditUser::class,
            ])
            ->assertCountTableRecords(1);
    }

    public function test_it_can_create_a_comped_product_license(): void
    {
        $product = Product::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(ProductLicensesRelationManager::class, [
                'ownerRecord' => $this->user,
                'pageClass' => EditUser::class,
            ])
            ->callTableAction('create', data: [
                'product_id' => $product->id,
                'is_comped' => true,
                'purchased_at' => now()->toDateTimeString(),
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('product_licenses', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'is_comped' => true,
            'price_paid' => 0,
            'currency' => 'USD',
        ]);
    }

    public function test_it_can_delete_a_product_license(): void
    {
        $license = ProductLicense::factory()->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProductLicensesRelationManager::class, [
                'ownerRecord' => $this->user,
                'pageClass' => EditUser::class,
            ])
            ->callTableAction('delete', $license)
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseMissing('product_licenses', [
            'id' => $license->id,
        ]);
    }

    public function test_it_can_filter_by_comped_status(): void
    {
        ProductLicense::factory()->create([
            'user_id' => $this->user->id,
            'is_comped' => true,
        ]);

        ProductLicense::factory()->create([
            'user_id' => $this->user->id,
            'is_comped' => false,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ProductLicensesRelationManager::class, [
                'ownerRecord' => $this->user,
                'pageClass' => EditUser::class,
            ])
            ->filterTable('is_comped', true)
            ->assertCountTableRecords(1);
    }
}
