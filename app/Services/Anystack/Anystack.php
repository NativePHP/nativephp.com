<?php

namespace App\Services\Anystack;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Anystack
{
    /**
     * Create a new Anystack API client.
     */
    public function client(): PendingRequest
    {
        return Http::withToken(config('services.anystack.key'))
            ->acceptJson()
            ->asJson();
    }

    /**
     * Suspend a license on AnyStack.
     *
     * @param  string  $productId  The AnyStack product ID
     * @param  string  $licenseId  The AnyStack license ID
     * @return Response The API response
     *
     * @throws \Illuminate\Http\Client\RequestException If the request fails
     */
    public function suspendLicense(string $productId, string $licenseId): Response
    {
        return $this->client()
            ->patch("https://api.anystack.sh/v1/products/{$productId}/licenses/{$licenseId}", [
                'suspended' => true,
            ])
            ->throw();
    }

    /**
     * Delete a license on AnyStack.
     *
     * @param  string  $productId  The AnyStack product ID
     * @param  string  $licenseId  The AnyStack license ID
     * @return Response The API response
     *
     * @throws \Illuminate\Http\Client\RequestException If the request fails
     */
    public function deleteLicense(string $productId, string $licenseId): Response
    {
        return $this->client()
            ->delete("https://api.anystack.sh/v1/products/{$productId}/licenses/{$licenseId}")
            ->throw();
    }

    /**
     * Retrieve a license from AnyStack.
     *
     * @param  string  $productId  The AnyStack product ID
     * @param  string  $licenseId  The AnyStack license ID
     * @return Response The API response
     *
     * @throws \Illuminate\Http\Client\RequestException If the request fails
     */
    public function getLicense(string $productId, string $licenseId): Response
    {
        return $this->client()
            ->get("https://api.anystack.sh/v1/products/{$productId}/licenses/{$licenseId}")
            ->throw();
    }
}
