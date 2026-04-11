<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\UsersChart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UsersChartTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_chart_renders_with_default_filter(): void
    {
        User::factory()->count(3)->create();

        Livewire::actingAs($this->admin)
            ->test(UsersChart::class)
            ->assertSuccessful();
    }

    public function test_chart_renders_with_this_month_filter(): void
    {
        User::factory()->count(2)->create(['created_at' => now()->startOfMonth()->addDays(3)]);

        Livewire::actingAs($this->admin)
            ->test(UsersChart::class)
            ->set('filter', 'this_month')
            ->assertSuccessful();
    }

    public function test_chart_renders_with_last_month_filter(): void
    {
        User::factory()->count(2)->create(['created_at' => now()->subMonth()->startOfMonth()->addDays(5)]);

        Livewire::actingAs($this->admin)
            ->test(UsersChart::class)
            ->set('filter', 'last_month')
            ->assertSuccessful();
    }

    public function test_chart_renders_with_this_year_filter(): void
    {
        User::factory()->count(2)->create(['created_at' => now()->startOfYear()->addMonths(2)]);

        Livewire::actingAs($this->admin)
            ->test(UsersChart::class)
            ->set('filter', 'this_year')
            ->assertSuccessful();
    }

    public function test_chart_renders_with_last_year_filter(): void
    {
        User::factory()->count(2)->create(['created_at' => now()->subYear()->startOfYear()->addMonths(3)]);

        Livewire::actingAs($this->admin)
            ->test(UsersChart::class)
            ->set('filter', 'last_year')
            ->assertSuccessful();
    }

    public function test_chart_renders_with_all_time_filter(): void
    {
        User::factory()->create(['created_at' => now()->subYears(2)]);
        User::factory()->count(3)->create();

        Livewire::actingAs($this->admin)
            ->test(UsersChart::class)
            ->set('filter', 'all_time')
            ->assertSuccessful();
    }

    public function test_chart_renders_with_no_users(): void
    {
        Livewire::actingAs($this->admin)
            ->test(UsersChart::class)
            ->set('filter', 'this_month')
            ->assertSuccessful();
    }

    public function test_all_time_filter_works_with_no_historical_users(): void
    {
        Livewire::actingAs($this->admin)
            ->test(UsersChart::class)
            ->set('filter', 'all_time')
            ->assertSuccessful();
    }
}
