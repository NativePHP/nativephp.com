<?php

namespace App\Mcp\Tools\Admin;

use App\Filament\Resources\ArticleResource;
use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Models\Article;
use App\Services\AdminArticleMediaService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use RuntimeException;

#[Name('admin-update-blog-post')]
#[Description('Update an existing blog article by id or slug. Patches only provided fields (title, content, excerpt, slug, hero image). Hero can be set via media_id/hero_image_path (from admin-upload-media) or hero_image_url. Does not publish or unpublish. When id is provided, slug is treated as the new slug (drafts only; refused if published). When only slug is provided, it identifies the article.')]
class AdminUpdateBlogPost extends Tool
{
    use RequiresAdmin;

    public function __construct(protected AdminArticleMediaService $media) {}

    public function handle(Request $request): Response
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $validated = $request->validate([
            'id' => ['nullable', 'integer', 'min:1'],
            'slug' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string', 'max:5000'],
            'media_id' => ['nullable', 'string', 'max:500'],
            'hero_image_path' => ['nullable', 'string', 'max:500'],
            'hero_image_url' => ['nullable', 'string', 'max:2000'],
        ]);

        if (empty($validated['id']) && empty($validated['slug'])) {
            return Response::error('Provide id or slug.');
        }

        $input = $request->all();
        $updatingTitle = array_key_exists('title', $input);
        $updatingContent = array_key_exists('content', $input);
        $updatingExcerpt = array_key_exists('excerpt', $input);
        // Slug is an update field only when identifying by id (same arg name as create).
        $updatingSlug = ! empty($validated['id']) && array_key_exists('slug', $input);
        $updatingHero = array_key_exists('media_id', $input)
            || array_key_exists('hero_image_path', $input)
            || array_key_exists('hero_image_url', $input);

        if (! $updatingTitle && ! $updatingContent && ! $updatingExcerpt && ! $updatingSlug && ! $updatingHero) {
            return Response::error('Provide at least one field to update: title, content, excerpt, slug, media_id, hero_image_path, or hero_image_url.');
        }

        $article = Article::query()
            ->when(! empty($validated['id']), fn ($q) => $q->where('id', $validated['id']))
            ->when(empty($validated['id']) && ! empty($validated['slug']), fn ($q) => $q->where('slug', $validated['slug']))
            ->first();

        if (! $article) {
            return Response::error('Article not found.');
        }

        $updates = [];

        if ($updatingTitle) {
            if (! filled($validated['title'] ?? null)) {
                return Response::error('title cannot be empty.');
            }
            $updates['title'] = $validated['title'];
        }

        if ($updatingContent) {
            if (! filled($validated['content'] ?? null)) {
                return Response::error('content cannot be empty.');
            }
            $updates['content'] = $validated['content'];
        }

        if ($updatingExcerpt) {
            $updates['excerpt'] = $validated['excerpt'] ?? '';
        }

        if ($updatingSlug) {
            if ($article->isPublished()) {
                return Response::error('The slug cannot be changed after the article is published.');
            }

            $slug = Str::slug((string) ($validated['slug'] ?? ''));

            if ($slug === '') {
                return Response::error('slug cannot be empty.');
            }

            if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
                return Response::error('slug must be a URL-safe kebab-case string.');
            }

            $conflict = Article::query()
                ->where('slug', $slug)
                ->where('id', '!=', $article->id)
                ->exists();

            if ($conflict) {
                return Response::error('That slug is already taken.');
            }

            $updates['slug'] = $slug;
        }

        if ($updates !== []) {
            $article->fill($updates);
            $article->save();
        }

        $heroMeta = null;

        if ($updatingHero) {
            $mediaId = $validated['media_id'] ?? $validated['hero_image_path'] ?? null;
            $heroUrl = $validated['hero_image_url'] ?? null;

            if (filled($mediaId) && filled($heroUrl)) {
                return Response::error('Provide only one of media_id/hero_image_path or hero_image_url.');
            }

            if (! filled($mediaId) && ! filled($heroUrl)) {
                return Response::error('media_id, hero_image_path, or hero_image_url cannot be empty.');
            }

            try {
                $stored = filled($mediaId)
                    ? $this->media->resolveExistingMedia((string) $mediaId)
                    : $this->media->storeHeroFromUrl((string) $heroUrl);

                $article = $this->media->attachHeroToArticle($article->fresh(), $stored['path']);
                $heroMeta = $stored;
            } catch (RuntimeException $e) {
                return Response::error($e->getMessage());
            }
        }

        $article = $article->fresh();

        $editUrl = null;

        try {
            $editUrl = ArticleResource::getUrl('edit', ['record' => $article]);
        } catch (\Throwable) {
            $editUrl = url('/admin/articles/'.$article->id.'/edit');
        }

        return Response::text($this->toJson([
            'id' => $article->id,
            'slug' => $article->slug,
            'title' => $article->title,
            'excerpt' => $article->excerpt,
            'content' => $article->content,
            'hero_image' => $article->hero_image,
            'hero_image_url' => $article->getHeroImageUrl(),
            'media' => $heroMeta,
            'published' => $article->isPublished(),
            'published_at' => optional($article->published_at)?->toIso8601String(),
            'author_id' => $article->author_id,
            'admin_edit_url' => $editUrl,
            'preview_url' => route('article', $article),
            'preview_note' => $article->isPublished()
                ? null
                : 'Drafts are only visible to signed-in site admins on the public blog route.',
        ]));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('Article id. Prefer id when changing the slug.'),
            'slug' => $schema->string()->description('Current slug to look up (when id omitted), or new slug to set (when id provided; drafts only).'),
            'title' => $schema->string()->description('Optional new title.'),
            'content' => $schema->string()->description('Optional new Markdown body.'),
            'excerpt' => $schema->string()->description('Optional new excerpt.'),
            'media_id' => $schema->string()->description('Optional media_id/path from admin-upload-media (under blog/heroes) to set as the hero image.'),
            'hero_image_path' => $schema->string()->description('Alias for media_id: public-disk relative path under blog/heroes.'),
            'hero_image_url' => $schema->string()->description('Optional http(s) URL to download and set as the hero image (re-encoded to WebP on the public disk).'),
        ];
    }
}
