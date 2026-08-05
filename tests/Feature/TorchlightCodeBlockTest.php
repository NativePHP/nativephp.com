<?php

namespace Tests\Feature;

use App\Support\CommonMark\CommonMark;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TorchlightCodeBlockTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // A token is required for Torchlight to attempt highlighting (it throws
        // outside production otherwise), and faking the API forces its built-in
        // "un-highlighted" fallback — the same state produced in the wild when
        // Torchlight cannot tokenise a block. This keeps the test deterministic
        // and offline regardless of whether a real token is present locally.
        config(['torchlight.token' => 'test-token']);
        Http::fake([
            '*' => Http::response(['blocks' => []], 200),
        ]);
    }

    public function test_fenced_code_renders_as_a_torchlight_block(): void
    {
        $html = CommonMark::convertToHtml("```php\necho 'hello';\n```");

        $this->assertStringContainsString("class='torchlight'", $html);
    }

    public function test_unhighlighted_block_carries_torchlight_class_for_css_fallback(): void
    {
        // When Torchlight cannot highlight a block (e.g. a language its API
        // can't tokenise, such as the Svelte snippet below) it returns the
        // block un-highlighted with an empty `style` attribute and no per-token
        // color spans. The fallback CSS targets
        // `code.torchlight:not([style*='background-color'])` to give these
        // otherwise-invisible blocks a background and readable text, so the
        // markup must still carry the `torchlight` class without an inline
        // background color.
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
