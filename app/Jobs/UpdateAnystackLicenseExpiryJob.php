<?php

namespace App\Jobs;

use App\Models\License;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class UpdateAnystackLicenseExpiryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public License $license
    ) {}

    public function handle(): void
    {
        if (! $this->license->anystack_id) {
            logger('Cannot update Anystack license expiry - no anystack_id', [
                'license_id' => $this->license->id,
                'license_key' => $this->license->key,
            ]);

            return;
        }

        try {
            // Update the license expiry in Anystack first using the renew endpoint
            $response = $this->anystackClient()
                ->patch("https://api.anystack.sh/v1/products/{$this->license->anystack_product_id}/licenses/{$this->license->anystack_id}/renew")
                ->throw()
                ->json('data');

            // Only update the database if Anystack update succeeded
            $oldExpiryDate = $this->license->expires_at;
            $newExpiryDate = $response['expires_at'];

            $this->license->update([
                'expires_at' => $newExpiryDate,
            ]);

            logger('Successfully updated license expiry (Anystack + Database)', [
                'license_id' => $this->license->id,
                'license_key' => $this->license->key,
                'anystack_id' => $this->license->anystack_id,
                'old_expiry' => $oldExpiryDate,
                'new_expiry' => $newExpiryDate,
            ]);

        } catch (\Exception $e) {
            logger('Failed to update Anystack license expiry', [
                'license_id' => $this->license->id,
                'license_key' => $this->license->key,
                'anystack_id' => $this->license->anystack_id,
                'error' => $e->getMessage(),
            ]);

            // Re-throw to trigger job failure and potential retry
            throw $e;
        }
    }

    private function anystackClient(): PendingRequest
    {
        return Http::withToken(config('services.anystack.key'))
            ->acceptJson()
            ->asJson();
    }
}
