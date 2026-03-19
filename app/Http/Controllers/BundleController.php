<?php

namespace App\Http\Controllers;

use App\Models\PluginBundle;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BundleController extends Controller
{
    public function show(PluginBundle $bundle): View
    {
        abort_unless($bundle->isActive(), 404);

        $user = Auth::user();

        // Check if user has an accessible price for this bundle
        if (! $bundle->hasAccessiblePriceFor($user)) {
            abort(404);
        }

        $bundle->load('plugins.activePrice', 'plugins.user');

        $bestPrice = $bundle->getBestPriceForUser($user);
        $regularPrice = $bundle->getRegularPrice();
        $priceAmount = $bestPrice ? $bestPrice->amount : $bundle->price;

        return view('bundle-show', [
            'bundle' => $bundle,
            'bestPrice' => $bestPrice,
            'regularPrice' => $regularPrice,
            'hasDiscount' => $bestPrice && $regularPrice && $bestPrice->id !== $regularPrice->id,
            'discountPercent' => $bundle->getDiscountPercentFor($priceAmount),
            'savings' => $bundle->getSavingsFor($priceAmount),
            'formattedSavings' => $bundle->getFormattedSavingsFor($priceAmount),
        ]);
    }
}
