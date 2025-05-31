<?php

namespace App\Services\Anystack\Resources;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

final class License
{
    public function __construct(
        private readonly PendingRequest $client,
        private readonly string $productId,
        private readonly string $licenseId,
    ) {}

    public function retrieve(): Response
    {
        return $this->client->get($this->baseUrl());
    }

    public function update(array $data): Response
    {
        return $this->client->patch($this->baseUrl(), $data);
    }

    public function suspend(bool $suspend = true): Response
    {
        return $this->client->patch($this->baseUrl(), [
            'suspended' => $suspend,
        ]);
    }

    public function renew(): Response
    {
        return $this->client->patch("{$this->baseUrl()}/renew");
    }

    public function delete(): Response
    {
        return $this->client->delete($this->baseUrl());
    }

    private function baseUrl(): string
    {
        return "products/{$this->productId}/licenses/{$this->licenseId}";
    }
}
