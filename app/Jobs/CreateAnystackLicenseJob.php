<?php

namespace App\Jobs;

use App\Notifications\LicenseKeyGenerated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

class CreateAnystackLicenseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public string $productId,
        public string $policyId,
        public ?string $firstName = null,
        public ?string $lastName = null,
    ) {}

    public function handle(): void
    {
        $contact = $this->createContact();

        $license = $this->createLicense($contact['id']);

        Cache::put($this->email.'.license_key', $license['key'], now()->addDay());

        Notification::route('mail', $this->email)
            ->notify(new LicenseKeyGenerated(
                $license['key'],
                $this->firstName
            ));
    }

    private function createContact(): array
    {
        $data = collect([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
        ])
            ->filter()
            ->all();

        // TODO: It's unknown what will happen if an existing contact with
        //  the same email address already exists.
        return $this->anystackClient()
            ->post('https://api.anystack.sh/v1/contacts', $data)
            ->throw()
            ->json('data');
    }

    private function createLicense(string $contactId): ?array
    {
        $data = [
            'policy_id' => $this->policyId,
            'contact_id' => $contactId,
        ];

        return $this->anystackClient()
            ->post("https://api.anystack.sh/v1/products/{$this->productId}/licenses", $data)
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
