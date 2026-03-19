<?php

namespace App\Jobs;

use App\Models\SubLicense;
use App\Services\Anystack\Anystack;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAnystackContactAssociationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public SubLicense $subLicense,
        public string $email
    ) {}

    public function handle(): void
    {
        if (! $this->subLicense->anystack_id) {
            logger('Cannot update Anystack contact association - no anystack_id', [
                'sub_license_id' => $this->subLicense->id,
                'sub_license_key' => $this->subLicense->key,
            ]);

            return;
        }

        try {
            // Create or find contact in Anystack by email
            $contactData = $this->createOrFindContact();

            // Update the sub-license to associate with the new contact
            $this->updateLicenseContact($contactData['id']);

            logger('Successfully updated Anystack contact association', [
                'sub_license_id' => $this->subLicense->id,
                'sub_license_key' => $this->subLicense->key,
                'email' => $this->email,
                'contact_id' => $contactData['id'],
            ]);

        } catch (\Exception $e) {
            logger('Failed to update Anystack contact association', [
                'sub_license_id' => $this->subLicense->id,
                'sub_license_key' => $this->subLicense->key,
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function createOrFindContact(): array
    {
        $client = Anystack::api()->prepareRequest();

        // Try to find existing contact by email
        try {
            $contacts = $client->get('contacts', [
                'filter' => ['email' => $this->email],
            ])->json('data');

            if (! empty($contacts)) {
                return $contacts[0];
            }
        } catch (\Exception $e) {
            // If search fails, continue to create new contact
            logger('Contact search failed, will create new contact', [
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
        }

        // Create new contact
        return $client->post('contacts', [
            'email' => $this->email,
            'name' => $this->email, // Use email as name if no name is provided
        ])->json('data');
    }

    private function updateLicenseContact(string $contactId): array
    {
        $productId = $this->subLicense->parentLicense->anystack_product_id;

        return Anystack::api()
            ->prepareRequest()
            ->patch("products/{$productId}/licenses/{$this->subLicense->anystack_id}", [
                'contact_id' => $contactId,
            ])
            ->json('data');
    }
}
