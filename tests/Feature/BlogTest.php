<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function published_articles_are_shown_on_the_blog_listing()
    {
        $article = Article::factory()->published()->create();

        $this->get(route('blog'))
            ->assertOk()
            ->assertSee($article->title)
            ->assertSee(route('article', $article));
    }

    #[Test]
    public function published_articles_are_shown_in_antichronological_order()
    {
        [$article1, $article2, $article3] = [
            Article::factory()->create([
                'published_at' => now()->subDays(2),
            ]),
            Article::factory()->create([
                'published_at' => now()->subDays(1),
            ]),
            Article::factory()->create([
                'published_at' => now()->subDays(3),
            ]),
        ];

        $this->get(route('blog'))
            ->assertOk()
            ->assertSeeInOrder([
                $article2->title,
                $article1->title,
                $article3->title,
            ]);
    }

    #[Test]
    public function scheduled_articles_are_not_shown_on_the_blog_listing()
    {
        $article = Article::factory()->scheduled()->create();

        $this->get(route('blog'))
            ->assertOk()
            ->assertDontSee($article->title)
            ->assertDontSee(route('article', $article));
    }

    #[Test]
    public function published_articles_are_visitable()
    {
        $article = Article::factory()->published()->create();

        $this->get(route('article', $article))
            ->assertOk();
    }

    #[Test]
    public function scheduled_articles_return_a_404()
    {
        $article = Article::factory()->scheduled()->create();

        $this->get(route('article', $article))
            ->assertStatus(404);
    }

    #[Test]
    public function articles_can_be_previewed_by_admin_users()
    {
        $article = Article::factory()->create([
            'published_at' => null,
        ]);

        $admin = User::factory()->create();
        Config::set('filament.users', [$admin->email]);

        // Visitors
        $this->get(route('article', $article))
            ->assertStatus(404);

        // Admins
        $this->actingAs($admin)
            ->get(route('article', $article))
            ->assertOk();
    }

    #[Test]
    public function articles_cant_be_previewed_by_regular_users()
    {
        $article = Article::factory()->create([
            'published_at' => null,
        ]);

        $user = User::factory()->create();

        // Non-admin users
        $this->actingAs($user)
            ->get(route('article', $article))
            ->assertStatus(404);
    }
}
