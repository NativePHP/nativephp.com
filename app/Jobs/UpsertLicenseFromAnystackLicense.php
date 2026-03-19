<?php

namespace App\Jobs;

use App\Enums\Subscription;
use App\Models\License;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpsertLicenseFromAnystackLicense implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $maxExceptions = 1;

    public function __construct(
        public array $licenseData
    ) {}

    public function handle(): void
    {
        License::updateOrCreate(['key' => $this->licenseData['key']], $this->values());
    }

    protected function values(): array
    {
        $values = [
            'anystack_id' => $this->licenseData['id'],
            // subscription_item_id is not set here because we don't want to replace any existing values.
            'policy_name' => Subscription::fromAnystackPolicy($this->licenseData['policy_id'])->value,
            'is_suspended' => $this->licenseData['suspended'],
            'expires_at' => $this->licenseData['expires_at'],
            'created_at' => $this->licenseData['created_at'],
            'updated_at' => $this->licenseData['updated_at'],
        ];

        if ($user = $this->user()) {
            $values['user_id'] = $user->id;
        }

        return $values;
    }

    protected function user(): User
    {
        return User::query()
            ->where('anystack_contact_id', $this->licenseData['contact_id'])
            ->firstOrFail();
    }
}
