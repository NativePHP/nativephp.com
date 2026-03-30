<?php

namespace Tests\Unit;

use App\Enums\Subscription;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubscriptionStripePriceIdTest extends TestCase
{
    private const YEARLY_PRICE = 'price_max_yearly';

    private const MONTHLY_PRICE = 'price_max_monthly';

    private const EAP_PRICE = 'price_max_eap';

    private const DISCOUNTED_PRICE = 'price_max_discounted';

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('subscriptions.plans.max.stripe_price_id', self::YEARLY_PRICE);
        Config::set('subscriptions.plans.max.stripe_price_id_monthly', self::MONTHLY_PRICE);
        Config::set('subscriptions.plans.max.stripe_price_id_eap', self::EAP_PRICE);
        Config::set('subscriptions.plans.max.stripe_price_id_discounted', self::DISCOUNTED_PRICE);
    }

    #[Test]
    public function yearly_returns_default_price(): void
    {
        $this->assertEquals(self::YEARLY_PRICE, Subscription::Max->stripePriceId());
    }

    #[Test]
    public function monthly_returns_monthly_price(): void
    {
        $this->assertEquals(self::MONTHLY_PRICE, Subscription::Max->stripePriceId(interval: 'month'));
    }

    #[Test]
    public function eap_yearly_returns_eap_price(): void
    {
        $this->assertEquals(self::EAP_PRICE, Subscription::Max->stripePriceId(forceEap: true));
    }

    #[Test]
    public function eap_monthly_returns_monthly_price_not_eap(): void
    {
        $this->assertEquals(
            self::MONTHLY_PRICE,
            Subscription::Max->stripePriceId(forceEap: true, interval: 'month')
        );
    }

    #[Test]
    public function discounted_returns_discounted_price(): void
    {
        $this->assertEquals(self::DISCOUNTED_PRICE, Subscription::Max->stripePriceId(discounted: true));
    }

    #[Test]
    public function monthly_falls_back_to_default_when_no_monthly_price(): void
    {
        Config::set('subscriptions.plans.max.stripe_price_id_monthly', null);

        $this->assertEquals(self::YEARLY_PRICE, Subscription::Max->stripePriceId(interval: 'month'));
    }
}
