<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        abort_unless($product->isActive(), 404);

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
