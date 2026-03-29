<?php

namespace Tests\Feature;

use App\Http\Controllers\ShowDocumentationController;
use Tests\TestCase;

class DocumentationTableOfContentsTest extends TestCase
{
    public function test_extracts_headings_from_rendered_html(): void
    {
        $html = '<h2 id="installation"><a href="#installation"><span>#</span></a>Installation</h2>'
            .'<p>Some text.</p>'
            .'<h3 id="sub-heading"><a href="#sub-heading"><span>#</span></a>Sub Heading</h3>';

        $controller = new ShowDocumentationController;

        $result = $this->invokeMethod($controller, 'extractTableOfContents', [$html]);

        $this->assertCount(2, $result);
        $this->assertEquals(['level' => 2, 'title' => 'Installation', 'anchor' => 'installation'], $result[0]);
        $this->assertEquals(['level' => 3, 'title' => 'Sub Heading', 'anchor' => 'sub-heading'], $result[1]);
    }

    public function test_anchors_match_heading_ids_for_inline_code(): void
    {
        $html = '<h2 id="using-codeconfigcode"><a href="#using-codeconfigcode"><span>#</span></a>Using <code>config()</code></h2>';

        $controller = new ShowDocumentationController;

        $result = $this->invokeMethod($controller, 'extractTableOfContents', [$html]);

        $this->assertCount(1, $result);
        $this->assertEquals('using-codeconfigcode', $result[0]['anchor']);
        $this->assertEquals('Using config()', $result[0]['title']);
    }

    public function test_returns_empty_array_when_no_headings(): void
    {
        $html = '<p>Just a paragraph.</p>';

        $controller = new ShowDocumentationController;

        $result = $this->invokeMethod($controller, 'extractTableOfContents', [$html]);

        $this->assertEmpty($result);
    }

    public function test_decodes_html_entities_in_title(): void
    {
        $html = '<h2 id="installation-amp-setup"><a href="#installation-amp-setup"><span>#</span></a>Installation &amp; Setup</h2>';

        $controller = new ShowDocumentationController;

        $result = $this->invokeMethod($controller, 'extractTableOfContents', [$html]);

        $this->assertCount(1, $result);
        $this->assertEquals('Installation & Setup', $result[0]['title']);
    }

    public function test_ignores_h1_and_h4_headings(): void
    {
        $html = '<h1 id="title"><a href="#title"><span>#</span></a>Title</h1>'
            .'<h2 id="section"><a href="#section"><span>#</span></a>Section</h2>'
            .'<h4 id="deep"><a href="#deep"><span>#</span></a>Deep</h4>';

        $controller = new ShowDocumentationController;

        $result = $this->invokeMethod($controller, 'extractTableOfContents', [$html]);

        $this->assertCount(1, $result);
        $this->assertEquals('section', $result[0]['anchor']);
    }

    /**
     * @param  array<mixed>  $args
     */
    protected function invokeMethod(object $object, string $method, array $args = []): mixed
    {
        $reflection = new \ReflectionMethod($object, $method);

        return $reflection->invoke($object, ...$args);
    }
}
