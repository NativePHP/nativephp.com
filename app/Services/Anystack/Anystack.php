<?php

namespace App\Services\Anystack;

use App\Models\User;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\Response;

final class Anystack
{
    /**
     * Create a new Anystack API client.
     */
    public static function api(?string $apiKey = null): AnystackClient
    {
        if (! ($apiKey ??= config('services.anystack.key'))) {
            throw new HttpClientException('Anystack API key is not configured.');
        }

        return app(AnystackClient::class, ['apiKey' => $apiKey]);
    }

    public static function findContact(string $contactUuid): ?User
    {
        return User::query()
            ->where('anystack_contact_id', $contactUuid)
            ->first();
    }

    public static function findContactOrFail(string $contactUuid): ?User
    {
        return User::query()
            ->where('anystack_contact_id', $contactUuid)
            ->firstOrFail();
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
