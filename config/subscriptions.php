<?php

return [
    'plans' => [
        'mini' => [
            'name' => 'Early Access (Mini)',
            'stripe_price_id' => env('STRIPE_MINI_PRICE_ID'),
            'stripe_payment_link' => env('STRIPE_MINI_PAYMENT_LINK'),
            'anystack_product_id' => env('ANYSTACK_MINI_PRODUCT_ID'),
            'anystack_policy_id' => env('ANYSTACK_MINI_POLICY_ID'),
        ],
        'pro' => [
            'name' => 'Early Access (Pro)',
            'stripe_price_id' => env('STRIPE_PRO_PRICE_ID'),
            'stripe_payment_link' => env('STRIPE_PRO_PAYMENT_LINK'),
            'anystack_product_id' => env('ANYSTACK_PRO_PRODUCT_ID'),
            'anystack_policy_id' => env('ANYSTACK_PRO_POLICY_ID'),
        ],
        'max' => [
            'name' => 'Early Access (Max)',
            'stripe_price_id' => env('STRIPE_MAX_PRICE_ID'),
            'stripe_payment_link' => env('STRIPE_MAX_PAYMENT_LINK'),
            'anystack_product_id' => env('ANYSTACK_MAX_PRODUCT_ID'),
            'anystack_policy_id' => env('ANYSTACK_MAX_POLICY_ID'),
        ],
    ],
];
