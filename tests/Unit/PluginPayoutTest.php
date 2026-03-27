<?php

namespace Tests\Unit;

use App\Models\PluginPayout;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PluginPayoutTest extends TestCase
{
    #[Test]
    public function calculate_split_with_default_platform_fee(): void
    {
        $split = PluginPayout::calculateSplit(10000);

        $this->assertEquals(3000, $split['platform_fee']);
        $this->assertEquals(7000, $split['developer_amount']);
    }

    #[Test]
    public function calculate_split_with_zero_platform_fee(): void
    {
        $split = PluginPayout::calculateSplit(10000, 0);

        $this->assertEquals(0, $split['platform_fee']);
        $this->assertEquals(10000, $split['developer_amount']);
    }

    #[Test]
    public function calculate_split_with_custom_platform_fee(): void
    {
        $split = PluginPayout::calculateSplit(10000, 15);

        $this->assertEquals(1500, $split['platform_fee']);
        $this->assertEquals(8500, $split['developer_amount']);
    }

    #[Test]
    public function calculate_split_with_zero_amount(): void
    {
        $split = PluginPayout::calculateSplit(0);

        $this->assertEquals(0, $split['platform_fee']);
        $this->assertEquals(0, $split['developer_amount']);
    }

    #[Test]
    public function calculate_split_rounds_platform_fee(): void
    {
        // 2999 * 30% = 899.7 -> rounds to 900
        $split = PluginPayout::calculateSplit(2999);

        $this->assertEquals(900, $split['platform_fee']);
        $this->assertEquals(2099, $split['developer_amount']);
    }
}
