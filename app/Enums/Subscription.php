<?php

namespace App\Enums;

use RuntimeException;

enum Subscription: string
{
    case Mini = 'mini';
    case Pro = 'pro';
    case Max = 'max';
    case Forever = 'forever';
    case Trial = 'trial';

    public static function fromStripeSubscription(\Stripe\Subscription $subscription): self
    {
        $priceId = $subscription->items->first()?->price->id;

        if (! $priceId) {
            throw new RuntimeException('Could not resolve Stripe price id from subscription object.');
        }

        return self::fromStripePriceId($priceId);
    }

    public static function fromStripePriceId(string $priceId): self
    {
        return match ($priceId) {
            config('subscriptions.plans.mini.stripe_price_id'),
            config('subscriptions.plans.mini.stripe_price_id_eap') => self::Mini,
            'price_1RoZeVAyFo6rlwXqtnOViUCf',
            config('subscriptions.plans.pro.stripe_price_id'),
            config('subscriptions.plans.pro.stripe_price_id_discounted'),
            config('subscriptions.plans.pro.stripe_price_id_eap') => self::Pro,
            'price_1RoZk0AyFo6rlwXqjkLj4hZ0',
            config('subscriptions.plans.max.stripe_price_id'),
            config('subscriptions.plans.max.stripe_price_id_discounted'),
            config('subscriptions.plans.max.stripe_price_id_eap') => self::Max,
            default => throw new RuntimeException("Unknown Stripe price id: {$priceId}"),
        };
    }

    public static function fromAnystackPolicy(string $policyId): self
    {
        return match ($policyId) {
            config('subscriptions.plans.mini.anystack_policy_id') => self::Mini,
            config('subscriptions.plans.pro.anystack_policy_id') => self::Pro,
            config('subscriptions.plans.max.anystack_policy_id') => self::Max,
            config('subscriptions.plans.forever.anystack_policy_id') => self::Forever,
            config('subscriptions.plans.trial.anystack_policy_id') => self::Trial,
            default => throw new RuntimeException("Unknown Anystack policy id: {$policyId}"),
        };
    }

    public function name(): string
    {
        return config("subscriptions.plans.{$this->value}.name");
    }

    public function stripePriceId(bool $forceEap = false, bool $discounted = false): string
    {
        // EAP ends June 1st at midnight UTC
        if (now()->isBefore('2025-06-01 00:00:00') || $forceEap) {
            return config("subscriptions.plans.{$this->value}.stripe_price_id_eap");
        }

        if ($discounted) {
            return config("subscriptions.plans.{$this->value}.stripe_price_id_discounted");
        }

        return config("subscriptions.plans.{$this->value}.stripe_price_id");
    }

    public function stripePaymentLink(): string
    {
        return config("subscriptions.plans.{$this->value}.stripe_payment_link");
    }

    public function anystackProductId(): string
    {
        return config("subscriptions.plans.{$this->value}.anystack_product_id");
    }

    public function anystackPolicyId(): string
    {
        return config("subscriptions.plans.{$this->value}.anystack_policy_id");
    }

    public function supportsSubLicenses(): bool
    {
        return in_array($this, [self::Pro, self::Max, self::Forever]);
    }

    public function subLicenseLimit(): ?int
    {
        return match ($this) {
            self::Pro => 9,
            self::Max, self::Forever => null, // Unlimited
            default => 0,
        };
    }
}
