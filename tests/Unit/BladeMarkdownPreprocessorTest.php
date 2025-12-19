<?php

namespace Tests\Unit;

use App\Support\CommonMark\BladeMarkdownPreprocessor;
use Tests\TestCase;

class BladeMarkdownPreprocessorTest extends TestCase
{
    public function test_it_processes_blade_components_in_markdown(): void
    {
        $markdown = <<<'MD'
# Hello World

<x-icons.checkmark class="size-4" />

Some more text.
MD;

        $result = BladeMarkdownPreprocessor::process($markdown);

        // The component should be rendered (replaced with SVG content)
        $this->assertStringNotContainsString('<x-icons.checkmark', $result);
        $this->assertStringContainsString('svg', $result);
    }

    public function test_it_preserves_code_blocks(): void
    {
        $markdown = <<<'MD'
# Example

```php
<x-component /> // This should NOT be rendered
```

<x-icons.checkmark class="size-4" />
MD;

        $result = BladeMarkdownPreprocessor::process($markdown);

        // Code block content should be preserved as-is
        $this->assertStringContainsString('<x-component />', $result);
        // But the actual component outside code block should be rendered
        $this->assertStringNotContainsString('<x-icons.checkmark', $result);
    }

    public function test_it_preserves_inline_code(): void
    {
        $markdown = <<<'MD'
Use the `<x-component />` syntax for components.

<x-icons.checkmark class="size-4" />
MD;

        $result = BladeMarkdownPreprocessor::process($markdown);

        // Inline code should be preserved
        $this->assertStringContainsString('`<x-component />`', $result);
        // But the actual component should be rendered
        $this->assertStringNotContainsString('<x-icons.checkmark', $result);
    }

    public function test_it_returns_original_content_when_no_components(): void
    {
        $markdown = <<<'MD'
# Just Markdown

No blade components here.

- Item 1
- Item 2
MD;

        $result = BladeMarkdownPreprocessor::process($markdown);

        $this->assertEquals($markdown, $result);
    }

    public function test_it_passes_data_to_blade_components(): void
    {
        $markdown = '{{ $name }}';

        $result = BladeMarkdownPreprocessor::process($markdown, ['name' => 'John']);

        $this->assertStringContainsString('John', $result);
    }
}
