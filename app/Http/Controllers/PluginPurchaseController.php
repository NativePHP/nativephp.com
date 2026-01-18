<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use App\Services\GrandfatheringService;
use App\Services\StripeConnectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PluginPurchaseController extends Controller
{
    public function __construct(
        protected StripeConnectService $stripeConnectService,
        protected GrandfatheringService $grandfatheringService
    ) {}

    public function show(Request $request, Plugin $plugin): View|RedirectResponse
    {
        if ($plugin->isFree()) {
            return redirect()->route('plugins.show', $plugin);
        }

        $user = $request->user();
        $activePrice = $plugin->activePrice;

        if (! $activePrice) {
            return redirect()->route('plugins.show', $plugin)
                ->with('error', 'This plugin is not available for purchase.');
        }

        $discountPercent = $this->grandfatheringService->getApplicableDiscount($user, $plugin->is_official);
        $discountedAmount = $activePrice->getDiscountedAmount($discountPercent);

        return view('plugins.purchase', [
            'plugin' => $plugin,
            'price' => $activePrice,
            'discountPercent' => $discountPercent,
            'discountedAmount' => $discountedAmount,
            'originalAmount' => $activePrice->amount,
        ]);
    }

    public function checkout(Request $request, Plugin $plugin): RedirectResponse
    {
        $user = $request->user();

        if ($plugin->isFree()) {
            return redirect()->route('plugins.show', $plugin);
        }

        $activePrice = $plugin->activePrice;

        if (! $activePrice) {
            return redirect()->route('plugins.show', $plugin)
                ->with('error', 'This plugin is not available for purchase.');
        }

        try {
            $session = $this->stripeConnectService->createCheckoutSession($activePrice, $user);

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Plugin checkout failed', [
                'plugin_id' => $plugin->id,
                'user_id' => $user->id,
                'price_id' => $activePrice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('plugins.purchase.show', $plugin)
                ->with('error', 'Unable to start checkout. Please try again.');
        }
    }

    public function success(Request $request, Plugin $plugin): View|RedirectResponse
    {
        $sessionId = $request->query('session_id');

        // Validate session ID exists and looks like a real Stripe session ID
        if (! $sessionId || ! str_starts_with($sessionId, 'cs_')) {
            return redirect()->route('plugins.show', $plugin)
                ->with('error', 'Invalid checkout session. Please try again.');
        }

        return view('plugins.purchase-success', [
            'plugin' => $plugin,
            'sessionId' => $sessionId,
        ]);
    }

    public function status(Request $request, Plugin $plugin, string $sessionId): JsonResponse
    {
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

    public function cancel(Request $request, Plugin $plugin): RedirectResponse
    {
        return redirect()->route('plugins.show', $plugin)
            ->with('message', 'Purchase cancelled.');
    }
}
