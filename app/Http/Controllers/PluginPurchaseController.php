<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use App\Services\CartService;
use App\Services\GrandfatheringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Cashier\Cashier;

class PluginPurchaseController extends Controller
{
    public function __construct(
        protected CartService $cartService,
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

        if (! $plugin->activePrice) {
            return redirect()->route('plugins.show', $plugin)
                ->with('error', 'This plugin is not available for purchase.');
        }

        // Add plugin to cart and redirect to cart checkout
        $cart = $this->cartService->getCart($user);

        try {
            $this->cartService->addPlugin($cart, $plugin);
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('plugins.show', $plugin)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('cart.checkout');
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

        // Retrieve the checkout session to get the invoice ID
        try {
            $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);
            $invoiceId = $session->invoice;
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to verify purchase status.',
            ], 400);
        }

        if (! $invoiceId) {
            return response()->json([
                'status' => 'pending',
                'message' => 'Processing your purchase...',
            ]);
        }

        // Check if license exists for this invoice and plugin
        $license = $user->pluginLicenses()
            ->where('stripe_invoice_id', $invoiceId)
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
