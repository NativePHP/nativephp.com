<?php

namespace App\Mcp\Tools\Admin;

use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Models\License;
use App\Models\Plugin;
use App\Models\PluginLicense;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('admin-sales-summary')]
#[Description('Read-only sales/license summaries: plugin license revenue and NativePHP license counts. Never returns raw license keys or Stripe secrets.')]
#[IsReadOnly]
class AdminSalesSummary extends Tool
{
    use RequiresAdmin;

    public function handle(Request $request): Response
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $validated = $request->validate([
            'days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ]);

        $days = (int) ($validated['days'] ?? 30);
        $since = now()->subDays($days);

        $pluginRevenue = PluginLicense::query()
            ->where('purchased_at', '>=', $since)
            ->selectRaw('currency, COUNT(*) as licenses_count, COALESCE(SUM(price_paid), 0) as revenue_cents')
            ->groupBy('currency')
            ->get()
            ->map(fn ($row): array => [
                'currency' => $row->currency,
                'licenses_count' => (int) $row->licenses_count,
                'revenue_cents' => (int) $row->revenue_cents,
            ])
            ->all();

        $topPluginRows = PluginLicense::query()
            ->where('purchased_at', '>=', $since)
            ->select('plugin_id', DB::raw('COUNT(*) as licenses_count'), DB::raw('COALESCE(SUM(price_paid), 0) as revenue_cents'))
            ->groupBy('plugin_id')
            ->orderByDesc('licenses_count')
            ->limit(10)
            ->get();

        $plugins = Plugin::query()
            ->whereIn('id', $topPluginRows->pluck('plugin_id'))
            ->get(['id', 'name', 'status'])
            ->keyBy('id');

        $topPlugins = $topPluginRows->map(function ($row) use ($plugins): array {
            $plugin = $plugins->get($row->plugin_id);

            return [
                'plugin_id' => $row->plugin_id,
                'plugin' => $plugin?->name,
                'status' => $plugin?->status?->value,
                'licenses_count' => (int) $row->licenses_count,
                'revenue_cents' => (int) $row->revenue_cents,
            ];
        })->all();

        $nativeLicenses = License::query()
            ->where('created_at', '>=', $since)
            ->selectRaw('policy_name, COUNT(*) as count')
            ->groupBy('policy_name')
            ->orderByDesc('count')
            ->get()
            ->map(fn ($row): array => [
                'policy_name' => $row->policy_name,
                'count' => (int) $row->count,
            ])
            ->all();

        return Response::text($this->toJson([
            'days' => $days,
            'since' => $since->toIso8601String(),
            'plugin_license_revenue' => $pluginRevenue,
            'top_plugins' => $topPlugins,
            'nativephp_licenses_created' => $nativeLicenses,
            'active_nativephp_licenses' => License::query()->whereActive()->count(),
            'note' => 'Amounts are in cents. License keys and Stripe secrets are intentionally omitted.',
        ]));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'days' => $schema->integer()->description('Lookback window in days (default 30, max 365).'),
        ];
    }
}
