<?php

namespace Tests\Feature\Filament;

use App\Enums\PluginType;
use App\Filament\Resources\ThirdPartySaleResource\Pages\ListThirdPartySales;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Models\User;
use App\Services\StripeConnectService;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Mockery\MockInterface;
use Stripe\LineItem;
use Stripe\Refund;
use Tests\TestCase;

class ThirdPartySaleResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    private function createThirdPartyPlugin(): Plugin
    {
        $developerAccount = DeveloperAccount::factory()->create();

        return Plugin::factory()->paid()->create([
            'is_official' => false,
            'user_id' => $developerAccount->user_id,
            'developer_account_id' => $developerAccount->id,
        ]);
    }

    public function test_list_page_renders_successfully(): void
    {
        $plugin = $this->createThirdPartyPlugin();
        PluginLicense::factory()->count(2)->create(['plugin_id' => $plugin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertSuccessful();
    }

    public function test_shows_sales_with_missing_payout(): void
    {
        $plugin = $this->createThirdPartyPlugin();
        $license = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'price_paid' => 5000,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$license])
            ->assertSee('Missing');
    }

    public function test_shows_payout_status_when_payout_exists(): void
    {
        $plugin = $this->createThirdPartyPlugin();
        $license = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);

        PluginPayout::factory()->transferred()->create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $plugin->developer_account_id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertSuccessful()
            ->assertSee('Transferred');
    }

    public function test_excludes_official_plugin_sales(): void
    {
        $officialPlugin = Plugin::factory()->paid()->create(['is_official' => true]);
        $officialSale = PluginLicense::factory()->create(['plugin_id' => $officialPlugin->id]);

        $thirdPartyPlugin = $this->createThirdPartyPlugin();
        $thirdPartySale = PluginLicense::factory()->create(['plugin_id' => $thirdPartyPlugin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertCanSeeTableRecords([$thirdPartySale])
            ->assertCanNotSeeTableRecords([$officialSale]);
    }

    public function test_excludes_free_plugin_sales(): void
    {
        $freePlugin = Plugin::factory()->create([
            'is_official' => false,
            'type' => PluginType::Free,
        ]);
        $freeSale = PluginLicense::factory()->create(['plugin_id' => $freePlugin->id]);

        $paidPlugin = $this->createThirdPartyPlugin();
        $paidSale = PluginLicense::factory()->create(['plugin_id' => $paidPlugin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertCanSeeTableRecords([$paidSale])
            ->assertCanNotSeeTableRecords([$freeSale]);
    }

    public function test_excludes_grandfathered_licenses(): void
    {
        $plugin = $this->createThirdPartyPlugin();
        $granted = PluginLicense::factory()->grandfathered()->create(['plugin_id' => $plugin->id]);
        $sale = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertCanSeeTableRecords([$sale])
            ->assertCanNotSeeTableRecords([$granted]);
    }

    public function test_totals_paid_and_due_to_seller(): void
    {
        $plugin = $this->createThirdPartyPlugin();

        $first = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'price_paid' => 2000,
        ]);
        PluginPayout::factory()->create([
            'plugin_license_id' => $first->id,
            'developer_account_id' => $plugin->developer_account_id,
            'gross_amount' => 2000,
            'platform_fee' => 600,
            'developer_amount' => 1400,
        ]);

        $second = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'price_paid' => 5000,
        ]);
        PluginPayout::factory()->create([
            'plugin_license_id' => $second->id,
            'developer_account_id' => $plugin->developer_account_id,
            'gross_amount' => 5000,
            'platform_fee' => 1500,
            'developer_amount' => 3500,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertSuccessful()
            ->assertTableColumnSummarySet('price_paid', 'sum', 7000)
            ->assertTableColumnSummarySet('payout.developer_amount', 'sum', 4900)
            ->assertSee('$70.00')
            ->assertSee('$49.00');
    }

    public function test_filters_sales_missing_payouts(): void
    {
        $plugin = $this->createThirdPartyPlugin();

        $missingPayout = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'price_paid' => 5000,
        ]);

        $withPayout = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);
        PluginPayout::factory()->create([
            'plugin_license_id' => $withPayout->id,
            'developer_account_id' => $plugin->developer_account_id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->filterTable('missing_payout', true)
            ->assertCanSeeTableRecords([$missingPayout])
            ->assertCanNotSeeTableRecords([$withPayout]);
    }

    public function test_refunded_sale_shows_refund_details_and_refunded_payout(): void
    {
        $plugin = $this->createThirdPartyPlugin();
        $refunder = User::factory()->create(['email' => 'refunder@test.com']);

        $license = PluginLicense::factory()->refunded()->create([
            'plugin_id' => $plugin->id,
            'refunded_by' => $refunder->id,
        ]);
        PluginPayout::factory()->cancelled()->create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $plugin->developer_account_id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertCanSeeTableRecords([$license])
            ->assertTableColumnStateSet('payout_status', 'Refunded', $license)
            ->assertSee('by refunder@test.com');
    }

    public function test_filters_refunded_sales(): void
    {
        $plugin = $this->createThirdPartyPlugin();

        $refunded = PluginLicense::factory()->refunded()->create(['plugin_id' => $plugin->id]);
        $kept = PluginLicense::factory()->create(['plugin_id' => $plugin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->filterTable('refunded_at', true)
            ->assertCanSeeTableRecords([$refunded])
            ->assertCanNotSeeTableRecords([$kept]);
    }

    public function test_refund_action_is_only_visible_for_refundable_sales(): void
    {
        $plugin = $this->createThirdPartyPlugin();

        $refundable = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'purchased_at' => now()->subDays(3),
        ]);
        $tooOld = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'purchased_at' => now()->subDays(20),
        ]);
        $alreadyRefunded = PluginLicense::factory()->refunded()->create(['plugin_id' => $plugin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->assertTableActionVisible('refund', $refundable)
            ->assertTableActionHidden('refund', $tooOld)
            ->assertTableActionHidden('refund', $alreadyRefunded);
    }

    public function test_refund_confirmation_explains_what_will_be_refunded(): void
    {
        $plugin = $this->createThirdPartyPlugin();
        $license = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'purchased_at' => now()->subDays(3),
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->mountTableAction('refund', $license)
            ->assertMountedActionModalSee("This will refund {$license->user->email} what they paid for {$plugin->name}, after any coupon or tax, and revoke their license.");
    }

    public function test_refund_action_reports_the_amount_refunded(): void
    {
        $plugin = $this->createThirdPartyPlugin();
        $license = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'price_paid' => 2900,
            'purchased_at' => now()->subDays(3),
        ]);

        $this->mock(StripeConnectService::class, function (MockInterface $mock) use ($license, $plugin) {
            $mock->shouldReceive('checkoutLineItems')
                ->with($license->stripe_payment_intent_id)
                ->andReturn(collect([
                    LineItem::constructFrom([
                        'amount_subtotal' => 2900,
                        'amount_discount' => 580,
                        'amount_tax' => 464,
                        'amount_total' => 2784,
                        'description' => $plugin->name,
                        'price' => ['product' => ['id' => 'prod_test_123', 'metadata' => ['plugin_id' => (string) $plugin->id]]],
                    ]),
                ]));
            $mock->shouldReceive('refundCheckoutLineItem')
                ->once()
                ->with($license->stripe_payment_intent_id, Mockery::on(fn (LineItem $lineItem): bool => $lineItem->amount_total === 2784))
                ->andReturn(Refund::constructFrom(['id' => 're_test_refund_123']));
        });

        Livewire::actingAs($this->admin)
            ->test(ListThirdPartySales::class)
            ->callTableAction('refund', $license)
            ->assertNotified(
                Notification::make()
                    ->title('Purchase refunded successfully')
                    ->body('Refunded $27.84.')
                    ->success(),
            );

        $license->refresh();

        $this->assertTrue($license->isRefunded());
        $this->assertSame('re_test_refund_123', $license->stripe_refund_id);
        $this->assertSame($this->admin->id, $license->refunded_by);
    }
}
