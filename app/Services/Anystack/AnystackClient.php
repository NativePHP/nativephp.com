<?php

namespace App\Services\Anystack;

use App\Enums\Subscription;
use App\Services\Anystack\Resources\License;
use App\Services\Anystack\Resources\Licenses;
use App\Services\Anystack\Resources\Products;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

final class AnystackClient
{
    public function __construct(
        public readonly string $apiKey,
    ) {}

    public function products(): Products
    {
        return new Products($this->prepareRequest());
    }

    /**
     * Get the licenses resource for a product on Anystack.
     */
    public function licenses(?string $productId = null): Licenses
    {
        $productId ??= Subscription::Mini->anystackProductId();

        return new Licenses($this->prepareRequest(), $productId);
    }

    public function license(string $id, ?string $productId = null): License
    {
        return $this->licenses($productId)->id($id);
    }

    /**
     * Create a new Anystack API client.
     */
    public function prepareRequest(): PendingRequest
    {
        return Http::withToken($this->apiKey)
            ->baseUrl('https://api.anystack.sh/v1/')
            ->acceptJson()
            ->asJson()
            ->throw();
    }
}
