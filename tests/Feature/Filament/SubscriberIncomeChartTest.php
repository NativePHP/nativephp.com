<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\SubscriberIncomeChart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use Livewire\Livewire;
use Tests\TestCase;

class SubscriberIncomeChartTest extends TestCase
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
        Subscription::factory()->for($this->admin)->active()->create([
            'price_paid' => 9900,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SubscriberIncomeChart::class)
            ->assertSuccessful();
    }

    public function test_chart_renders_with_this_month_filter(): void
    {
        Subscription::factory()->for($this->admin)->active()->create([
            'price_paid' => 9900,
            'created_at' => now()->startOfMonth()->addDays(3),
        ]);

        Livewire::actingAs($this->admin)
            ->test(SubscriberIncomeChart::class)
            ->set('filter', 'this_month')
            ->assertSuccessful();
    }

    public function test_chart_renders_with_last_month_filter(): void
    {
        Subscription::factory()->for($this->admin)->active()->create([
            'price_paid' => 4900,
            'created_at' => now()->subMonth()->startOfMonth()->addDays(5),
        ]);

        Livewire::actingAs($this->admin)
            ->test(SubscriberIncomeChart::class)
            ->set('filter', 'last_month')
            ->assertSuccessful();
    }

    public function test_chart_renders_with_this_year_filter(): void
    {
        Subscription::factory()->for($this->admin)->active()->create([
            'price_paid' => 9900,
            'created_at' => now()->startOfYear()->addMonths(2),
        ]);

        Livewire::actingAs($this->admin)
            ->test(SubscriberIncomeChart::class)
            ->set('filter', 'this_year')
            ->assertSuccessful();
    }

    public function test_chart_renders_with_last_year_filter(): void
    {
        Subscription::factory()->for($this->admin)->active()->create([
            'price_paid' => 9900,
            'created_at' => now()->subYear()->startOfYear()->addMonths(3),
        ]);

        Livewire::actingAs($this->admin)
            ->test(SubscriberIncomeChart::class)
            ->set('filter', 'last_year')
            ->assertSuccessful();
    }

    public function test_chart_renders_with_all_time_filter(): void
    {
        Subscription::factory()->for($this->admin)->active()->create([
            'price_paid' => 9900,
            'created_at' => now()->subYears(2),
        ]);
        Subscription::factory()->for($this->admin)->active()->create([
            'price_paid' => 4900,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SubscriberIncomeChart::class)
            ->set('filter', 'all_time')
            ->assertSuccessful();
    }

    public function test_chart_renders_with_no_subscriptions(): void
    {
        Livewire::actingAs($this->admin)
            ->test(SubscriberIncomeChart::class)
            ->set('filter', 'this_month')
            ->assertSuccessful();
    }

    public function test_all_time_filter_works_with_no_paid_subscriptions(): void
    {
        Livewire::actingAs($this->admin)
            ->test(SubscriberIncomeChart::class)
            ->set('filter', 'all_time')
            ->assertSuccessful();
    }

    public function test_comped_subscriptions_with_zero_price_are_excluded(): void
    {
        Subscription::factory()->for($this->admin)->active()->create([
            'price_paid' => 0,
            'is_comped' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SubscriberIncomeChart::class)
            ->set('filter', 'this_year')
            ->assertSuccessful();
    }

    public function test_subscriptions_with_null_price_are_excluded(): void
    {
        Subscription::factory()->for($this->admin)->active()->create([
            'price_paid' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(SubscriberIncomeChart::class)
            ->set('filter', 'this_year')
            ->assertSuccessful();
    }
}
