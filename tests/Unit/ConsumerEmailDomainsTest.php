<?php

namespace Tests\Unit;

use App\Support\ConsumerEmailDomains;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ConsumerEmailDomainsTest extends TestCase
{
    public function test_it_extracts_a_lowercase_domain_from_an_email(): void
    {
        $this->assertSame('acme.com', ConsumerEmailDomains::extract('Ada@Acme.com'));
    }

    public function test_it_returns_null_for_invalid_emails(): void
    {
        $this->assertNull(ConsumerEmailDomains::extract(null));
        $this->assertNull(ConsumerEmailDomains::extract(''));
        $this->assertNull(ConsumerEmailDomains::extract('not-an-email'));
    }

    #[DataProvider('consumerDomains')]
    public function test_it_recognizes_consumer_domains(string $domain): void
    {
        $this->assertTrue(ConsumerEmailDomains::isConsumer($domain));
        $this->assertFalse(ConsumerEmailDomains::isCompanyDomain($domain));
    }

    #[DataProvider('companyDomains')]
    public function test_it_recognizes_company_domains(string $domain): void
    {
        $this->assertFalse(ConsumerEmailDomains::isConsumer($domain));
        $this->assertTrue(ConsumerEmailDomains::isCompanyDomain($domain));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function consumerDomains(): array
    {
        return [
            'gmail' => ['gmail.com'],
            'googlemail' => ['googlemail.com'],
            'yahoo prefix' => ['yahoo.co.uk'],
            'hotmail prefix' => ['hotmail.fr'],
            'outlook' => ['outlook.com'],
            'icloud' => ['icloud.com'],
            'proton' => ['proton.me'],
            'gmx prefix' => ['gmx.de'],
            'yandex prefix' => ['yandex.ru'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function companyDomains(): array
    {
        return [
            'acme' => ['acme.com'],
            'nativephp' => ['nativephp.com'],
            'laravel' => ['laravel.com'],
        ];
    }
}
