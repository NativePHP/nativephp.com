<?php

namespace App\Services\Anystack\Resources;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

final class Licenses
{
    public function __construct(
        private readonly PendingRequest $client,
        private readonly string $productId,
    ) {}

    public function all(?int $page = 1): Response
    {
        return $this->client->get($this->url(), [
            'page' => $page,
        ]);
    }

    public function create(array $data): Response
    {
        return $this->client->post($this->url(), $data);
    }

    public function id(string $licenseId): License
    {
        return new License(
            $this->client,
            $this->productId,
            $licenseId,
        );
    }

    private function url(): string
    {
        return "products/{$this->productId}/licenses";
    }
}
