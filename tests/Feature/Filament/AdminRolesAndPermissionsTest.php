<?php

namespace Tests\Feature\Filament;

use App\Enums\PluginStatus;
use App\Filament\Pages\Companies;
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\PluginResource;
use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Filament\Resources\PluginResource\Pages\ListPlugins;
use App\Filament\Resources\PluginResource\RelationManagers\ActivitiesRelationManager;
use App\Filament\Resources\PluginResource\RelationManagers\PricesRelationManager;
use App\Filament\Resources\SalesResource;
use App\Filament\Resources\SupportTicketResource\Widgets\TicketRepliesWidget;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Widgets\StatsOverview;
use App\Models\Plugin;
use App\Models\PluginActivity;
use App\Models\SupportTicket;
use App\Models\User;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

use function Filament\get_authorization_response;

class AdminRolesAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    private const PLUGIN_REVIEW_PERMISSIONS = [
        'ViewAny:Plugin',
        'View:Plugin',
        'Update:Plugin',
        'Approve:Plugin',
        'Reject:Plugin',
        'MessageDeveloper:Plugin',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        config(['filament.users' => ['env-admin@test.com']]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Filament::setCurrentPanel('admin');
    }

    public function test_migrations_create_only_the_super_admin_role(): void
    {
        $this->assertSame(['super_admin'], Role::pluck('name')->all());
    }

    public function test_user_with_super_admin_role_is_an_admin_and_can_open_every_section(): void
    {
        $superAdmin = $this->superAdmin();

        $this->assertTrue($superAdmin->isAdmin());

        $this->actingAs($superAdmin);

        $this->get(PluginResource::getUrl('index'))->assertOk();
        $this->get(SalesResource::getUrl('index'))->assertOk();
        $this->get(UserResource::getUrl('index'))->assertOk();
        $this->get(RoleResource::getUrl('index'))->assertOk();
        $this->get(Companies::getUrl())->assertOk();
        $this->assertTrue(StatsOverview::canView());
    }

    public function test_user_listed_in_filament_users_keeps_full_access_without_a_role(): void
    {
        $envAdmin = User::factory()->create(['email' => 'env-admin@test.com']);

        $this->assertTrue($envAdmin->isAdmin());

        $this->actingAs($envAdmin);

        $this->get(SalesResource::getUrl('index'))->assertOk();
        $this->get(RoleResource::getUrl('index'))->assertOk();
    }

    public function test_customer_without_a_role_cannot_enter_the_panel(): void
    {
        $customer = User::factory()->create();

        $this->assertFalse($customer->isAdmin());
        $this->assertFalse($customer->canAccessPanel(Filament::getPanel('admin')));

        $this->actingAs($customer)->get('/admin')->assertForbidden();
    }

    public function test_plugin_reviewer_can_list_plugins_but_not_open_sales_users_roles_or_companies(): void
    {
        $reviewer = $this->userWithRole(self::PLUGIN_REVIEW_PERMISSIONS);

        $this->assertFalse($reviewer->isAdmin());
        $this->assertTrue($reviewer->canAccessPanel(Filament::getPanel('admin')));

        $this->actingAs($reviewer);

        $this->get(PluginResource::getUrl('index'))->assertOk();
        $this->get(SalesResource::getUrl('index'))->assertForbidden();
        $this->get(UserResource::getUrl('index'))->assertForbidden();
        $this->get(RoleResource::getUrl('index'))->assertForbidden();
        $this->get(Companies::getUrl())->assertForbidden();
        $this->assertFalse(StatsOverview::canView());
    }

    public function test_staff_who_cannot_see_any_dashboard_widget_land_on_their_first_section(): void
    {
        $this->actingAs($this->userWithRole(self::PLUGIN_REVIEW_PERMISSIONS));

        $this->assertFalse(Dashboard::shouldRegisterNavigation());

        Livewire::test(Dashboard::class)->assertRedirect(PluginResource::getUrl('index'));
    }

    public function test_super_admin_still_gets_the_dashboard(): void
    {
        $this->actingAs($this->superAdmin());

        $this->assertTrue(Dashboard::shouldRegisterNavigation());

        $this->get(Dashboard::getUrl())->assertOk()->assertSeeLivewire(StatsOverview::class);
        Livewire::test(Dashboard::class)->assertNoRedirect();
    }

    public function test_plugin_reviewer_can_reject_a_plugin(): void
    {
        Notification::fake();

        $reviewer = $this->userWithRole(self::PLUGIN_REVIEW_PERMISSIONS);
        $plugin = Plugin::factory()->pending()->free()->create();

        Livewire::actingAs($reviewer)
            ->test(EditPlugin::class, ['record' => $plugin->getRouteKey()])
            ->assertActionVisible('approve')
            ->callAction('reject', data: ['rejection_reason' => 'Missing documentation.'])
            ->assertHasNoActionErrors();

        $this->assertSame(PluginStatus::Rejected, $plugin->fresh()->status);
    }

    public function test_plugin_reviewer_cannot_touch_paid_plugin_settings(): void
    {
        $reviewer = $this->userWithRole(self::PLUGIN_REVIEW_PERMISSIONS);
        $freePlugin = Plugin::factory()->approved()->free()->create(['repository_url' => 'https://github.com/acme/free']);
        $paidPlugin = Plugin::factory()->approved()->paid()->create();

        Livewire::actingAs($reviewer)
            ->test(EditPlugin::class, ['record' => $freePlugin->getRouteKey()])
            ->assertActionHidden('convertToPaid')
            ->assertActionHidden('runReviewChecks')
            ->assertActionHidden('resync')
            ->assertFormFieldDisabled('type')
            ->assertFormFieldDisabled('tier');

        Livewire::actingAs($reviewer)
            ->test(EditPlugin::class, ['record' => $paidPlugin->getRouteKey()])
            ->assertActionHidden('grantToUser')
            ->assertActionHidden('syncToSatis');

        Livewire::actingAs($reviewer)
            ->test(PricesRelationManager::class, ['ownerRecord' => $paidPlugin, 'pageClass' => EditPlugin::class])
            ->assertActionHidden(TestAction::make('create')->table());
    }

    public function test_super_admin_sees_every_plugin_action(): void
    {
        $freePlugin = Plugin::factory()->approved()->free()->create(['repository_url' => 'https://github.com/acme/free']);

        Livewire::actingAs($this->superAdmin())
            ->test(EditPlugin::class, ['record' => $freePlugin->getRouteKey()])
            ->assertActionVisible('convertToPaid')
            ->assertActionVisible('runReviewChecks')
            ->assertActionVisible('resync')
            ->assertFormFieldEnabled('type')
            ->assertFormFieldEnabled('tier');
    }

    public function test_role_without_update_permission_cannot_flip_plugin_toggles(): void
    {
        $viewer = $this->userWithRole(['ViewAny:Plugin', 'View:Plugin']);
        $editor = $this->userWithRole(['ViewAny:Plugin', 'View:Plugin', 'Update:Plugin']);
        $plugin = Plugin::factory()->pending()->free()->create(['featured' => false]);

        Livewire::actingAs($viewer)
            ->test(ListPlugins::class)
            ->call('updateTableColumnState', 'featured', (string) $plugin->getKey(), true);

        $this->assertFalse($plugin->fresh()->featured);

        Livewire::actingAs($editor)
            ->test(ListPlugins::class)
            ->call('updateTableColumnState', 'featured', (string) $plugin->getKey(), true);

        $this->assertTrue($plugin->fresh()->featured);
    }

    public function test_relation_managers_without_their_own_policy_follow_the_owner_record(): void
    {
        $plugin = Plugin::factory()->approved()->free()->create();
        $reviewer = $this->userWithRole(self::PLUGIN_REVIEW_PERMISSIONS);
        $unrelatedStaff = $this->userWithRole(['ViewAny:Lead']);

        $this->actingAs($reviewer);
        $this->assertTrue(ActivitiesRelationManager::canViewForRecord($plugin, EditPlugin::class));

        $this->actingAs($unrelatedStaff);
        $this->assertFalse(ActivitiesRelationManager::canViewForRecord($plugin, EditPlugin::class));
    }

    public function test_models_without_a_policy_are_locked_to_super_admins(): void
    {
        $this->actingAs($this->userWithRole(self::PLUGIN_REVIEW_PERMISSIONS));
        $this->assertTrue(get_authorization_response('viewAny', PluginActivity::class)->denied());

        $this->actingAs($this->superAdmin());
        $this->assertTrue(get_authorization_response('viewAny', PluginActivity::class)->allowed());
    }

    public function test_staff_who_manage_users_cannot_edit_super_admins_or_assign_roles(): void
    {
        $userManager = $this->userWithRole(['ViewAny:User', 'View:User', 'Update:User']);
        $superAdmin = $this->superAdmin();
        $customer = User::factory()->create();

        $this->assertTrue($userManager->can('update', $customer));
        $this->assertFalse($userManager->can('update', $superAdmin));
        $this->assertFalse($userManager->canImpersonate());

        $this->actingAs($userManager)
            ->get(UserResource::getUrl('edit', ['record' => $superAdmin]))
            ->assertForbidden();

        Livewire::actingAs($userManager)
            ->test(EditUser::class, ['record' => $customer->getRouteKey()])
            ->assertFormFieldHidden('roles');
    }

    public function test_super_admin_can_assign_roles_from_the_users_page(): void
    {
        $role = Role::create(['name' => 'test_role']);
        $customer = User::factory()->create();

        Livewire::actingAs($this->superAdmin())
            ->test(EditUser::class, ['record' => $customer->getRouteKey()])
            ->assertFormFieldVisible('roles')
            ->fillForm(['roles' => [$role->id]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue($customer->fresh()->hasRole('test_role'));
        $this->assertTrue($customer->fresh()->canAccessPanel(Filament::getPanel('admin')));
    }

    public function test_super_admin_role_cannot_be_edited_or_deleted(): void
    {
        $superAdmin = $this->superAdmin();
        $superAdminRole = Role::findByName(config('filament-shield.super_admin.name'));
        $otherRole = Role::create(['name' => 'test_role']);

        $this->assertFalse($superAdmin->can('update', $superAdminRole));
        $this->assertFalse($superAdmin->can('delete', $superAdminRole));
        $this->assertTrue($superAdmin->can('update', $otherRole));
    }

    public function test_ticket_viewer_without_update_permission_cannot_reply(): void
    {
        $viewer = $this->userWithRole(['ViewAny:SupportTicket', 'View:SupportTicket']);
        $ticket = SupportTicket::factory()->create();

        Livewire::actingAs($viewer)
            ->test(TicketRepliesWidget::class, ['record' => $ticket])
            ->set('newMessage', 'Hello')
            ->call('sendReply')
            ->assertForbidden();

        $this->assertSame(0, $ticket->replies()->count());
    }

    public function test_custom_roles_do_not_override_customer_facing_ticket_rules(): void
    {
        $staff = $this->userWithRole(['ViewAny:SupportTicket', 'View:SupportTicket', 'Update:SupportTicket']);
        $ticket = SupportTicket::factory()->create();

        $this->assertTrue($staff->can('view', $ticket));
        $this->assertFalse($staff->can('reply', $ticket));
        $this->assertFalse($staff->can('delete', $ticket));
        $this->assertFalse($this->superAdmin()->can('reply', $ticket));
    }

    /**
     * Pages and widgets have no policy to fall back on, so each one needs a
     * Shield trait or it would be open to every role.
     */
    public function test_every_admin_page_and_widget_requires_a_permission(): void
    {
        $panel = Filament::getPanel('admin');

        foreach (array_diff($panel->getPages(), config('filament-shield.pages.exclude')) as $page) {
            $this->assertContains(HasPageShield::class, class_uses_recursive($page), "{$page} must use HasPageShield.");
        }

        foreach (array_diff($panel->getWidgets(), config('filament-shield.widgets.exclude')) as $widget) {
            $this->assertContains(HasWidgetShield::class, class_uses_recursive($widget), "{$widget} must use HasWidgetShield.");
        }
    }

    private function superAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate(config('filament-shield.super_admin.name')));

        return $user;
    }

    /**
     * @param  list<string>  $permissions
     */
    private function userWithRole(array $permissions): User
    {
        $role = Role::findOrCreate('test_role_'.count(Role::all()));

        foreach ($permissions as $permission) {
            $role->givePermissionTo(Permission::findOrCreate($permission));
        }

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
