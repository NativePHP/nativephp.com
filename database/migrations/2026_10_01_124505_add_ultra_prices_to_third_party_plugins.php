<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Third-party plugins that already have a pricing tier get the Ultra price
     * for that tier. From here on the model adds it whenever a tier is set.
     */
    public function up(): void
    {
        $ultraPrices = [
            'bronze' => 2000,
            'silver' => 3500,
            'gold' => 7000,
        ];

        foreach ($ultraPrices as $tier => $amount) {
            DB::table('plugins')
                ->where('is_official', false)
                ->where('tier', $tier)
                ->whereNotExists(fn (Builder $query) => $query
                    ->from('plugin_prices')
                    ->whereColumn('plugin_prices.plugin_id', 'plugins.id')
                    ->where('plugin_prices.tier', 'ultra'))
                ->pluck('id')
                ->each(fn (int $pluginId) => DB::table('plugin_prices')->insert([
                    'plugin_id' => $pluginId,
                    'tier' => 'ultra',
                    'amount' => $amount,
                    'currency' => 'USD',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('plugin_prices')->where('tier', 'ultra')->delete();
    }
};
