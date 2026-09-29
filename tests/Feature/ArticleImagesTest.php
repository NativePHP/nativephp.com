<?php

namespace Tests\Feature;

use App\Filament\Resources\ArticleResource\Pages\EditArticle;
use App\Models\Article;
use App\Models\User;
use App\Services\ArticleImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\ImageManager;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleImagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    /**
     * Write a hero image to the public disk: the left half red, the right half blue.
     */
    protected function createHeroImage(string $path, int $width = 1600, int $height = 800): void
    {
        Storage::disk('public')->makeDirectory(dirname($path));

        ImageManager::gd()
            ->create($width, $height)
            ->fill('ff0000')
            ->drawRectangle((int) ($width / 2), 0, function (RectangleFactory $rectangle) use ($width, $height): void {
                $rectangle->size((int) ($width / 2), $height);
                $rectangle->background('0000ff');
            })
            ->save(Storage::disk('public')->path($path));
    }

    protected function articleWithHero(array $attributes = []): Article
    {
        $article = Article::factory()->published()->create([
            'hero_image' => 'blog/heroes/test-hero.png',
            ...$attributes,
        ]);

        $this->createHeroImage('blog/heroes/test-hero.png');

        return $article;
    }

    public function test_refresh_images_generates_og_card_and_header_crops_from_the_hero(): void
    {
        $article = $this->articleWithHero();

        resolve(ArticleImageService::class)->refreshImages($article);

        $article->refresh();

        Storage::disk('public')->assertExists("og-images/{$article->slug}.png");
        Storage::disk('public')->assertExists("blog/cards/{$article->slug}.jpg");
        Storage::disk('public')->assertExists("blog/headers/{$article->slug}.jpg");

        $this->assertStringEndsWith("og-images/{$article->slug}.png", $article->og_image);
        $this->assertStringEndsWith("blog/cards/{$article->slug}.jpg", $article->card_image);
        $this->assertStringEndsWith("blog/headers/{$article->slug}.jpg", $article->header_image);

        $ogImage = ImageManager::gd()->read(Storage::disk('public')->path("og-images/{$article->slug}.png"));
        $this->assertSame(ArticleImageService::OG_WIDTH, $ogImage->width());
        $this->assertSame(ArticleImageService::OG_HEIGHT, $ogImage->height());

        $cardImage = ImageManager::gd()->read(Storage::disk('public')->path("blog/cards/{$article->slug}.jpg"));
        $this->assertSame(ArticleImageService::CARD_WIDTH, $cardImage->width());
        $this->assertSame(ArticleImageService::CARD_HEIGHT, $cardImage->height());

        $headerImage = ImageManager::gd()->read(Storage::disk('public')->path("blog/headers/{$article->slug}.jpg"));
        $this->assertSame(ArticleImageService::HEADER_WIDTH, $headerImage->width());
        $this->assertSame(ArticleImageService::HEADER_HEIGHT, $headerImage->height());
    }

    public function test_refresh_images_honours_the_stored_crop_selections(): void
    {
        $article = $this->articleWithHero([
            // Entirely within the left (red) half of the 1600x800 hero
            'og_image_crop' => ['x' => 0, 'y' => 0, 'width' => 762, 'height' => 400],
        ]);

        resolve(ArticleImageService::class)->refreshImages($article);

        $ogImage = ImageManager::gd()->read(Storage::disk('public')->path("og-images/{$article->slug}.png"));

        $this->assertSame('ff0000', $ogImage->pickColor(600, 315)->toHex());
        $this->assertSame('ff0000', $ogImage->pickColor(1190, 315)->toHex());
    }

    public function test_refresh_images_clamps_out_of_bounds_crop_selections(): void
    {
        $article = $this->articleWithHero([
            'og_image_crop' => ['x' => 5000, 'y' => 5000, 'width' => 9000, 'height' => 9000],
        ]);

        resolve(ArticleImageService::class)->refreshImages($article);

        $ogImage = ImageManager::gd()->read(Storage::disk('public')->path("og-images/{$article->slug}.png"));

        $this->assertSame(ArticleImageService::OG_WIDTH, $ogImage->width());
        $this->assertSame(ArticleImageService::OG_HEIGHT, $ogImage->height());
    }

    public function test_oversized_hero_images_are_scaled_down(): void
    {
        $article = Article::factory()->published()->create([
            'hero_image' => 'blog/heroes/huge-hero.png',
        ]);

        $this->createHeroImage('blog/heroes/huge-hero.png', 2500, 1000);

        resolve(ArticleImageService::class)->refreshImages($article);

        $hero = ImageManager::gd()->read(Storage::disk('public')->path('blog/heroes/huge-hero.png'));

        $this->assertSame(ArticleImageService::HERO_MAX_WIDTH, $hero->width());
        $this->assertSame((int) (ArticleImageService::HERO_MAX_WIDTH * 1000 / 2500), $hero->height());
    }

    public function test_refresh_images_generates_an_og_image_when_there_is_no_hero(): void
    {
        $article = Article::factory()->published()->create();

        resolve(ArticleImageService::class)->refreshImages($article);

        $article->refresh();

        Storage::disk('public')->assertExists("og-images/{$article->slug}.png");
        $this->assertStringEndsWith("og-images/{$article->slug}.png", $article->og_image);
        $this->assertNull($article->card_image);
        $this->assertNull($article->header_image);
    }

    public function test_replacing_the_hero_image_deletes_the_old_file(): void
    {
        $article = $this->articleWithHero();

        $this->createHeroImage('blog/heroes/new-hero.png');

        $article->update(['hero_image' => 'blog/heroes/new-hero.png']);

        Storage::disk('public')->assertMissing('blog/heroes/test-hero.png');
        Storage::disk('public')->assertExists('blog/heroes/new-hero.png');
    }

    public function test_changing_the_slug_deletes_stale_generated_images(): void
    {
        $article = $this->articleWithHero(['slug' => 'old-slug', 'published_at' => null]);

        resolve(ArticleImageService::class)->refreshImages($article);

        Storage::disk('public')->assertExists('og-images/old-slug.png');
        Storage::disk('public')->assertExists('blog/cards/old-slug.jpg');
        Storage::disk('public')->assertExists('blog/headers/old-slug.jpg');

        $article->update(['slug' => 'new-slug']);

        Storage::disk('public')->assertMissing('og-images/old-slug.png');
        Storage::disk('public')->assertMissing('blog/cards/old-slug.jpg');
        Storage::disk('public')->assertMissing('blog/headers/old-slug.jpg');
    }

    public function test_deleting_an_article_deletes_its_images(): void
    {
        $article = $this->articleWithHero();

        resolve(ArticleImageService::class)->refreshImages($article);

        Storage::disk('public')->assertExists('blog/heroes/test-hero.png');
        Storage::disk('public')->assertExists("og-images/{$article->slug}.png");
        Storage::disk('public')->assertExists("blog/cards/{$article->slug}.jpg");
        Storage::disk('public')->assertExists("blog/headers/{$article->slug}.jpg");

        $slug = $article->slug;

        $article->delete();

        Storage::disk('public')->assertMissing('blog/heroes/test-hero.png');
        Storage::disk('public')->assertMissing("og-images/{$slug}.png");
        Storage::disk('public')->assertMissing("blog/cards/{$slug}.jpg");
        Storage::disk('public')->assertMissing("blog/headers/{$slug}.jpg");
    }

    public function test_blog_index_shows_the_card_image(): void
    {
        Article::factory()->published()->create([
            'card_image' => '/storage/blog/cards/test-article.jpg',
        ]);

        $this->get(route('blog'))
            ->assertOk()
            ->assertSee('/storage/blog/cards/test-article.jpg');
    }

    public function test_article_page_shows_the_header_image(): void
    {
        $article = $this->articleWithHero();

        resolve(ArticleImageService::class)->refreshImages($article);

        $this->get(route('article', $article))
            ->assertOk()
            ->assertSee("storage/blog/headers/{$article->slug}.jpg");
    }

    public function test_article_page_has_no_header_markup_without_a_hero_image(): void
    {
        $article = Article::factory()->published()->create();

        $this->get(route('article', $article))
            ->assertOk()
            ->assertDontSee('storage/blog/headers');
    }

    public function test_saving_an_article_with_a_hero_generates_the_custom_images(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $article = $this->articleWithHero(['published_at' => null]);

        Livewire::actingAs($admin)
            ->test(EditArticle::class, ['record' => $article->id])
            ->fillForm(['title' => 'Updated title'])
            ->call('save')
            ->assertHasNoFormErrors();

        $article->refresh();

        Storage::disk('public')->assertExists("blog/cards/{$article->slug}.jpg");
        Storage::disk('public')->assertExists("blog/headers/{$article->slug}.jpg");
        $this->assertStringEndsWith("og-images/{$article->slug}.png", $article->og_image);
        $this->assertStringEndsWith("blog/cards/{$article->slug}.jpg", $article->card_image);
        $this->assertStringEndsWith("blog/headers/{$article->slug}.jpg", $article->header_image);

        // The OG image is cropped from the hero rather than generated by TheOG
        $ogImage = ImageManager::gd()->read(Storage::disk('public')->path("og-images/{$article->slug}.png"));
        $this->assertContains($ogImage->pickColor(10, 315)->toHex(), ['ff0000', '0000ff']);
    }

    public function test_removing_the_hero_resets_crops_and_reverts_to_generated_og_images(): void
    {
        $admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);

        $article = $this->articleWithHero([
            'published_at' => null,
            'og_image_crop' => ['x' => 0, 'y' => 0, 'width' => 762, 'height' => 400],
            'card_image_crop' => ['x' => 0, 'y' => 0, 'width' => 900, 'height' => 338],
            'header_image_crop' => ['x' => 0, 'y' => 0, 'width' => 1024, 'height' => 500],
        ]);

        resolve(ArticleImageService::class)->refreshImages($article);

        Livewire::actingAs($admin)
            ->test(EditArticle::class, ['record' => $article->id])
            ->fillForm(['hero_image' => null])
            ->call('save')
            ->assertHasNoFormErrors();

        $article->refresh();

        $this->assertNull($article->hero_image);
        $this->assertNull($article->og_image_crop);
        $this->assertNull($article->card_image_crop);
        $this->assertNull($article->header_image_crop);
        $this->assertNull($article->card_image);
        $this->assertNull($article->header_image);
        $this->assertStringEndsWith("og-images/{$article->slug}.png", $article->og_image);

        Storage::disk('public')->assertMissing('blog/heroes/test-hero.png');
        Storage::disk('public')->assertMissing("blog/cards/{$article->slug}.jpg");
        Storage::disk('public')->assertMissing("blog/headers/{$article->slug}.jpg");
        Storage::disk('public')->assertExists("og-images/{$article->slug}.png");
    }
}
