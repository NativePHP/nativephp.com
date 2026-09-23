<?php

namespace App\Console\Commands;

use App\Enums\PayoutStatus;
use App\Enums\PluginType;
use App\Models\PluginLicense;
use App\Models\PluginPayout;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class BackfillMissingPayouts extends Command
{
    protected $signature = 'payouts:backfill {--dry-run : List the payouts that would be created without creating them}';

    protected $description = 'Create missing payout records for paid third-party plugin sales';

    public function handle(): int
    {
        $licenses = PluginLicense::query()
            ->whereDoesntHave('payout')
            ->where('is_grandfathered', false)
            ->where('price_paid', '>', 0)
            ->whereHas('plugin', function (Builder $query): void {
                $query->where('is_official', false)
                    ->where('type', PluginType::Paid)
                    ->whereNotNull('developer_account_id');
            })
            ->with('plugin.developerAccount')
            ->get();

        if ($licenses->isEmpty()) {
            $this->info('No sales are missing payout records.');

            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        $created = 0;

        foreach ($licenses as $license) {
            $developerAccount = $license->plugin->developerAccount;

            $split = PluginPayout::calculateSplit($license->price_paid, $developerAccount->platformFeePercent());

            $status = $developerAccount->canReceivePayouts()
                ? PayoutStatus::Pending
                : PayoutStatus::Held;

            $eligibleAt = $license->purchased_at?->clone()->addDays(15) ?? now();

            $this->line(sprintf(
                '%sLicense #%d (%s): gross $%s, developer $%s, status %s',
                $dryRun ? '[dry-run] ' : '',
                $license->id,
                $license->plugin->name,
                number_format($license->price_paid / 100, 2),
                number_format($split['developer_amount'] / 100, 2),
                $status->value,
            ));

            if ($dryRun) {
                continue;
            }

            PluginPayout::create([
                'plugin_license_id' => $license->id,
                'developer_account_id' => $developerAccount->id,
                'gross_amount' => $license->price_paid,
                'platform_fee' => $split['platform_fee'],
                'developer_amount' => $split['developer_amount'],
                'status' => $status,
                'eligible_for_payout_at' => $eligibleAt,
            ]);

            $created++;

            Log::info('Backfilled missing payout', [
                'plugin_license_id' => $license->id,
                'developer_account_id' => $developerAccount->id,
                'status' => $status->value,
            ]);
        }

        $this->info($dryRun
            ? "Found {$licenses->count()} sale(s) missing payout records."
            : "Created {$created} payout record(s).");

        return self::SUCCESS;
    }
}
