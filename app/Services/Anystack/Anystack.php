<?php

namespace App\Services\Anystack;

use App\Models\User;
use Illuminate\Http\Client\HttpClientException;

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

        return resolve(AnystackClient::class, ['apiKey' => $apiKey]);
    }

    public static function findContact(string $contactUuid): ?User
    {
        return User::query()
            ->where('anystack_contact_id', $contactUuid)
            ->first();
    }

    public static function findContactOrFail(string $contactUuid): User
    {
        return User::query()
            ->where('anystack_contact_id', $contactUuid)
            ->firstOrFail();
    }
}
