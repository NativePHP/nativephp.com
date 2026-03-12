<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View|RedirectResponse
    {
        abort_unless($product->isActive(), 404);

        if ($product->slug === 'nativephp-masterclass') {
            return redirect()->route('course');
        }

        $user = Auth::user();

        if (! $product->hasAccessiblePriceFor($user)) {
            abort(404);
        }

        $bestPrice = $product->getBestPriceForUser($user);
        $regularPrice = $product->getRegularPrice();
        $alreadyOwned = $user && $product->isOwnedBy($user);

        return view('products.show', [
            'product' => $product,
            'bestPrice' => $bestPrice,
            'regularPrice' => $regularPrice,
            'hasDiscount' => $bestPrice && $regularPrice && $bestPrice->id !== $regularPrice->id,
            'alreadyOwned' => $alreadyOwned,
        ]);
    }
}
