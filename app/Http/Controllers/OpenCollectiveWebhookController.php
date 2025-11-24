<?php

namespace App\Http\Controllers;

use App\Enums\LicenseSource;
use App\Enums\Subscription;
use App\Jobs\CreateAnystackLicenseJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OpenCollectiveWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Verify webhook signature if secret is configured
        if (config('services.opencollective.webhook_secret')) {
            $this->verifySignature($request);
        }

        $payload = $request->all();
        $type = $payload['type'] ?? null;

        Log::info('OpenCollective webhook received', [
            'type' => $type,
            'payload' => $payload,
        ]);

        // Handle different webhook types
        match ($type) {
            'collective.transaction.created' => $this->handleContributionProcessed($payload),
            default => Log::info('Unhandled OpenCollective webhook type', ['type' => $type]),
        };

        return response()->json(['status' => 'success']);
    }

    protected function verifySignature(Request $request): void
    {
        $secret = config('services.opencollective.webhook_secret');
        $signature = $request->header('X-OpenCollective-Signature');

        if (! $signature) {
            abort(401, 'Missing webhook signature');
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            abort(401, 'Invalid webhook signature');
        }
    }

    protected function handleContributionProcessed(array $payload): void
    {
        $data = $payload['data'] ?? [];

        // Extract transaction details
        $order = $data['order'] ?? [];
        $fromAccount = $order['fromAccount'] ?? [];

        // Get contributor email and name
        $email = $fromAccount['email'] ?? null;
        $name = $fromAccount['name'] ?? null;

        if (! $email) {
            Log::warning('OpenCollective contribution missing email', ['payload' => $payload]);

            return;
        }

        // Check if this is a recurring contribution (monthly sponsor)
        $frequency = $order['frequency'] ?? 'ONETIME';
        $amount = $data['amount'] ?? [];
        $value = $amount['value'] ?? 0;

        // Only grant licenses for monthly sponsors above $10
        if ($frequency !== 'MONTHLY' || $value < 1000) { // Amount in cents
            Log::info('OpenCollective contribution does not qualify for license', [
                'email' => $email,
                'frequency' => $frequency,
                'value' => $value,
            ]);

            return;
        }

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name ?? Str::before($email, '@'),
                'password' => Hash::make(Str::random(32)),
            ]
        );

        // Check if user already has a Mini license from OpenCollective
        $existingLicense = $user->licenses()
            ->where('policy_name', Subscription::Mini->value)
            ->where('source', LicenseSource::OpenCollective)
            ->first();

        if ($existingLicense) {
            Log::info('User already has OpenCollective Mini license', [
                'user_id' => $user->id,
                'license_id' => $existingLicense->id,
            ]);

            return;
        }

        // Create Mini license
        $firstName = null;
        $lastName = null;

        if ($name) {
            $nameParts = explode(' ', $name, 2);
            $firstName = $nameParts[0] ?? null;
            $lastName = $nameParts[1] ?? null;
        }

        CreateAnystackLicenseJob::dispatch(
            user: $user,
            subscription: Subscription::Mini,
            subscriptionItemId: null,
            firstName: $firstName,
            lastName: $lastName,
            source: LicenseSource::OpenCollective
        );

        Log::info('Mini license creation dispatched for OpenCollective sponsor', [
            'user_id' => $user->id,
            'email' => $email,
        ]);
    }
}
