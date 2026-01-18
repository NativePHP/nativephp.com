<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use App\Models\PluginBundle;
use App\Services\CartService;
use App\Services\StripeConnectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected StripeConnectService $stripeConnectService
    ) {}

    public function show(Request $request): View
    {
        $user = Auth::user();
        $cart = $this->cartService->getCart($user);

        $cart->load('items.plugin.activePrice', 'items.plugin.user', 'items.pluginBundle.plugins');

        // Refresh prices and notify of changes
        $priceChanges = $this->cartService->refreshPrices($cart);

        $cart = $cart->fresh(['items.plugin.activePrice', 'items.plugin.user', 'items.pluginBundle.plugins']);

        // Check for available bundle upgrades
        $bundleUpgrades = $cart->getAvailableBundleUpgrades();

        return view('cart.show', [
            'cart' => $cart,
            'priceChanges' => $priceChanges,
            'bundleUpgrades' => $bundleUpgrades,
        ]);
    }

    public function add(Request $request, Plugin $plugin): RedirectResponse|JsonResponse
    {
        $user = Auth::user();
        $cart = $this->cartService->getCart($user);

        try {
            $this->cartService->addPlugin($cart, $plugin);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Plugin added to cart',
                    'cart_count' => $cart->itemCount(),
                ]);
            }

            // Store the added plugin ID to highlight it in the cart
            session()->flash('just_added_plugin_id', $plugin->id);

            return redirect()->route('cart.show')
                ->with('success', '<strong>'.e($plugin->name).'</strong> has been added to your cart!');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function remove(Request $request, Plugin $plugin): RedirectResponse|JsonResponse
    {
        $user = Auth::user();
        $cart = $this->cartService->getCart($user);

        $this->cartService->removePlugin($cart, $plugin);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Plugin removed from cart',
                'cart_count' => $cart->itemCount(),
            ]);
        }

        return redirect()->route('cart.show')->with('success', "{$plugin->name} removed from cart.");
    }

    public function addBundle(Request $request, PluginBundle $bundle): RedirectResponse|JsonResponse
    {
        $user = Auth::user();
        $cart = $this->cartService->getCart($user);

        try {
            $this->cartService->addBundle($cart, $bundle);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bundle added to cart',
                    'cart_count' => $cart->itemCount(),
                ]);
            }

            session()->flash('just_added_bundle_id', $bundle->id);

            return redirect()->route('cart.show')
                ->with('success', '<strong>'.e($bundle->name).'</strong> has been added to your cart!');
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function removeBundle(Request $request, PluginBundle $bundle): RedirectResponse|JsonResponse
    {
        $user = Auth::user();
        $cart = $this->cartService->getCart($user);

        $this->cartService->removeBundle($cart, $bundle);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bundle removed from cart',
                'cart_count' => $cart->itemCount(),
            ]);
        }

        return redirect()->route('cart.show')->with('success', "{$bundle->name} removed from cart.");
    }

    public function exchangeForBundle(Request $request, PluginBundle $bundle): RedirectResponse
    {
        $user = Auth::user();
        $cart = $this->cartService->getCart($user);

        try {
            $this->cartService->exchangeForBundle($cart, $bundle);

            return redirect()->route('cart.show')
                ->with('success', 'Swapped individual plugins for <strong>'.e($bundle->name).'</strong> bundle and saved '.$bundle->formatted_savings.'!');
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('cart.show')->with('error', $e->getMessage());
        }
    }

    public function clear(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $cart = $this->cartService->getCart($user);

        $cart->clear();

        return redirect()->route('cart.show')->with('success', 'Cart cleared.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            // Store intended URL and redirect to login
            session(['url.intended' => route('cart.checkout')]);

            return redirect()->route('customer.login')
                ->with('message', 'Please log in or create an account to complete your purchase.');
        }

        $cart = $this->cartService->getCart($user);

        if ($cart->isEmpty()) {
            return redirect()->route('cart.show')
                ->with('error', 'Your cart is empty.');
        }

        // Refresh prices
        $this->cartService->refreshPrices($cart);

        try {
            $session = $this->createMultiItemCheckoutSession($cart, $user);

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Cart checkout failed', [
                'cart_id' => $cart->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('cart.show')
                ->with('error', 'Unable to start checkout. Please try again.');
        }
    }

    public function success(Request $request): View|RedirectResponse
    {
        $sessionId = $request->query('session_id');

        // Validate session ID exists and looks like a real Stripe session ID
        if (! $sessionId || ! str_starts_with($sessionId, 'cs_')) {
            return redirect()->route('cart.show')
                ->with('error', 'Invalid checkout session. Please try again.');
        }

        $user = Auth::user();

        // Clear the cart - the webhook will create the licenses
        $cart = $this->cartService->getCart($user);
        $cart->clear();

        return view('cart.success', [
            'sessionId' => $sessionId,
        ]);
    }

    public function status(Request $request, string $sessionId): JsonResponse
    {
        $user = Auth::user();

        // Check if licenses exist for this checkout session
        $licenses = $user->pluginLicenses()
            ->where('stripe_checkout_session_id', $sessionId)
            ->with('plugin')
            ->get();

        if ($licenses->isEmpty()) {
            return response()->json([
                'status' => 'pending',
                'message' => 'Processing your purchase...',
            ]);
        }

        return response()->json([
            'status' => 'complete',
            'message' => 'Purchase complete!',
            'licenses' => $licenses->map(fn ($license) => [
                'id' => $license->id,
                'plugin_name' => $license->plugin->name,
                'plugin_slug' => $license->plugin->slug,
            ]),
        ]);
    }

    public function cancel(): RedirectResponse
    {
        return redirect()->route('cart.show')
            ->with('message', 'Checkout cancelled. Your cart items are still saved.');
    }

    public function count(Request $request): JsonResponse
    {
        $user = Auth::user();
        $count = $this->cartService->getCartItemCount($user);

        return response()->json(['count' => $count]);
    }

    protected function createMultiItemCheckoutSession($cart, $user): \Stripe\Checkout\Session
    {
        $stripe = new \Stripe\StripeClient(config('cashier.secret'));

        // Eager load items with plugins and bundles to avoid any stale data issues
        $cart->load('items.plugin', 'items.pluginBundle.plugins');

        $lineItems = [];
        $metadata = [
            'cart_id' => $cart->id,
            'user_id' => $user->id,
            'plugin_ids' => [],
            'price_ids' => [],
            'bundle_ids' => [],
            'bundle_plugin_ids' => [],
        ];

        Log::info('Creating multi-item checkout session', [
            'cart_id' => $cart->id,
            'user_id' => $user->id,
            'item_count' => $cart->items->count(),
        ]);

        foreach ($cart->items as $item) {
            if ($item->isBundle()) {
                $bundle = $item->pluginBundle;

                Log::info('Adding bundle to checkout session', [
                    'cart_id' => $cart->id,
                    'cart_item_id' => $item->id,
                    'bundle_id' => $bundle->id,
                    'bundle_name' => $bundle->name,
                    'plugin_count' => $bundle->plugins->count(),
                ]);

                $pluginNames = $bundle->plugins->pluck('name')->take(3)->implode(', ');
                if ($bundle->plugins->count() > 3) {
                    $pluginNames .= ' and '.($bundle->plugins->count() - 3).' more';
                }

                $lineItems[] = [
                    'price_data' => [
                        'currency' => strtolower($item->currency),
                        'unit_amount' => $item->bundle_price_at_addition,
                        'product_data' => [
                            'name' => $bundle->name.' (Bundle)',
                            'description' => 'Includes: '.$pluginNames,
                        ],
                    ],
                    'quantity' => 1,
                ];

                $metadata['bundle_ids'][] = $bundle->id;
                $metadata['bundle_plugin_ids'][$bundle->id] = $bundle->plugins->pluck('id')->implode(':');
            } else {
                $plugin = $item->plugin;

                Log::info('Adding plugin to checkout session', [
                    'cart_id' => $cart->id,
                    'cart_item_id' => $item->id,
                    'plugin_id' => $plugin->id,
                    'plugin_name' => $plugin->name,
                    'price_id' => $item->plugin_price_id,
                ]);

                $lineItems[] = [
                    'price_data' => [
                        'currency' => strtolower($item->currency),
                        'unit_amount' => $item->price_at_addition,
                        'product_data' => [
                            'name' => $plugin->name,
                            'description' => $plugin->description ?? 'NativePHP Plugin',
                        ],
                    ],
                    'quantity' => 1,
                ];

                $metadata['plugin_ids'][] = $plugin->id;
                $metadata['price_ids'][] = $item->plugin_price_id;
            }
        }

        // Encode arrays for Stripe metadata (must be strings)
        $metadata['cart_id'] = (string) $metadata['cart_id'];
        $metadata['user_id'] = (string) $metadata['user_id'];
        $metadata['plugin_ids'] = implode(',', $metadata['plugin_ids']);
        $metadata['price_ids'] = implode(',', $metadata['price_ids']);
        $metadata['bundle_ids'] = implode(',', $metadata['bundle_ids']);
        $metadata['bundle_plugin_ids'] = json_encode($metadata['bundle_plugin_ids']);

        Log::info('Checkout session metadata prepared', [
            'cart_id' => $cart->id,
            'metadata' => $metadata,
            'line_items_count' => count($lineItems),
        ]);

        // Ensure the user has a Stripe customer ID (required for Stripe Accounts V2)
        if (! $user->stripe_id) {
            $user->createAsStripeCustomer();
        }

        return $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'success_url' => route('cart.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('cart.cancel'),
            'customer' => $user->stripe_id,
            'customer_update' => [
                'name' => 'auto',
                'address' => 'auto',
            ],
            'metadata' => $metadata,
            'billing_address_collection' => 'required',
            'tax_id_collection' => ['enabled' => true],
            'invoice_creation' => [
                'enabled' => true,
                'invoice_data' => [
                    'description' => 'NativePHP Plugin Purchase',
                    'footer' => 'Thank you for your purchase!',
                ],
            ],
        ]);
    }
}
