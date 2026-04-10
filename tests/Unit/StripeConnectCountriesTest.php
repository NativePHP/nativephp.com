<?php

namespace Tests\Unit;

use App\Support\StripeConnectCountries;
use PHPUnit\Framework\TestCase;

class StripeConnectCountriesTest extends TestCase
{
    /** @test */
    public function all_returns_all_supported_countries(): void
    {
        $countries = StripeConnectCountries::all();

        $this->assertCount(103, $countries);
        $this->assertArrayHasKey('US', $countries);
        $this->assertArrayHasKey('GB', $countries);
        $this->assertArrayHasKey('DE', $countries);
    }

    /** @test */
    public function is_supported_returns_true_for_valid_country(): void
    {
        $this->assertTrue(StripeConnectCountries::isSupported('US'));
        $this->assertTrue(StripeConnectCountries::isSupported('GB'));
        $this->assertTrue(StripeConnectCountries::isSupported('FR'));
    }

    /** @test */
    public function is_supported_returns_false_for_invalid_country(): void
    {
        $this->assertFalse(StripeConnectCountries::isSupported('XX'));
        $this->assertFalse(StripeConnectCountries::isSupported('ZZ'));
    }

    /** @test */
    public function is_supported_is_case_insensitive(): void
    {
        $this->assertTrue(StripeConnectCountries::isSupported('us'));
        $this->assertTrue(StripeConnectCountries::isSupported('gb'));
    }

    /** @test */
    public function default_currency_returns_correct_currency(): void
    {
        $this->assertEquals('USD', StripeConnectCountries::defaultCurrency('US'));
        $this->assertEquals('GBP', StripeConnectCountries::defaultCurrency('GB'));
        $this->assertEquals('EUR', StripeConnectCountries::defaultCurrency('DE'));
        $this->assertEquals('AUD', StripeConnectCountries::defaultCurrency('AU'));
        $this->assertEquals('JPY', StripeConnectCountries::defaultCurrency('JP'));
    }

    /** @test */
    public function default_currency_returns_null_for_unsupported_country(): void
    {
        $this->assertNull(StripeConnectCountries::defaultCurrency('XX'));
    }

    /** @test */
    public function available_currencies_returns_array(): void
    {
        $currencies = StripeConnectCountries::availableCurrencies('US');

        $this->assertIsArray($currencies);
        $this->assertContains('USD', $currencies);
    }

    /** @test */
    public function available_currencies_returns_multiple_for_european_countries(): void
    {
        $currencies = StripeConnectCountries::availableCurrencies('DE');

        $this->assertContains('EUR', $currencies);
        $this->assertContains('GBP', $currencies);
        $this->assertContains('USD', $currencies);
    }

    /** @test */
    public function available_currencies_returns_empty_for_unsupported_country(): void
    {
        $this->assertEmpty(StripeConnectCountries::availableCurrencies('XX'));
    }

    /** @test */
    public function is_valid_currency_for_country_validates_correctly(): void
    {
        $this->assertTrue(StripeConnectCountries::isValidCurrencyForCountry('US', 'USD'));
        $this->assertTrue(StripeConnectCountries::isValidCurrencyForCountry('DE', 'EUR'));
        $this->assertTrue(StripeConnectCountries::isValidCurrencyForCountry('DE', 'USD'));
        $this->assertFalse(StripeConnectCountries::isValidCurrencyForCountry('US', 'EUR'));
        $this->assertFalse(StripeConnectCountries::isValidCurrencyForCountry('JP', 'USD'));
    }

    /** @test */
    public function supported_country_codes_returns_array_of_codes(): void
    {
        $codes = StripeConnectCountries::supportedCountryCodes();

        $this->assertContains('US', $codes);
        $this->assertContains('GB', $codes);
        $this->assertCount(103, $codes);
    }

    /** @test */
    public function currency_name_returns_correct_name(): void
    {
        $this->assertEquals('US Dollar', StripeConnectCountries::currencyName('USD'));
        $this->assertEquals('Euro', StripeConnectCountries::currencyName('EUR'));
        $this->assertEquals('British Pound', StripeConnectCountries::currencyName('GBP'));
        $this->assertEquals('Japanese Yen', StripeConnectCountries::currencyName('JPY'));
    }

    /** @test */
    public function currency_name_returns_code_for_unknown_currency(): void
    {
        $this->assertEquals('ZZZ', StripeConnectCountries::currencyName('ZZZ'));
    }

    /** @test */
    public function every_used_currency_has_a_name(): void
    {
        $countries = StripeConnectCountries::all();
        $currencies = collect($countries)->pluck('currencies')->flatten()->unique();

        foreach ($currencies as $code) {
            $name = StripeConnectCountries::currencyName($code);
            $this->assertNotEquals($code, $name, "Currency {$code} is missing a name in CURRENCY_NAMES");
        }
    }

    /** @test */
    public function india_is_not_in_supported_countries(): void
    {
        $this->assertFalse(StripeConnectCountries::isSupported('IN'));
        $this->assertArrayNotHasKey('IN', StripeConnectCountries::all());
    }

    /** @test */
    public function taiwan_is_not_in_supported_countries(): void
    {
        $this->assertFalse(StripeConnectCountries::isSupported('TW'));
        $this->assertArrayNotHasKey('TW', StripeConnectCountries::all());
    }

    /** @test */
    public function south_korea_is_not_in_supported_countries(): void
    {
        $this->assertFalse(StripeConnectCountries::isSupported('KR'));
        $this->assertArrayNotHasKey('KR', StripeConnectCountries::all());
    }

    /** @test */
    public function nigeria_is_not_in_supported_countries(): void
    {
        $this->assertFalse(StripeConnectCountries::isSupported('NG'));
        $this->assertArrayNotHasKey('NG', StripeConnectCountries::all());
    }

    /** @test */
    public function namibia_is_not_in_supported_countries(): void
    {
        $this->assertFalse(StripeConnectCountries::isSupported('NA'));
        $this->assertArrayNotHasKey('NA', StripeConnectCountries::all());
    }

    /** @test */
    public function each_country_has_required_keys(): void
    {
        foreach (StripeConnectCountries::all() as $code => $details) {
            $this->assertArrayHasKey('name', $details, "Country {$code} missing 'name'");
            $this->assertArrayHasKey('flag', $details, "Country {$code} missing 'flag'");
            $this->assertArrayHasKey('default_currency', $details, "Country {$code} missing 'default_currency'");
            $this->assertArrayHasKey('currencies', $details, "Country {$code} missing 'currencies'");
            $this->assertNotEmpty($details['currencies'], "Country {$code} has empty currencies");
            $this->assertContains($details['default_currency'], $details['currencies'], "Country {$code} default currency not in currencies list");
            $this->assertEquals(2, strlen($code), "Country code {$code} is not 2 characters");
            $this->assertEquals(3, strlen($details['default_currency']), "Country {$code} default currency is not 3 characters");
        }
    }
}
