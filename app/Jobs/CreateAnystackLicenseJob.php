<?php

namespace App\Jobs;

use App\Enums\LicenseSource;
use App\Enums\Subscription;
use App\Models\License;
use App\Models\User;
use App\Notifications\LicenseKeyGenerated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CreateAnystackLicenseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Subscription $subscription,
        public ?int $subscriptionItemId = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public LicenseSource $source = LicenseSource::Stripe,
    ) {}

    public function handle(): void
    {
        if (! $this->user->anystack_contact_id) {
            $contact = $this->createContact();

            $this->user->anystack_contact_id = $contact['id'];
            $this->user->save();
        }

        $licenseData = $this->createLicense($this->user->anystack_contact_id);

        $license = License::create([
            'anystack_id' => $licenseData['id'],
            'user_id' => $this->user->id,
            'subscription_item_id' => $this->subscriptionItemId,
            'policy_name' => $this->subscription->value,
            'source' => $this->source,
            'key' => $licenseData['key'],
            'expires_at' => $licenseData['expires_at'],
            'created_at' => $licenseData['created_at'],
            'updated_at' => $licenseData['updated_at'],
        ]);

        $this->user->notify(new LicenseKeyGenerated(
            $license->key,
            $this->subscription,
            $this->firstName
        ));
    }

    private function createContact(): array
    {
        $data = collect([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->user->email,
        ])
            ->filter()
            ->all();

        // TODO: If an existing contact with the same email address already exists,
        //  anystack will return a 422 validation error response.
        return $this->anystackClient()
            ->post('https://api.anystack.sh/v1/contacts', $data)
            ->throw()
            ->json('data');
    }

    private function createLicense(string $contactId): ?array
    {
        $data = [
            'policy_id' => $this->subscription->anystackPolicyId(),
            'contact_id' => $contactId,
        ];

        return $this->anystackClient()
            ->post("https://api.anystack.sh/v1/products/{$this->subscription->anystackProductId()}/licenses", $data)
            ->throw()
            ->json('data');
    }

    private function anystackClient(): PendingRequest
    {
        return Http::withToken(config('services.anystack.key'))
            ->acceptJson()
            ->asJson();
    }
}
