<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Accounts notification address
    |--------------------------------------------------------------------------
    |
    | When a user registers with a new non-consumer email domain, a short
    | notification is sent to this address.
    |
    */

    'accounts_email' => env('COMPANIES_ACCOUNTS_EMAIL', 'accounts@nativephp.com'),

    /*
    |--------------------------------------------------------------------------
    | Consumer / free mailbox domains
    |--------------------------------------------------------------------------
    |
    | Exact domains that should never be treated as company domains.
    |
    */

    'consumer_domains' => [
        'gmail.com',
        'googlemail.com',
        'outlook.com',
        'live.com',
        'msn.com',
        'icloud.com',
        'me.com',
        'mac.com',
        'aol.com',
        'protonmail.com',
        'proton.me',
        'hey.com',
        'mail.com',
        'qq.com',
        '163.com',
        '126.com',
        'yeah.net',
        'ymail.com',
        'rocketmail.com',
        'mail.ru',
        'inbox.com',
        'fastmail.com',
        'fastmail.fm',
        'tutanota.com',
        'tuta.io',
        'zoho.com',
        'zohomail.com',
        'gmx.com',
        'gmx.net',
        'gmx.de',
        'gmx.at',
        'gmx.ch',
        'gmx.fr',
        'gmx.co.uk',
        'hotmail.com',
        'hotmail.co.uk',
        'hotmail.fr',
        'hotmail.de',
        'hotmail.it',
        'hotmail.es',
        'hotmail.ca',
        'yahoo.com',
        'yahoo.co.uk',
        'yahoo.fr',
        'yahoo.de',
        'yahoo.it',
        'yahoo.es',
        'yahoo.ca',
        'yahoo.com.au',
        'yahoo.co.in',
        'yahoo.co.jp',
        'yandex.com',
        'yandex.ru',
        'yandex.ua',
        'yandex.by',
        'yandex.kz',
    ],

    /*
    |--------------------------------------------------------------------------
    | Consumer domain prefixes
    |--------------------------------------------------------------------------
    |
    | Domains that start with these prefixes (e.g. yahoo.co.uk, hotmail.fr,
    | gmx.de, yandex.ru) are also treated as consumer mailboxes.
    |
    */

    'consumer_domain_prefixes' => [
        'yahoo.',
        'hotmail.',
        'gmx.',
        'yandex.',
    ],

];
