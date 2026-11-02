<?php

namespace App\Listeners;

use App\Notifications\NewCompanyDomainRegistered;
use App\Services\CompanyAggregator;
use App\Support\ConsumerEmailDomains;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Notification;

class NotifyAccountsOfNewCompanyDomain
{
    public function __construct(
        public CompanyAggregator $companies,
    ) {}

    public function handle(Registered $event): void
    {
        $user = $event->user;
        $domain = ConsumerEmailDomains::domainFromEmail($user->email);

        if (! ConsumerEmailDomains::isCompanyDomain($domain)) {
            return;
        }

        if ($this->companies->countUsersForDomain($domain) !== 1) {
            return;
        }

        Notification::route('mail', config('companies.accounts_email'))
            ->notify(new NewCompanyDomainRegistered($user, $domain));
    }
}
