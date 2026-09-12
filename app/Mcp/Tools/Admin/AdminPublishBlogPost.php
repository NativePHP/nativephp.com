<?php

namespace App\Mcp\Tools\Admin;

use App\Filament\Resources\ArticleResource;
use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Models\Article;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\Date;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;

#[Name('admin-publish-blog-post')]
#[Description('Publish a blog article by id or slug the same way Filament does (sets published_at via Article::publish). Optional published_at (ISO8601); defaults to now. Idempotent if already published — returns current state without error. Does not unpublish.')]
class AdminPublishBlogPost extends Tool
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
            'published_at' => ['nullable', 'date'],
        ]);

        if (empty($validated['id']) && empty($validated['slug'])) {
            return Response::error('Provide id or slug.');
        }

        $article = Article::query()
            ->when(! empty($validated['id']), fn ($q) => $q->where('id', $validated['id']))
            ->when(empty($validated['id']) && ! empty($validated['slug']), fn ($q) => $q->where('slug', $validated['slug']))
            ->first();

        if (! $article) {
            return Response::error('Article not found.');
        }

        if (! $article->isPublished()) {
            $on = array_key_exists('published_at', $validated) && filled($validated['published_at'] ?? null)
                ? Date::parse($validated['published_at'])
                : null;

            $article->publish($on);
            $article = $article->fresh();
        }

        $editUrl = null;

        try {
            $editUrl = ArticleResource::getUrl('edit', ['record' => $article]);
        } catch (\Throwable) {
            $editUrl = url('/admin/articles/'.$article->id.'/edit');
        }

        $publicUrl = route('article', $article);

        return Response::text($this->toJson([
            'id' => $article->id,
            'slug' => $article->slug,
            'title' => $article->title,
            'published' => $article->isPublished(),
            'published_at' => optional($article->published_at)?->toIso8601String(),
            'admin_edit_url' => $editUrl,
            'public_url' => $publicUrl,
            'preview_url' => $publicUrl,
        ]));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('Article id.'),
            'slug' => $schema->string()->description('Article slug (used when id is omitted).'),
            'published_at' => $schema->string()->description('Optional ISO8601 publish time. Defaults to now when omitted. Ignored when the article is already published (idempotent).'),
        ];
    }
}
