<?php

namespace App\Support;

use Illuminate\Support\Str;

class ConsumerEmailDomains
{
    public static function extract(?string $email): ?string
    {
        if (! filled($email) || ! str_contains($email, '@')) {
            return null;
        }

        $domain = strtolower(trim(Str::afterLast($email, '@')));

        return $domain !== '' ? $domain : null;
    }

    public static function isConsumer(?string $domain): bool
    {
        if (! filled($domain)) {
            return true;
        }

        $domain = strtolower($domain);

        if (in_array($domain, config('companies.consumer_domains', []), true)) {
            return true;
        }

        foreach (config('companies.consumer_domain_prefixes', []) as $prefix) {
            if (str_starts_with($domain, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public static function isCompanyDomain(?string $domain): bool
    {
        return filled($domain) && ! static::isConsumer($domain);
    }

    public static function domainFromEmail(?string $email): ?string
    {
        return static::extract($email);
    }
}
