<?php

namespace App\Console\Commands;

use App\Enums\PayoutStatus;
use App\Models\DeveloperAccount;
use App\Services\StripeConnectService;
use App\Support\StripeConnectCountries;
use Illuminate\Console\Command;
use Laravel\Cashier\Cashier;

class RecreateConnectAccount extends Command
{
    protected $signature = 'payouts:recreate-connect-account {developerAccount : The developer account ID}';

    protected $description = 'Move a developer to a new Stripe Connect account on the recipient service agreement so payouts can reach their country';

    public function handle(StripeConnectService $stripeConnectService): int
    {
        $developerAccountId = $this->argument('developerAccount');
        $developerAccount = DeveloperAccount::find($developerAccountId);

        if (! $developerAccount) {
            $this->error("Developer account #{$developerAccountId} not found.");

            return self::FAILURE;
        }

        $stripeAccount = Cashier::stripe()->accounts->retrieve($developerAccount->stripe_connect_account_id);

        if ($stripeAccount->tos_acceptance?->service_agreement === 'recipient') {
            $this->info("Developer account #{$developerAccount->id} is already on the recipient service agreement.");

            return self::SUCCESS;
        }

        if (! StripeConnectCountries::requiresRecipientServiceAgreement($stripeAccount->country)) {
            $this->error("Stripe account {$stripeAccount->id} is in {$stripeAccount->country}, which we can already pay on the full service agreement.");

            return self::FAILURE;
        }

        $previousAccountId = $developerAccount->stripe_connect_account_id;

        $stripeConnectService->replaceConnectAccount($developerAccount, $stripeAccount->country);

        $heldPayouts = $developerAccount->payouts()
            ->failed()
            ->update(['status' => PayoutStatus::Held]);

        $this->info("Replaced {$previousAccountId} with {$developerAccount->stripe_connect_account_id}.");
        $this->info("Moved {$heldPayouts} failed payout(s) back to held. They will be sent once the developer finishes onboarding.");
        $this->line('Ask the developer to complete onboarding again at '.route('customer.developer.onboarding'));
        $this->line("The old Stripe account {$previousAccountId} can't receive payouts any more and can be deleted from the Stripe dashboard.");

        return self::SUCCESS;
    }
}
