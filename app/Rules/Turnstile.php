<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\PotentiallyTranslatedString;

class Turnstile implements ValidationRule
{
    private const SITEVERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    private const TIMEOUT_SECONDS = 10;

    /**
     * Cloudflare documents tokens as being up to 2048 characters, so anything
     * longer is junk that isn't worth a siteverify round trip.
     */
    private const MAX_TOKEN_LENGTH = 2048;

    /**
     * @param  string|null  $expectedAction  The `data-action` the widget was rendered with. When
     *                                       given, a token minted by a different form is rejected.
     */
    public function __construct(private ?string $expectedAction = null) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secretKey = config('services.turnstile.secret_key');

        if (empty($secretKey)) {
            return;
        }

        if (! is_string($value) || $value === '') {
            $fail('Please complete the security check.');

            return;
        }

        if (strlen($value) > self::MAX_TOKEN_LENGTH) {
            $fail('Security verification failed. Please try again.');

            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(self::TIMEOUT_SECONDS)
                ->post(self::SITEVERIFY_URL, [
                    'secret' => $secretKey,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);
        } catch (ConnectionException $e) {
            Log::warning('Turnstile siteverify request failed.', ['exception' => $e->getMessage()]);

            $fail('Security verification failed. Please try again.');

            return;
        }

        if (! $response->successful() || ! $response->json('success')) {
            $fail('Security verification failed. Please try again.');

            return;
        }

        if ($this->expectedAction !== null && $response->json('action') !== $this->expectedAction) {
            $fail('Security verification failed. Please try again.');

            return;
        }

        if (! $this->hostnameIsAllowed($response->json('hostname'))) {
            $fail('Security verification failed. Please try again.');
        }
    }

    /**
     * Hostname pinning is opt-in: with no allow list configured we defer to the
     * domain list already enforced on the widget by Cloudflare.
     */
    private function hostnameIsAllowed(mixed $hostname): bool
    {
        $allowed = array_filter(array_map(
            trim(...),
            explode(',', (string) config('services.turnstile.hostnames'))
        ));

        if ($allowed === []) {
            return true;
        }

        return is_string($hostname) && in_array($hostname, $allowed, true);
    }
}
