<?php

namespace Tests\Unit\Support;

use App\Support\QrCode;
use Tests\TestCase;

class QrCodeTest extends TestCase
{
    public function test_it_renders_inline_svg_markup(): void
    {
        $svg = QrCode::svg('https://nativephp.com/docs/mobile/4/edge-components/stack?jump=qr');

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('</svg>', $svg);
    }

    public function test_it_returns_raw_markup_not_a_base64_data_uri(): void
    {
        $svg = QrCode::svg('https://example.test');

        $this->assertStringNotContainsString('data:image', $svg);
    }

    public function test_it_pins_the_svg_to_fill_its_container(): void
    {
        $svg = QrCode::svg('https://example.test');

        $this->assertStringContainsString('width:100%', $svg);
    }
}
