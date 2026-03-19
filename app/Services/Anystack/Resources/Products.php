<?php

namespace App\Services\Anystack\Resources;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

final class Products
{
    public function __construct(
        private readonly PendingRequest $client,
    ) {}

    public function all(?int $page = 1): Response
    {
        return $this->client->get($this->url(), [
            'page' => $page,
        ]);
    }

    public function id(string $productId): Product
    {
        return new Product(
            $this->client,
            $productId,
        );
    }

    private function url(): string
    {
        return 'products';
    }
}
