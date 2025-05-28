<?php

namespace App\Services\Anystack\Resources;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

final class Product
{
    public function __construct(
        private readonly PendingRequest $client,
        private readonly string $productId,
    ) {}

    public function licenses(): Licenses
    {
        return new Licenses($this->client, $this->productId);
    }

    public function retrieve(): Response
    {
        return $this->client->get($this->baseUrl());
    }

    private function baseUrl(): string
    {
        return "products/{$this->productId}";
    }
}
