<?php

return [
    'plans' => [
        'mini' => [
            'name' => 'Mini',
            'stripe_price_id' => env('STRIPE_MINI_PRICE_ID'),
            'stripe_price_id_eap' => env('STRIPE_MINI_PRICE_ID_EAP'),
            'stripe_payment_link' => env('STRIPE_MINI_PAYMENT_LINK'),
            'anystack_product_id' => env('ANYSTACK_PRODUCT_ID'),
            'anystack_policy_id' => env('ANYSTACK_MINI_POLICY_ID'),
        ],
        'pro' => [
            'name' => 'Pro',
            'stripe_price_id' => env('STRIPE_PRO_PRICE_ID'),
            'stripe_price_id_eap' => env('STRIPE_PRO_PRICE_ID_EAP'),
            'stripe_payment_link' => env('STRIPE_PRO_PAYMENT_LINK'),
            'anystack_product_id' => env('ANYSTACK_PRODUCT_ID'),
            'anystack_policy_id' => env('ANYSTACK_PRO_POLICY_ID'),
        ],
        'max' => [
            'name' => 'Max',
            'stripe_price_id' => env('STRIPE_MAX_PRICE_ID'),
            'stripe_price_id_eap' => env('STRIPE_MAX_PRICE_ID_EAP'),
            'stripe_payment_link' => env('STRIPE_MAX_PAYMENT_LINK'),
            'anystack_product_id' => env('ANYSTACK_PRODUCT_ID'),
            'anystack_policy_id' => env('ANYSTACK_MAX_POLICY_ID'),
        ],
        'forever' => [
            'name' => 'Forever',
            'stripe_price_id' => env('STRIPE_FOREVER_PRICE_ID'),
            'stripe_payment_link' => env('STRIPE_FOREVER_PAYMENT_LINK'),
            'anystack_product_id' => env('ANYSTACK_PRODUCT_ID'),
            'anystack_policy_id' => env('ANYSTACK_FOREVER_POLICY_ID'),
        ],
        'trial' => [
            'name' => 'Trial',
            'stripe_price_id' => env('STRIPE_TRIAL_PRICE_ID'),
            'stripe_payment_link' => env('STRIPE_TRIAL_PAYMENT_LINK'),
            'anystack_product_id' => env('ANYSTACK_PRODUCT_ID'),
            'anystack_policy_id' => env('ANYSTACK_TRIAL_POLICY_ID'),
        ],
    ],
];
