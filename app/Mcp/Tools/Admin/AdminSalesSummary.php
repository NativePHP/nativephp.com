<?php

namespace App\Mcp\Tools\Admin;

use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Models\License;
use App\Models\Plugin;
use App\Models\PluginLicense;
use App\Models\ProductLicense;
use App\Models\Sale;
use Carbon\CarbonInterface;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Subscription;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('admin-sales-summary')]
#[Description('Read-only attributable inbound revenue: Ultra (Cashier) subscriptions + plugin + first-party product sales from sales_view, plus Anystack NativePHP license counts. Never returns raw license keys or Stripe secrets.')]
#[IsReadOnly]
class AdminSalesSummary extends Tool
{
    use RequiresAdmin;

    private const BY_PRODUCT_LIMIT = 50;

    public function handle(Request $request): Response
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $validated = $request->validate([
            'days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'all_time' => ['nullable', 'boolean'],
        ]);

        $allTime = (bool) ($validated['all_time'] ?? false);
        $days = $allTime ? null : (int) ($validated['days'] ?? 30);
        $since = $allTime ? null : now()->subDays($days);

        $pluginPaid = $this->aggregatePluginLicenses($since, paidOnly: true);
        $productPaid = $this->aggregateProductLicenses($since, paidOnly: true);
        $pluginCompedCount = $this->countPluginComps($since);
        $productCompedCount = $this->countProductComps($since);

        $ultra = $this->ultraSummary($since);

        $breakdown = $this->buildBreakdown($pluginPaid, $productPaid, $ultra);
        $totalRevenueCents = $this->sumPreferredCurrency($breakdown);

        $pluginRevenue = $pluginPaid->map(fn (array $row): array => [
            'currency' => $row['currency'],
            'licenses_count' => $row['sales_count'],
            'revenue_cents' => $row['revenue_cents'],
        ])->values()->all();

        $topPlugins = $this->topPlugins($since);
        $byProduct = $this->byProduct($since);

        $nativeLicensesQuery = License::query();
        if ($since !== null) {
            $nativeLicensesQuery->where('created_at', '>=', $since);
        }

