<?php

namespace App\Support\Stripe;

use Exception;
use Illuminate\Support\Arr;
use Stripe\Price;

final class StripePrice
{
    public array $plan;

    public function __construct(public readonly Price $price) {}

    public static function from(Price $price): self
    {
        return new self($price);
    }

    public function getAnystackProductId(): string
    {
        return $this->getPlan()['anystack_product_id'];
    }

    public function getAnystackPolicyId(): string
    {
        return $this->getPlan()['anystack_policy_id'];
    }

    public function getPlanName(): string
    {
        return $this->getPlan()['name'];
    }

    public function getPlan(): array
    {
        return $this->plan ?? $this->resolvePlan();
    }

    protected function resolvePlan(): array
    {
        $plan = Arr::first(
            config('subscriptions.plans'),
            fn ($value, $key): bool => $value['stripe_price_id'] === $this->price->id
        );

        if (! $plan) {
            throw new Exception("Could not resolve plan for Stripe price_id: {$this->price->id}");
        }

        return $plan;
    }
}
