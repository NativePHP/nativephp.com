<?php

namespace Tests\Feature\Notifications;

use App\Enums\PayoutStatus;
use App\Models\DeveloperAccount;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use App\Models\User;
use App\Notifications\PluginSaleCompleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PluginSaleCompletedTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_lists_plugin_names_and_payout_amounts(): void
    {
        $developer = User::factory()->create();
        $developerAccount = DeveloperAccount::factory()
            ->withAcceptedTerms()
            ->create(['user_id' => $developer->id]);

        $plugin = Plugin::factory()->paid()->create([
            'user_id' => $developer->id,
            'developer_account_id' => $developerAccount->id,
            'name' => 'acme/camera-plugin',
        ]);

        $license = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'stripe_invoice_id' => 'in_test_123',
            'price_paid' => 2900,
        ]);

        $payout = PluginPayout::create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 2900,
            'platform_fee' => 870,
            'developer_amount' => 2030,
            'status' => PayoutStatus::Pending,
        ]);

        $notification = new PluginSaleCompleted(collect([$payout->load('pluginLicense.plugin')]));
        $rendered = $notification->toMail($developer)->render()->toHtml();

        $this->assertStringContainsString('acme/camera-plugin', $rendered);
        $this->assertStringContainsString('$20.30', $rendered);
        $this->assertStringContainsString('Total payout: $20.30', $rendered);

        $mail = $notification->toMail($developer);
        $this->assertEquals("You've made a sale!", $mail->subject);
    }

    public function test_email_lists_multiple_plugins_with_correct_total(): void
    {
        $developer = User::factory()->create();
        $developerAccount = DeveloperAccount::factory()
            ->withAcceptedTerms()
            ->create(['user_id' => $developer->id]);

        $plugin1 = Plugin::factory()->paid()->create([
            'user_id' => $developer->id,
            'developer_account_id' => $developerAccount->id,
            'name' => 'acme/camera-plugin',
        ]);

        $plugin2 = Plugin::factory()->paid()->create([
            'user_id' => $developer->id,
            'developer_account_id' => $developerAccount->id,
            'name' => 'acme/gps-plugin',
        ]);

        $license1 = PluginLicense::factory()->create([
            'plugin_id' => $plugin1->id,
            'stripe_invoice_id' => 'in_test_456',
            'price_paid' => 2900,
        ]);

        $license2 = PluginLicense::factory()->create([
            'plugin_id' => $plugin2->id,
            'stripe_invoice_id' => 'in_test_456',
            'price_paid' => 4900,
        ]);

        $payout1 = PluginPayout::create([
            'plugin_license_id' => $license1->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 2900,
            'platform_fee' => 870,
            'developer_amount' => 2030,
            'status' => PayoutStatus::Pending,
        ]);

        $payout2 = PluginPayout::create([
            'plugin_license_id' => $license2->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 4900,
            'platform_fee' => 1470,
            'developer_amount' => 3430,
            'status' => PayoutStatus::Pending,
        ]);

        $payout1->load('pluginLicense.plugin');
        $payout2->load('pluginLicense.plugin');
        $payouts = collect([$payout1, $payout2]);

        $notification = new PluginSaleCompleted($payouts);
        $rendered = $notification->toMail($developer)->render()->toHtml();

        $this->assertStringContainsString('acme/camera-plugin', $rendered);
        $this->assertStringContainsString('$20.30', $rendered);
        $this->assertStringContainsString('acme/gps-plugin', $rendered);
        $this->assertStringContainsString('$34.30', $rendered);
        $this->assertStringContainsString('Total payout: $54.60', $rendered);
    }

    public function test_email_does_not_contain_buyer_information(): void
    {
        $buyer = User::factory()->create([
            'name' => 'BuyerFirstName BuyerLastName',
            'email' => 'buyer@example.com',
        ]);

        $developer = User::factory()->create();
        $developerAccount = DeveloperAccount::factory()
            ->withAcceptedTerms()
            ->create(['user_id' => $developer->id]);

        $plugin = Plugin::factory()->paid()->create([
            'user_id' => $developer->id,
            'developer_account_id' => $developerAccount->id,
            'name' => 'acme/test-plugin',
        ]);

        $license = PluginLicense::factory()->create([
            'user_id' => $buyer->id,
            'plugin_id' => $plugin->id,
            'stripe_invoice_id' => 'in_test_789',
            'price_paid' => 2900,
        ]);

        $payout = PluginPayout::create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 2900,
            'platform_fee' => 870,
            'developer_amount' => 2030,
            'status' => PayoutStatus::Pending,
        ]);

        $notification = new PluginSaleCompleted(collect([$payout->load('pluginLicense.plugin')]));
        $rendered = $notification->toMail($developer)->render()->toHtml();

        $this->assertStringNotContainsString('BuyerFirstName', $rendered);
        $this->assertStringNotContainsString('BuyerLastName', $rendered);
        $this->assertStringNotContainsString('buyer@example.com', $rendered);
    }

    public function test_toarray_contains_payout_ids_and_total(): void
    {
        $developer = User::factory()->create();
        $developerAccount = DeveloperAccount::factory()
            ->withAcceptedTerms()
            ->create(['user_id' => $developer->id]);

        $plugin = Plugin::factory()->paid()->create([
            'user_id' => $developer->id,
            'developer_account_id' => $developerAccount->id,
        ]);

        $license = PluginLicense::factory()->create([
            'plugin_id' => $plugin->id,
            'stripe_invoice_id' => 'in_test_arr',
            'price_paid' => 2900,
        ]);

        $payout = PluginPayout::create([
            'plugin_license_id' => $license->id,
            'developer_account_id' => $developerAccount->id,
            'gross_amount' => 2900,
            'platform_fee' => 870,
            'developer_amount' => 2030,
            'status' => PayoutStatus::Pending,
        ]);

        $notification = new PluginSaleCompleted(collect([$payout]));
        $array = $notification->toArray($developer);

        $this->assertEquals([$payout->id], $array['payout_ids']);
        $this->assertEquals(2030, $array['total_developer_amount']);
    }
}
