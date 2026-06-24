<?php

namespace Tests\Feature;

use App\Support\CommonMark\CommonMark;
use Tests\TestCase;

class TorchlightCodeBlockTest extends TestCase
{
    public function test_fenced_code_renders_as_a_torchlight_block(): void
    {
        $html = CommonMark::convertToHtml("```php\necho 'hello';\n```");

        $this->assertStringContainsString("class='torchlight'", $html);
    }

    public function test_unhighlighted_block_carries_torchlight_class_for_css_fallback(): void
    {
        // When Torchlight cannot highlight a block (no API token, as in the
        // test environment, or a language its API can't tokenise such as the
        // Svelte snippet below) it returns the block un-highlighted with an
        // empty `style` attribute and no per-token color spans. The fallback
        // CSS targets `code.torchlight:not([style*='background-color'])` to
        // give these otherwise-invisible blocks a background and readable
        // text, so the markup must still carry the `torchlight` class without
        // an inline background color.
        $svelte = <<<'MD'
        ```svelte
        <div>
            {#if status}<p>{status}</p>{/if}
            <button on:click={tap} disabled={!ready}>Tap</button>
        </div>
        ```
        MD;

        $html = CommonMark::convertToHtml($svelte);

        $this->assertStringContainsString("class='torchlight'", $html);
        $this->assertStringContainsString("style=''", $html);
        $this->assertStringNotContainsString('background-color', $html);
    }
}
