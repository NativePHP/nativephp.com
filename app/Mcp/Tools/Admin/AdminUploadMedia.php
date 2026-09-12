<?php

namespace App\Mcp\Tools\Admin;

use App\Filament\Resources\ArticleResource;
use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Models\Article;
use App\Services\AdminArticleMediaService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use RuntimeException;

#[Name('admin-upload-media')]
#[Description('Upload a blog hero/featured image to the same public disk path Filament uses (blog/heroes). Accepts base64 content (no data: prefix). Prefer image/webp; jpeg/png allowed. Optionally attach to an article via article_id and/or slug in one shot. Does not publish.')]
class AdminUploadMedia extends Tool
{
    use RequiresAdmin;

    public function __construct(protected AdminArticleMediaService $media) {}

    public function handle(Request $request): Response
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $validated = $request->validate([
            'filename' => ['required', 'string', 'max:255'],
            'contentType' => ['required', 'string', 'max:100'],
            'content' => ['required', 'string'],
            'article_id' => ['nullable', 'integer', 'min:1'],
            'slug' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $stored = $this->media->storeHeroFromBase64(
                $validated['content'],
                $validated['contentType'],
                $validated['filename'],
            );
        } catch (RuntimeException $e) {
            return Response::error($e->getMessage());
        }

        $payload = [
            'path' => $stored['path'],
            'media_id' => $stored['media_id'],
            'url' => $stored['url'],
            'content_type' => $stored['content_type'],
            'bytes' => $stored['bytes'],
            'width' => $stored['width'],
            'height' => $stored['height'],
            'original_filename' => $stored['original_filename'],
            'disk' => AdminArticleMediaService::DISK,
            'directory' => AdminArticleMediaService::HERO_DIRECTORY,
            'attached' => false,
            'article' => null,
        ];

        $articleId = $validated['article_id'] ?? null;
        $slug = $validated['slug'] ?? null;

        if ($articleId || filled($slug)) {
            $article = Article::query()
                ->when($articleId, fn ($q) => $q->where('id', $articleId))
                ->when(! $articleId && filled($slug), fn ($q) => $q->where('slug', $slug))
                ->first();

            if (! $article) {
                return Response::error('Image uploaded, but article not found for attach. media_id='.$stored['media_id']);
            }

            try {
                $article = $this->media->attachHeroToArticle($article, $stored['path']);
            } catch (RuntimeException $e) {
                return Response::error('Image uploaded, but attach failed: '.$e->getMessage().' media_id='.$stored['media_id']);
            }

            $payload['attached'] = true;
            $payload['article'] = $this->articlePayload($article);
        }

        return Response::text($this->toJson($payload));
    }

    /**
     * @return array<string, mixed>
     */
    protected function articlePayload(Article $article): array
    {
        $editUrl = null;

        try {
            $editUrl = ArticleResource::getUrl('edit', ['record' => $article]);
        } catch (\Throwable) {
            $editUrl = url('/admin/articles/'.$article->id.'/edit');
        }

        return [
            'id' => $article->id,
            'slug' => $article->slug,
            'title' => $article->title,
            'hero_image' => $article->hero_image,
            'hero_image_url' => $article->getHeroImageUrl(),
            'published' => $article->isPublished(),
            'admin_edit_url' => $editUrl,
            'preview_url' => route('article', $article),
        ];
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'filename' => $schema->string()->description('Original filename (used for logging; stored name is a UUID .webp).')->required(),
            'contentType' => $schema->string()->description('MIME type: image/webp (preferred), image/jpeg, or image/png.')->required(),
            'content' => $schema->string()->description('Base64-encoded image bytes with no data: URI prefix. Decoded size hard-capped at a few MB; re-encoded to WebP.')->required(),
            'article_id' => $schema->integer()->description('Optional article id to attach this upload as the hero/featured image.'),
            'slug' => $schema->string()->description('Optional article slug to attach as hero when article_id is omitted.'),
        ];
    }
}
