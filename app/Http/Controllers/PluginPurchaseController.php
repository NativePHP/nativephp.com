<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use App\Services\StripeConnectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PluginPurchaseController extends Controller
{
    public function __construct(
        protected StripeConnectService $stripeConnectService
    ) {}

    public function show(Request $request, string $vendor, string $package): View|RedirectResponse
    {
        $plugin = Plugin::findByVendorPackageOrFail($vendor, $package);

        if ($plugin->isFree()) {
            return redirect()->route('plugins.show', $this->pluginRouteParams($plugin));
        }

        $user = $request->user();
        $bestPrice = $plugin->getBestPriceForUser($user);
        $regularPrice = $plugin->getRegularPrice();

        if (! $bestPrice) {
            return redirect()->route('plugins.show', $this->pluginRouteParams($plugin))
                ->with('error', 'This plugin is not available for purchase.');
        }

        return view('plugins.purchase', [
            'plugin' => $plugin,
            'price' => $bestPrice,
            'regularPrice' => $regularPrice,
            'hasDiscount' => $regularPrice && $bestPrice->id !== $regularPrice->id,
        ]);
    }

    public function checkout(Request $request, string $vendor, string $package): RedirectResponse
    {
        $plugin = Plugin::findByVendorPackageOrFail($vendor, $package);
        $user = $request->user();

        if ($plugin->isFree()) {
            return redirect()->route('plugins.show', $this->pluginRouteParams($plugin));
        }

        $bestPrice = $plugin->getBestPriceForUser($user);

        if (! $bestPrice) {
            return redirect()->route('plugins.show', $this->pluginRouteParams($plugin))
                ->with('error', 'This plugin is not available for purchase.');
        }

        try {
            $session = $this->stripeConnectService->createCheckoutSession($bestPrice, $user);

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Plugin checkout failed', [
                'plugin_id' => $plugin->id,
                'user_id' => $user->id,
                'price_id' => $bestPrice->id,
                'price_tier' => $bestPrice->tier->value,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('plugins.purchase.show', $this->pluginRouteParams($plugin))
                ->with('error', 'Unable to start checkout. Please try again.');
        }
    }

    public function success(Request $request, string $vendor, string $package): View|RedirectResponse
    {
        $plugin = Plugin::findByVendorPackageOrFail($vendor, $package);
        $sessionId = $request->query('session_id');

        // Validate session ID exists and looks like a real Stripe session ID
        if (! $sessionId || ! str_starts_with($sessionId, 'cs_')) {
            return redirect()->route('plugins.show', $this->pluginRouteParams($plugin))
                ->with('error', 'Invalid checkout session. Please try again.');
        }

        return view('plugins.purchase-success', [
            'plugin' => $plugin,
            'sessionId' => $sessionId,
        ]);
    }

    public function status(Request $request, string $vendor, string $package, string $sessionId): JsonResponse
    {
        $plugin = Plugin::findByVendorPackageOrFail($vendor, $package);
        $user = $request->user();

        // Check if license exists for this checkout session and plugin
        $license = $user->pluginLicenses()
            ->where('stripe_checkout_session_id', $sessionId)
            ->where('plugin_id', $plugin->id)
            ->first();

        if (! $license) {
            return response()->json([
                'status' => 'pending',
                'message' => 'Processing your purchase...',
            ]);
        }

        return response()->json([
            'status' => 'complete',
            'message' => 'Purchase complete!',
            'plugin_name' => $plugin->name,
        ]);
    }

    public function cancel(Request $request, string $vendor, string $package): RedirectResponse
    {
        $plugin = Plugin::findByVendorPackageOrFail($vendor, $package);

        return redirect()->route('plugins.show', $this->pluginRouteParams($plugin))
            ->with('message', 'Purchase cancelled.');
    }

    /**
     * Get route parameters for a plugin's vendor/package URL.
     *
     * @return array{vendor: string, package: string}
     */
    protected function pluginRouteParams(Plugin $plugin): array
    {
        [$vendor, $package] = explode('/', $plugin->name);

        return ['vendor' => $vendor, 'package' => $package];
    }
}
