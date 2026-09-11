<?php

namespace App\Mcp\Tools\Admin;

use App\Filament\Resources\ArticleResource;
use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Models\Article;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('admin-update-blog-post')]
#[Description('Update an existing blog article by id or slug. Patches only provided fields (title, content, excerpt, slug). Does not publish or unpublish. When id is provided, slug is treated as the new slug (drafts only; refused if published). When only slug is provided, it identifies the article.')]
class AdminUpdateBlogPost extends Tool
{
    use RequiresAdmin;

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

        if (! $updatingTitle && ! $updatingContent && ! $updatingExcerpt && ! $updatingSlug) {
            return Response::error('Provide at least one field to update: title, content, excerpt, or slug.');
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

        $article->fill($updates);
        $article->save();

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
        ];
    }
}
