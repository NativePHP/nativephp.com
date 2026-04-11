<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers\DeveloperPluginsRelationManager;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserResourceDeveloperTest extends TestCase
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

    public function test_developer_account_section_is_visible_when_user_has_developer_account(): void
    {
        DeveloperAccount::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->admin)
            ->get(EditUser::getUrl(['record' => $this->user]))
            ->assertSee('Developer Account')
            ->assertSee($this->user->developerAccount->stripe_connect_account_id);
    }

    public function test_developer_account_section_is_hidden_when_user_has_no_developer_account(): void
    {
        $this->actingAs($this->admin)
            ->get(EditUser::getUrl(['record' => $this->user]))
            ->assertDontSee('Developer Account');
    }

    public function test_developer_account_section_shows_stripe_connect_link(): void
    {
        $developerAccount = DeveloperAccount::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->admin)
            ->get(EditUser::getUrl(['record' => $this->user]))
            ->assertSee('https://dashboard.stripe.com/connect/accounts/'.$developerAccount->stripe_connect_account_id);
    }

    public function test_developer_plugins_relation_manager_lists_plugins(): void
    {
        $plugins = Plugin::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(DeveloperPluginsRelationManager::class, [
                'ownerRecord' => $this->user,
                'pageClass' => EditUser::class,
            ])
            ->assertCanSeeTableRecords($plugins)
            ->assertCountTableRecords(3);
    }

    public function test_developer_plugins_relation_manager_does_not_show_other_users_plugins(): void
    {
        $otherUser = User::factory()->create();

        Plugin::factory()->create(['user_id' => $this->user->id]);
        Plugin::factory()->create(['user_id' => $otherUser->id]);

        Livewire::actingAs($this->admin)
            ->test(DeveloperPluginsRelationManager::class, [
                'ownerRecord' => $this->user,
                'pageClass' => EditUser::class,
            ])
            ->assertCountTableRecords(1);
    }

    public function test_developer_plugins_relation_manager_renders_successfully(): void
    {
        Livewire::actingAs($this->admin)
            ->test(DeveloperPluginsRelationManager::class, [
                'ownerRecord' => $this->user,
                'pageClass' => EditUser::class,
            ])
            ->assertOk()
            ->assertCountTableRecords(0);
    }

    public function test_users_index_shows_developer_column(): void
    {
        DeveloperAccount::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $nonDeveloper = User::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(UserResource\Pages\ListUsers::class)
            ->assertCanRenderTableColumn('developerAccount.id');
    }
}
