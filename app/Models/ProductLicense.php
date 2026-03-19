<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductLicense extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo<User, ProductLicense>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Product, ProductLicense>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @param  Builder<ProductLicense>  $query
     * @return Builder<ProductLicense>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function forUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * @param  Builder<ProductLicense>  $query
     * @return Builder<ProductLicense>
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function forProduct(Builder $query, Product $product): Builder
    {
        return $query->where('product_id', $product->id);
    }

    protected function casts(): array
    {
        return [
            'price_paid' => 'integer',
            'purchased_at' => 'datetime',
        ];
    }
}
