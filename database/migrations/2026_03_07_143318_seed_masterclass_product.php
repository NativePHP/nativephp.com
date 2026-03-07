<?php

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $product = Product::create([
            'name' => 'The NativePHP Masterclass',
            'slug' => 'nativephp-masterclass',
            'description' => 'Go from zero to published app. Learn to build native mobile and desktop applications using the PHP and Laravel skills you already have.',
            'is_active' => true,
            'published_at' => now(),
        ]);

        ProductPrice::create([
            'product_id' => $product->id,
            'tier' => 'regular',
            'amount' => 10100,
            'currency' => 'USD',
            'is_active' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $product = Product::where('slug', 'nativephp-masterclass')->first();

        if ($product) {
            $product->prices()->delete();
            $product->delete();
        }
    }
};
