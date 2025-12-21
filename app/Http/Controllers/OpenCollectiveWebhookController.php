<?php

namespace App\Http\Controllers;

use App\Models\OpenCollectiveDonation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            'order.processed' => $this->handleOrderProcessed($payload),
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

    protected function handleOrderProcessed(array $payload): void
    {
        $webhookId = $payload['id'] ?? null;
        $data = $payload['data'] ?? [];
        $order = $data['order'] ?? [];
        $fromCollective = $data['fromCollective'] ?? [];

        $orderId = $order['id'] ?? null;

        if (! $orderId) {
            Log::warning('OpenCollective order.processed missing order ID', ['payload' => $payload]);

            return;
        }

        // Check if we've already processed this order
        if (OpenCollectiveDonation::where('order_id', $orderId)->exists()) {
            Log::info('OpenCollective order already processed', ['order_id' => $orderId]);

            return;
        }

        // Store the donation for later claiming
        OpenCollectiveDonation::create([
            'webhook_id' => $webhookId,
            'order_id' => $orderId,
            'order_idv2' => $order['idV2'] ?? null,
            'amount' => $order['totalAmount'] ?? 0,
            'currency' => $order['currency'] ?? 'USD',
            'interval' => $order['interval'] ?? null,
            'from_collective_id' => $fromCollective['id'] ?? $order['FromCollectiveId'] ?? 0,
            'from_collective_name' => $fromCollective['name'] ?? null,
            'from_collective_slug' => $fromCollective['slug'] ?? null,
            'raw_payload' => $payload,
        ]);

        Log::info('OpenCollective donation stored for claiming', [
            'order_id' => $orderId,
            'amount' => $order['totalAmount'] ?? 0,
        ]);
    }
}