        $nativeLicenses = $nativeLicensesQuery
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
            'all_time' => $allTime,
            'since' => $since?->toIso8601String(),
            'total_revenue_cents' => $totalRevenueCents,
            'breakdown' => $breakdown,
            'by_product' => $byProduct,
            'plugin_license_revenue' => $pluginRevenue,
            'top_plugins' => $topPlugins,
            'ultra' => $ultra,
            'nativephp_licenses_created' => $nativeLicenses,
            'active_nativephp_licenses' => License::query()->whereActive()->count(),
            'comped' => [
                'plugin_count' => $pluginCompedCount,
                'product_count' => $productCompedCount,
                'ultra_active_count' => $ultra['active_comped_count'],
            ],
            'note' => implode(' ', [
                'Amounts are in cents (prefer USD when summing total_revenue_cents across currencies).',
                'sales_view unions plugin_licenses + product_licenses (one-time sales); paid revenue excludes is_comped / $0 rows (comps counted separately).',
                'Ultra is Laravel Cashier subscriptions on Max/Ultra Stripe prices (plan key max, display name Ultra) — not an Anystack policy.',
                'Ultra revenue_cents_in_window sums subscriptions.price_paid for Ultra subs created in the window with price_paid > 0; HandleInvoicePaidJob overwrites price_paid with the latest invoice total (not cumulative), so renewals do not create new rows and window revenue under-counts renewals — same limitation as Filament SubscriberIncomeChart.',
                'License keys and Stripe secrets are intentionally omitted.',
            ]),
        ]));
    }

    /**
     * @return list<string>
     */
    protected function ultraPayingPriceIds(): array
    {
        return array_values(array_filter([
            config('subscriptions.plans.max.stripe_price_id'),
            config('subscriptions.plans.max.stripe_price_id_monthly'),
            config('subscriptions.plans.max.stripe_price_id_eap'),
            config('subscriptions.plans.max.stripe_price_id_discounted'),
        ]));
    }

    protected function ultraCompedPriceId(): ?string
    {
        $id = config('subscriptions.plans.max.stripe_price_id_comped');

        return is_string($id) && $id !== '' ? $id : null;
    }

    /**
     * @return list<string>
     */
    protected function allUltraPriceIds(): array
    {
        return array_values(array_filter([
            ...$this->ultraPayingPriceIds(),
            $this->ultraCompedPriceId(),
        ]));
    }

    /**
     * @param  Builder<Subscription>  $query
     * @param  list<string>  $priceIds
     * @return Builder<Subscription>
     */
    protected function whereHasUltraPrice(Builder $query, array $priceIds): Builder
    {
        if ($priceIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $inner) use ($priceIds): void {
            $inner->whereIn('stripe_price', $priceIds)
                ->orWhereHas('items', fn (Builder $items) => $items->whereIn('stripe_price', $priceIds));
        });
    }

    /**
     * @return array{
     *     active_count: int,
     *     active_comped_count: int,
     *     created_in_window: int,
     *     paid_created_in_window: int,
     *     revenue_cents_in_window: int,
     *     currency: string,
     *     caveat: string
     * }
     */
    protected function ultraSummary(?CarbonInterface $since): array
    {
        $payingIds = $this->ultraPayingPriceIds();
        $compedId = $this->ultraCompedPriceId();
        $allIds = $this->allUltraPriceIds();

        $activePaying = Subscription::query()
            ->active()
            ->where(function (Builder $q): void {
                $q->where('is_comped', false)->orWhereNull('is_comped');
            });
        $this->whereHasUltraPrice($activePaying, $payingIds);

        $activeCompedCount = 0;
        if ($compedId) {
            $activeComped = Subscription::query()->active();
            $this->whereHasUltraPrice($activeComped, [$compedId]);
            $activeCompedCount = $activeComped->count();
        }

        $createdQuery = Subscription::query();
        $this->whereHasUltraPrice($createdQuery, $allIds);
        if ($since !== null) {
            $createdQuery->where('created_at', '>=', $since);
        }
        $createdInWindow = (clone $createdQuery)->count();

        $revenueQuery = Subscription::query()
            ->whereNotNull('price_paid')
            ->where('price_paid', '>', 0)
            ->where(function (Builder $q): void {
                $q->where('is_comped', false)->orWhereNull('is_comped');
            });
        $this->whereHasUltraPrice($revenueQuery, $payingIds);
        if ($since !== null) {
            $revenueQuery->where('created_at', '>=', $since);
        }
        $revenueCents = (int) (clone $revenueQuery)->sum('price_paid');
        $paidSalesCount = (clone $revenueQuery)->count();

        return [
            'active_count' => $activePaying->count(),
            'active_comped_count' => $activeCompedCount,
            'created_in_window' => $createdInWindow,
            'paid_created_in_window' => $paidSalesCount,
            'revenue_cents_in_window' => $revenueCents,
            'currency' => 'USD',
            'caveat' => 'price_paid is overwritten on each paid invoice (not cumulative); renewals do not add rows. Window revenue only includes Ultra/Max subs created in-range with price_paid > 0.',
        ];
    }

    /**
     * @return Collection<int, array{currency: string, sales_count: int, revenue_cents: int}>
     */
    protected function aggregatePluginLicenses(?CarbonInterface $since, bool $paidOnly): Collection
    {
        $query = PluginLicense::query();
        if ($since !== null) {
            $query->where('purchased_at', '>=', $since);
        }
        if ($paidOnly) {
            $query->where('is_grandfathered', false)->where('price_paid', '>', 0);
        }

        return $query
            ->selectRaw('currency, COUNT(*) as sales_count, COALESCE(SUM(price_paid), 0) as revenue_cents')
            ->groupBy('currency')
            ->get()
            ->map(fn ($row): array => [
                'currency' => $row->currency ?: 'USD',
                'sales_count' => (int) $row->sales_count,
                'revenue_cents' => (int) $row->revenue_cents,
            ]);
    }

    /**
     * @return Collection<int, array{currency: string, sales_count: int, revenue_cents: int}>
     */
    protected function aggregateProductLicenses(?CarbonInterface $since, bool $paidOnly): Collection
    {
        $query = ProductLicense::query();
        if ($since !== null) {
            $query->where('purchased_at', '>=', $since);
        }
        if ($paidOnly) {
            $query->where('is_comped', false)->where('price_paid', '>', 0);
        }

        return $query
            ->selectRaw('currency, COUNT(*) as sales_count, COALESCE(SUM(price_paid), 0) as revenue_cents')
            ->groupBy('currency')
            ->get()
            ->map(fn ($row): array => [
                'currency' => $row->currency ?: 'USD',
                'sales_count' => (int) $row->sales_count,
                'revenue_cents' => (int) $row->revenue_cents,
            ]);
    }

    protected function countPluginComps(?CarbonInterface $since): int
    {
        $query = PluginLicense::query()->where(function (Builder $q): void {
            $q->where('is_grandfathered', true)->orWhere('price_paid', '<=', 0);
        });
        if ($since !== null) {
            $query->where('purchased_at', '>=', $since);
        }

        return $query->count();
    }

    protected function countProductComps(?CarbonInterface $since): int
    {
        $query = ProductLicense::query()->where(function (Builder $q): void {
            $q->where('is_comped', true)->orWhere('price_paid', '<=', 0);
        });
        if ($since !== null) {
            $query->where('purchased_at', '>=', $since);
        }

        return $query->count();
    }

    /**
     * @param  Collection<int, array{currency: string, sales_count: int, revenue_cents: int}>  $pluginPaid
     * @param  Collection<int, array{currency: string, sales_count: int, revenue_cents: int}>  $productPaid
     * @param  array{revenue_cents_in_window: int, currency: string, caveat: string, created_in_window: int, paid_created_in_window: int}  $ultra
     * @return list<array{source: string, revenue_cents: int, sales_count: int, currency: string, caveat?: string}>
     */
    protected function buildBreakdown(Collection $pluginPaid, Collection $productPaid, array $ultra): array
    {
        $rows = [];

        foreach ($pluginPaid as $row) {
            $rows[] = [
                'source' => 'plugins',
                'revenue_cents' => $row['revenue_cents'],
                'sales_count' => $row['sales_count'],
                'currency' => $row['currency'],
            ];
        }

        if ($pluginPaid->isEmpty()) {
            $rows[] = [
                'source' => 'plugins',
                'revenue_cents' => 0,
                'sales_count' => 0,
                'currency' => 'USD',
            ];
        }

        foreach ($productPaid as $row) {
            $rows[] = [
                'source' => 'products',
                'revenue_cents' => $row['revenue_cents'],
                'sales_count' => $row['sales_count'],
                'currency' => $row['currency'],
            ];
        }

        if ($productPaid->isEmpty()) {
            $rows[] = [
                'source' => 'products',
                'revenue_cents' => 0,
                'sales_count' => 0,
                'currency' => 'USD',
            ];
        }

        $rows[] = [
            'source' => 'ultra_subscriptions',
            'revenue_cents' => $ultra['revenue_cents_in_window'],
            'sales_count' => $ultra['paid_created_in_window'],
            'currency' => $ultra['currency'],
            'caveat' => $ultra['caveat'],
        ];

        return $rows;
    }

    /**
     * @param  list<array{source: string, revenue_cents: int, sales_count: int, currency: string}>  $breakdown
     */
    protected function sumPreferredCurrency(array $breakdown): int
    {
        $byCurrency = collect($breakdown)->groupBy('currency');

        if ($byCurrency->has('USD')) {
            return (int) $byCurrency->get('USD')->sum('revenue_cents');
        }

        if ($byCurrency->count() === 1) {
            return (int) $byCurrency->first()->sum('revenue_cents');
        }

        return (int) collect($breakdown)->sum('revenue_cents');
    }

    /**
     * @return list<array{plugin_id: int, plugin: ?string, status: ?string, licenses_count: int, revenue_cents: int}>
     */
    protected function topPlugins(?CarbonInterface $since): array
    {
        $query = PluginLicense::query()
            ->where('is_grandfathered', false)
            ->where('price_paid', '>', 0);
        if ($since !== null) {
            $query->where('purchased_at', '>=', $since);
        }

        $topPluginRows = $query
            ->select('plugin_id', DB::raw('COUNT(*) as licenses_count'), DB::raw('COALESCE(SUM(price_paid), 0) as revenue_cents'))
            ->groupBy('plugin_id')
            ->orderByDesc('licenses_count')
            ->limit(10)
            ->get();

        $plugins = Plugin::query()
            ->whereIn('id', $topPluginRows->pluck('plugin_id'))
            ->get(['id', 'name', 'status'])
            ->keyBy('id');

        return $topPluginRows->map(function ($row) use ($plugins): array {
            $plugin = $plugins->get($row->plugin_id);

            return [
                'plugin_id' => $row->plugin_id,
                'plugin' => $plugin?->name,
                'status' => $plugin?->status?->value,
                'licenses_count' => (int) $row->licenses_count,
                'revenue_cents' => (int) $row->revenue_cents,
            ];
        })->all();
    }

    /**
     * @return list<array{product_name: ?string, source: string, revenue_cents: int, sales_count: int, currency: string}>
     */
    protected function byProduct(?CarbonInterface $since): array
    {
        $query = Sale::query()
            ->where(function (Builder $q): void {
                $q->where('is_comped', false)->orWhereNull('is_comped');
            })
            ->where('price_paid', '>', 0);

        if ($since !== null) {
            $query->where('purchased_at', '>=', $since);
        }

        // sales_view ids are pl_{id} (plugins) or pr_{id} (products)
        $sourceExpression = "CASE WHEN id LIKE 'pl_%' THEN 'plugins' ELSE 'products' END";

        return $query
            ->selectRaw("product_name, currency, {$sourceExpression} as source, COUNT(*) as sales_count, COALESCE(SUM(price_paid), 0) as revenue_cents")
            ->groupByRaw("product_name, currency, {$sourceExpression}")
            ->orderByDesc('revenue_cents')
            ->limit(self::BY_PRODUCT_LIMIT)
            ->get()
            ->map(fn ($row): array => [
                'product_name' => $row->product_name,
                'source' => $row->source,
                'revenue_cents' => (int) $row->revenue_cents,
                'sales_count' => (int) $row->sales_count,
                'currency' => $row->currency ?: 'USD',
            ])
            ->all();
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'days' => $schema->integer()->description('Lookback window in days (default 30, max 3650). Ignored when all_time is true.'),
            'all_time' => $schema->boolean()->description('When true, ignore days/since and include all attributable revenue.'),
        ];
    }
}
