<?php

namespace Tests\Feature\Mcp;

use App\Models\Article;
use App\Models\User;
use App\Services\AdminArticleMediaService;
use App\Services\ArticleImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Tests\Concerns\InteractsWithMcpOAuth;
use Tests\TestCase;

class AdminUploadMediaTest extends TestCase
{
    use InteractsWithMcpOAuth;
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->configureMcpOAuthKeys();
        $this->admin = User::factory()->create(['email' => 'admin-media@nativephp.com']);
        config(['filament.users' => [$this->admin->email]]);
    }

    /**
     * @return array{binary: string, base64: string}
     */
    protected function makeHeroPayload(int $width = 1600, int $height = 840, string $format = 'jpeg'): array
    {
        $image = ImageManager::gd()->create($width, $height)->fill('3366ff');

        $encoded = match ($format) {
            'png' => $image->toPng(),
            'webp' => $image->toWebp(80),
            default => $image->toJpeg(85),
        };

        $binary = $encoded->toString();

        return [
            'binary' => $binary,
            'base64' => base64_encode($binary),
        ];
    }

    public function test_upload_media_defaults_to_website_images_directory(): void
    {
        $payload = $this->makeHeroPayload();
        $tokens = $this->issueMcpOAuthTokens($this->admin);

        $response = $this->callMcpTool($tokens['access_token'], 'admin-upload-media', [
            'filename' => 'hero-source.jpg',
            'contentType' => 'image/jpeg',
            'content' => $payload['base64'],
        ])->assertOk();

        $this->assertFalse($response->json('result.isError'));
        $result = json_decode((string) data_get($response->json(), 'result.content.0.text'), true);

        $this->assertSame('image/webp', $result['content_type']);
        $this->assertSame('public', $result['disk']);
        $this->assertSame('website-images', $result['directory']);
        $this->assertStringStartsWith('website-images/', $result['path']);
        $this->assertSame($result['path'], $result['media_id']);
        $this->assertFalse($result['attached']);
        $this->assertNull($result['article']);
        $this->assertGreaterThan(0, $result['bytes']);
        $this->assertSame(1600, $result['width']);
        $this->assertSame(840, $result['height']);

        Storage::disk('public')->assertExists($result['path']);
        $this->assertStringEndsWith('.webp', $result['path']);
    }

    public function test_upload_media_respects_explicit_directory(): void
    {
        $payload = $this->makeHeroPayload();
        $tokens = $this->issueMcpOAuthTokens($this->admin);

        $response = $this->callMcpTool($tokens['access_token'], 'admin-upload-media', [
            'filename' => 'hero-source.jpg',
            'contentType' => 'image/jpeg',
            'content' => $payload['base64'],
            'directory' => 'blog/heroes',
        ])->assertOk();

        $this->assertFalse($response->json('result.isError'));
        $result = json_decode((string) data_get($response->json(), 'result.content.0.text'), true);

        $this->assertSame('blog/heroes', $result['directory']);
        $this->assertStringStartsWith('blog/heroes/', $result['path']);
        Storage::disk('public')->assertExists($result['path']);
    }

    public function test_upload_media_rejects_unsafe_directories(): void
    {
        $payload = $this->makeHeroPayload();
        $tokens = $this->issueMcpOAuthTokens($this->admin);

        foreach (['../etc', '/absolute', 'blog/../../secrets'] as $directory) {
            $response = $this->callMcpTool($tokens['access_token'], 'admin-upload-media', [
                'filename' => 'hero.jpg',
                'contentType' => 'image/jpeg',
                'content' => $payload['base64'],
                'directory' => $directory,
            ])->assertOk();

            $this->assertTrue($response->json('result.isError'), 'Expected rejection for directory '.$directory);
            $message = (string) data_get($response->json(), 'result.content.0.text');
            $this->assertTrue(
                str_contains($message, '..') || str_contains($message, 'absolute') || str_contains($message, 'directory'),
                'Unexpected message for '.$directory.': '.$message
            );
        }
    }

    public function test_upload_media_rejects_data_uri_prefix_and_invalid_base64(): void
    {
        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $payload = $this->makeHeroPayload();

        $dataUri = $this->callMcpTool($tokens['access_token'], 'admin-upload-media', [
            'filename' => 'hero.webp',
            'contentType' => 'image/webp',
            'content' => 'data:image/webp;base64,'.$payload['base64'],
        ])->assertOk();
        $this->assertTrue($dataUri->json('result.isError'));
        $this->assertStringContainsString('data:', (string) data_get($dataUri->json(), 'result.content.0.text'));

        $bad = $this->callMcpTool($tokens['access_token'], 'admin-upload-media', [
            'filename' => 'hero.webp',
            'contentType' => 'image/webp',
            'content' => '%%%not-base64%%%',
        ])->assertOk();
        $this->assertTrue($bad->json('result.isError'));
        $this->assertStringContainsString('base64', (string) data_get($bad->json(), 'result.content.0.text'));
    }

    public function test_upload_media_rejects_oversized_decoded_payload(): void
    {
        $tokens = $this->issueMcpOAuthTokens($this->admin);

        // Oversized base64 (valid alphabet) without allocating a huge decoded binary first.
        $oversizedBase64 = str_repeat('A', (int) ceil(AdminArticleMediaService::MAX_DECODED_BYTES * 4 / 3) + 16);

        $response = $this->callMcpTool($tokens['access_token'], 'admin-upload-media', [
            'filename' => 'huge.jpg',
            'contentType' => 'image/jpeg',
            'content' => $oversizedBase64,
        ])->assertOk();

        $this->assertTrue($response->json('result.isError'));
        $this->assertStringContainsString('exceeds', (string) data_get($response->json(), 'result.content.0.text'));
    }

    public function test_upload_media_rejects_undersized_dimensions(): void
    {
        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $payload = $this->makeHeroPayload(800, 400);

        $response = $this->callMcpTool($tokens['access_token'], 'admin-upload-media', [
            'filename' => 'small.jpg',
            'contentType' => 'image/jpeg',
            'content' => $payload['base64'],
        ])->assertOk();

        $this->assertTrue($response->json('result.isError'));
        $this->assertStringContainsString((string) ArticleImageService::OG_WIDTH, (string) data_get($response->json(), 'result.content.0.text'));
    }

    public function test_upload_media_can_attach_to_article_by_id(): void
    {
        $article = Article::factory()->create([
            'author_id' => $this->admin->id,
            'slug' => 'attach-by-id',
            'published_at' => null,
            'hero_image' => null,
        ]);

        $payload = $this->makeHeroPayload();
        $tokens = $this->issueMcpOAuthTokens($this->admin);

        $response = $this->callMcpTool($tokens['access_token'], 'admin-upload-media', [
            'filename' => 'hero.jpg',
            'contentType' => 'image/jpeg',
            'content' => $payload['base64'],
            'directory' => 'blog/heroes',
            'article_id' => $article->id,
        ])->assertOk();

        $this->assertFalse($response->json('result.isError'));
        $result = json_decode((string) data_get($response->json(), 'result.content.0.text'), true);

        $this->assertTrue($result['attached']);
        $this->assertSame('blog/heroes', $result['directory']);
        $this->assertSame($article->id, $result['article']['id']);
        $this->assertSame($result['path'], $article->fresh()->hero_image);

        Storage::disk('public')->assertExists($result['path']);
        Storage::disk('public')->assertExists('og-images/'.$article->slug.'.png');
        Storage::disk('public')->assertExists('blog/cards/'.$article->slug.'.jpg');
        Storage::disk('public')->assertExists('blog/headers/'.$article->slug.'.jpg');
    }

    public function test_upload_media_attach_does_not_force_blog_heroes_directory(): void
    {
        $article = Article::factory()->create([
            'author_id' => $this->admin->id,
            'slug' => 'no-force-heroes',
            'published_at' => null,
            'hero_image' => null,
        ]);

        $payload = $this->makeHeroPayload();
        $tokens = $this->issueMcpOAuthTokens($this->admin);

        // article_id alone must not silently rewrite directory to blog/heroes.
        $response = $this->callMcpTool($tokens['access_token'], 'admin-upload-media', [
            'filename' => 'hero.jpg',
            'contentType' => 'image/jpeg',
            'content' => $payload['base64'],
            'article_id' => $article->id,
        ])->assertOk();

        $this->assertTrue($response->json('result.isError'));
        $message = (string) data_get($response->json(), 'result.content.0.text');
        $this->assertStringContainsString('attach failed', $message);
        $this->assertStringContainsString('blog/heroes', $message);
        $this->assertNull($article->fresh()->hero_image);

        // Confirm the upload itself landed in the default generic directory.
        $this->assertMatchesRegularExpression('#media_id=website-images/[^\s]+#', $message);
        preg_match('#media_id=(website-images/\S+)#', $message, $matches);
        Storage::disk('public')->assertExists($matches[1]);
    }

    public function test_upload_media_can_attach_to_article_by_slug(): void
    {
        $article = Article::factory()->create([
            'author_id' => $this->admin->id,
            'slug' => 'attach-by-slug',
            'published_at' => null,
        ]);

        $payload = $this->makeHeroPayload(format: 'png');
        $tokens = $this->issueMcpOAuthTokens($this->admin);

        $response = $this->callMcpTool($tokens['access_token'], 'admin-upload-media', [
            'filename' => 'hero.png',
            'contentType' => 'image/png',
            'content' => $payload['base64'],
            'directory' => 'blog/heroes',
            'slug' => 'attach-by-slug',
        ])->assertOk();

        $result = json_decode((string) data_get($response->json(), 'result.content.0.text'), true);
        $this->assertTrue($result['attached']);
        $this->assertSame($article->id, $result['article']['id']);
        $this->assertSame($result['path'], $article->fresh()->hero_image);
    }

    public function test_update_blog_post_sets_hero_from_media_id(): void
    {
        $article = Article::factory()->create([
            'author_id' => $this->admin->id,
            'slug' => 'hero-from-media',
            'published_at' => null,
        ]);

        $payload = $this->makeHeroPayload();
        $tokens = $this->issueMcpOAuthTokens($this->admin);

        $upload = $this->callMcpTool($tokens['access_token'], 'admin-upload-media', [
            'filename' => 'hero.jpg',
            'contentType' => 'image/jpeg',
            'content' => $payload['base64'],
            'directory' => 'blog/heroes',
        ])->assertOk();
        $uploaded = json_decode((string) data_get($upload->json(), 'result.content.0.text'), true);

        $response = $this->callMcpTool($tokens['access_token'], 'admin-update-blog-post', [
            'id' => $article->id,
            'media_id' => $uploaded['media_id'],
        ])->assertOk();

        $this->assertFalse($response->json('result.isError'));
        $result = json_decode((string) data_get($response->json(), 'result.content.0.text'), true);

        $this->assertSame($uploaded['path'], $result['hero_image']);
        $this->assertSame($uploaded['path'], $article->fresh()->hero_image);
        $this->assertNotNull($result['hero_image_url']);
        Storage::disk('public')->assertExists('og-images/'.$article->slug.'.png');
    }

    public function test_update_blog_post_sets_hero_from_url(): void
    {
        $article = Article::factory()->create([
            'author_id' => $this->admin->id,
            'slug' => 'hero-from-url',
            'published_at' => null,
        ]);

        $payload = $this->makeHeroPayload();
        Http::fake([
            'https://cdn.example.test/heroes/sample.jpg' => Http::response($payload['binary'], 200, [
                'Content-Type' => 'image/jpeg',
            ]),
        ]);

        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $response = $this->callMcpTool($tokens['access_token'], 'admin-update-blog-post', [
            'slug' => 'hero-from-url',
            'hero_image_url' => 'https://cdn.example.test/heroes/sample.jpg',
        ])->assertOk();

        $this->assertFalse($response->json('result.isError'));
        $result = json_decode((string) data_get($response->json(), 'result.content.0.text'), true);

        $this->assertNotNull($result['hero_image']);
        $this->assertStringStartsWith('blog/heroes/', $result['hero_image']);
        $this->assertSame($result['hero_image'], $article->fresh()->hero_image);
        Storage::disk('public')->assertExists($result['hero_image']);
    }

    public function test_update_blog_post_rejects_media_id_and_url_together(): void
    {
        $article = Article::factory()->create([
            'author_id' => $this->admin->id,
            'published_at' => null,
        ]);

        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $response = $this->callMcpTool($tokens['access_token'], 'admin-update-blog-post', [
            'id' => $article->id,
            'media_id' => 'blog/heroes/x.webp',
            'hero_image_url' => 'https://cdn.example.test/x.jpg',
        ])->assertOk();

        $this->assertTrue($response->json('result.isError'));
        $this->assertStringContainsString('only one', (string) data_get($response->json(), 'result.content.0.text'));
    }

    public function test_tool_rejects_when_authenticated_user_is_no_longer_admin(): void
    {
        $tokens = $this->issueMcpOAuthTokens($this->admin);

        // Token subject is still the admin user, but they are no longer in filament.users.
        config(['filament.users' => ['someone-else@nativephp.com']]);

        $payload = $this->makeHeroPayload();
        $response = $this->callMcpTool($tokens['access_token'], 'admin-upload-media', [
            'filename' => 'hero.jpg',
            'contentType' => 'image/jpeg',
            'content' => $payload['base64'],
        ]);

        // Middleware may 403 when admin scope checks fail, or the tool returns an error.
        $this->assertTrue(
            $response->status() === 403
            || ($response->status() === 200 && $response->json('result.isError') === true)
        );

        if ($response->status() === 200) {
            $this->assertStringContainsString('site admins', (string) data_get($response->json(), 'result.content.0.text'));
        }
    }
}
