<?php

namespace App\Livewire;

use App\Enums\LicenseSource;
use App\Enums\Subscription;
use App\Jobs\CreateAnystackLicenseJob;
use App\Models\OpenCollectiveDonation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class ClaimDonationLicense extends Component
{
    public string $email = '';

    public string $name = '';

    public string $order_id = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $claimed = false;

    protected function rules(): array
    {
        $rules = [
            'order_id' => ['required', 'string'],
        ];

        // Only require these fields if user is not logged in
        if (! Auth::check()) {
            $rules['email'] = ['required', 'email', 'max:255'];
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        return $rules;
    }

    protected $messages = [
        'order_id.required' => 'Please enter your OpenCollective Transaction ID.',
    ];

    public function claim(): void
    {
        $this->validate();

        // Find the donation by order ID
        $donation = OpenCollectiveDonation::where('order_id', $this->order_id)->first();

        if (! $donation) {
            $this->addError('order_id', 'We could not find a donation with this order ID. Please check and try again.');

            return;
        }

        if ($donation->isClaimed()) {
            $this->addError('order_id', 'This donation has already been claimed.');

            return;
        }

        // Check if any donation from this contributor has already been claimed
        $alreadyClaimedByContributor = OpenCollectiveDonation::where('from_collective_id', $donation->from_collective_id)
            ->whereNotNull('claimed_at')
            ->exists();

        if ($alreadyClaimedByContributor) {
            $this->addError('order_id', 'A license has already been claimed for this OpenCollective account.');

            return;
        }

        // Get or create user
        if (Auth::check()) {
            $user = Auth::user();

            // Verify they don't already have an OpenCollective license
            $existingLicense = $user->licenses()
                ->where('source', LicenseSource::OpenCollective)
                ->first();

            if ($existingLicense) {
                $this->addError('order_id', 'You already have a license from OpenCollective.');

                return;
            }
        } else {
            // Check if email already exists
            $existingUser = User::where('email', $this->email)->first();

            if ($existingUser) {
                // Email exists but user isn't logged in - tell them to log in
                $this->addError('email', 'An account with this email already exists. Please log in first, then return to claim your license.');

                return;
            }

            // Create new user
            $user = User::create([
                'email' => $this->email,
                'name' => $this->name,
                'password' => Hash::make($this->password),
            ]);
        }

        // Parse name for first/last
        $name = Auth::check() ? $user->name : $this->name;
        $nameParts = explode(' ', $name, 2);
        $firstName = $nameParts[0] ?? null;
        $lastName = $nameParts[1] ?? null;

        // Create the license
        CreateAnystackLicenseJob::dispatch(
            user: $user,
            subscription: Subscription::Mini,
            subscriptionItemId: null,
            firstName: $firstName,
            lastName: $lastName,
            source: LicenseSource::OpenCollective
        );

        // Mark donation as claimed
        $donation->markAsClaimed($user);

        // Log the user in
        Auth::login($user);

        $this->claimed = true;
    }

    public function render()
    {
        return view('livewire.claim-donation-license')
            ->layout('components.layout', ['title' => 'Claim Your License']);
    }
}
