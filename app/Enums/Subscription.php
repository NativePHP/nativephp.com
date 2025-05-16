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
            config('subscriptions.plans.mini.stripe_price_id') => self::Mini,
            config('subscriptions.plans.pro.stripe_price_id') => self::Pro,
            config('subscriptions.plans.max.stripe_price_id') => self::Max,
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

    public function stripePriceId(): string
    {
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
}
