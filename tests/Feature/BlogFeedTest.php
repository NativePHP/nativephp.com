<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BlogFeedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_blog_feed_is_served_as_rss_xml()
    {
        $this->get(route('blog.feed'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->assertSee('<rss', false)
            ->assertSee('<channel>', false);
    }

    #[Test]
    public function published_articles_appear_in_the_feed()
    {
        $article = Article::factory()->published()->create();

        $this->get(route('blog.feed'))
            ->assertOk()
            ->assertSee($article->title, false)
            ->assertSee(route('article', $article), false)
            ->assertSee($article->excerpt, false);
    }

    #[Test]
    public function articles_expose_their_open_graph_image_in_the_feed()
    {
        $article = Article::factory()->published()->create([
            'og_image' => 'https://example.com/og/cover.png',
        ]);

        $this->get(route('blog.feed'))
            ->assertOk()
            ->assertSee('<media:content', false)
            ->assertSee('url="https://example.com/og/cover.png"', false)
            ->assertSee('medium="image"', false);
    }

    #[Test]
    public function articles_without_an_open_graph_image_omit_the_media_tag()
    {
        Article::factory()->published()->create([
            'og_image' => null,
        ]);

        $this->get(route('blog.feed'))
            ->assertOk()
            ->assertDontSee('<media:content', false);
    }

    #[Test]
    public function the_feed_last_build_date_reflects_the_most_recently_updated_article()
    {
        $this->freezeTime();

        Article::factory()->published()->create([
            'updated_at' => now()->subWeek(),
        ]);

        $mostRecentlyUpdated = Article::factory()->published()->create([
            'updated_at' => now()->subDay(),
        ]);

        $this->get(route('blog.feed'))
            ->assertOk()
            ->assertSee('<lastBuildDate>'.$mostRecentlyUpdated->updated_at->toRssString().'</lastBuildDate>', false);
    }

    #[Test]
    public function scheduled_articles_do_not_appear_in_the_feed()
    {
        $article = Article::factory()->scheduled()->create();

        $this->get(route('blog.feed'))
            ->assertOk()
            ->assertDontSee($article->title, false);
    }

    #[Test]
    public function draft_articles_do_not_appear_in_the_feed()
    {
        $article = Article::factory()->create([
            'published_at' => null,
        ]);

        $this->get(route('blog.feed'))
            ->assertOk()
            ->assertDontSee($article->title, false);
    }

    #[Test]
    public function articles_appear_in_the_feed_in_reverse_chronological_order()
    {
        $older = Article::factory()->create([
            'published_at' => now()->subDays(2),
        ]);

        $newer = Article::factory()->create([
            'published_at' => now()->subDay(),
        ]);

        $this->get(route('blog.feed'))
            ->assertOk()
            ->assertSeeInOrder([$newer->title, $older->title], false);
    }

    #[Test]
    public function the_blog_page_links_to_the_feed_for_autodiscovery()
    {
        $this->get(route('blog'))
            ->assertOk()
            ->assertSee(route('blog.feed'), false);
    }
}
