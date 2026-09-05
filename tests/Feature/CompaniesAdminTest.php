<?php

namespace Tests\Feature;

use App\Filament\Pages\Companies;
use App\Models\User;
use App\Services\CompanyAggregator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CompaniesAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@nativephp.com']);
        config(['filament.users' => ['admin@nativephp.com']]);
    }

    public function test_aggregator_groups_users_by_company_domain_and_excludes_consumer_domains(): void
    {
        User::factory()->create(['email' => 'a@acme.com', 'created_at' => now()->subDays(3)]);
        User::factory()->create(['email' => 'b@acme.com', 'created_at' => now()->subDay()]);
        User::factory()->create(['email' => 'c@widgets.io', 'created_at' => now()]);
        User::factory()->create(['email' => 'person@gmail.com']);
        User::factory()->create(['email' => 'person@yahoo.co.uk']);

        $companies = app(CompanyAggregator::class)->aggregate();

        $this->assertTrue($companies->has('acme.com'));
        $this->assertTrue($companies->has('widgets.io'));
        $this->assertFalse($companies->has('gmail.com'));
        $this->assertFalse($companies->has('yahoo.co.uk'));
        $this->assertSame(2, $companies['acme.com']['users_count']);
        $this->assertSame(1, $companies['widgets.io']['users_count']);
    }

    public function test_admin_companies_page_lists_company_domains_and_excludes_consumer_mailboxes(): void
    {
        User::factory()->create(['email' => 'a@acme.com']);
        User::factory()->create(['email' => 'b@acme.com']);
        User::factory()->create(['email' => 'c@widgets.io']);
        User::factory()->create(['email' => 'd@gmail.com']);

        Livewire::actingAs($this->admin)
            ->test(Companies::class)
            ->assertCanSeeTableRecords(['acme.com', 'widgets.io'])
            ->assertCanNotSeeTableRecords(['gmail.com'])
            ->assertSee('acme.com')
            ->assertSee('widgets.io')
            ->assertDontSee('gmail.com');
    }

    public function test_admin_companies_page_is_accessible_to_admins(): void
    {
        $this->actingAs($this->admin)
            ->get(Companies::getUrl())
            ->assertOk()
            ->assertSee('Companies');
    }
}
