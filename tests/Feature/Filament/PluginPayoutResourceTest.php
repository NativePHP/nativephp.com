<?php

namespace Tests\Feature\Filament;

use App\Enums\PayoutStatus;
use App\Filament\Resources\PluginPayoutResource\Pages\ListPluginPayouts;
use App\Filament\Resources\PluginPayoutResource\Pages\ViewPluginPayout;
use App\Filament\Resources\PluginPayoutResource\RelationManagers\AttemptsRelationManager;
use App\Jobs\ProcessPayoutTransfer;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Models\PluginPayoutAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class PluginPayoutResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_list_page_renders_successfully(): void
    {
        PluginPayout::factory()->count(3)->create();

        Livewire::actingAs($this->admin)
            ->test(ListPluginPayouts::class)
            ->assertSuccessful();
    }

    public function test_list_page_shows_plugin_customer_and_amounts(): void
    {
        $plugin = Plugin::factory()->paid()->create(['name' => 'acme/super-plugin']);
        $customer = User::factory()->create(['email' => 'buyer@example.com']);
        $license = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'user_id' => $customer->id,
            'price_paid' => 2000,
        ]);

        PluginPayout::factory()->create([
            'plugin_license_id' => $license->id,
            'gross_amount' => 2000,
            'platform_fee' => 600,
            'developer_amount' => 1400,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListPluginPayouts::class)
            ->assertSuccessful()
            ->assertSee('acme/super-plugin')
            ->assertSee('buyer@example.com')
            ->assertSee('$20.00')
            ->assertSee('$14.00');
    }

    public function test_view_page_renders_successfully(): void
    {
        $payout = PluginPayout::factory()->failed()->create();

        Livewire::actingAs($this->admin)
            ->test(ViewPluginPayout::class, ['record' => $payout->getRouteKey()])
            ->assertSuccessful();
    }

    public function test_view_page_shows_failure_reason(): void
    {
        $payout = PluginPayout::factory()->create([
            'status' => PayoutStatus::Failed,
            'failure_reason' => 'currency mismatch (usd != eur)',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ViewPluginPayout::class, ['record' => $payout->getRouteKey()])
            ->assertSuccessful()
            ->assertSee('currency mismatch');
    }

    public function test_attempts_relation_manager_lists_history(): void
    {
        $payout = PluginPayout::factory()->failed()->create();

        PluginPayoutAttempt::factory()->create([
            'plugin_payout_id' => $payout->id,
            'succeeded' => false,
            'error_message' => 'first error',
        ]);

        PluginPayoutAttempt::factory()->create([
            'plugin_payout_id' => $payout->id,
            'succeeded' => false,
            'error_message' => 'second error',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AttemptsRelationManager::class, [
                'ownerRecord' => $payout,
                'pageClass' => ViewPluginPayout::class,
            ])
            ->assertCanSeeTableRecords($payout->attempts()->get())
            ->assertCountTableRecords(2);
    }

    public function test_retry_action_dispatches_job_and_resets_status(): void
    {
        Queue::fake();

        $payout = PluginPayout::factory()->failed()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPluginPayouts::class)
            ->callTableAction('retryPayout', $payout);

        Queue::assertPushed(ProcessPayoutTransfer::class, function ($job) use ($payout) {
            return $job->payout->is($payout);
        });

        $this->assertEquals(PayoutStatus::Pending, $payout->fresh()->status);
    }

    public function test_retry_action_is_hidden_for_transferred_payouts(): void
    {
        $payout = PluginPayout::factory()->transferred()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPluginPayouts::class)
            ->assertTableActionHidden('retryPayout', $payout);
    }

    public function test_list_filters_by_status(): void
    {
        $failed = PluginPayout::factory()->failed()->create();
        $transferred = PluginPayout::factory()->transferred()->create();

        Livewire::actingAs($this->admin)
            ->test(ListPluginPayouts::class)
            ->filterTable('status', PayoutStatus::Failed->value)
            ->assertCanSeeTableRecords([$failed])
            ->assertCanNotSeeTableRecords([$transferred]);
    }
}
