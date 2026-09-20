<?php

namespace App\Mcp\Tools\Admin;

use App\Filament\Resources\ArticleResource;
use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Models\Article;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('admin-get-blog-post')]
#[Description('Get a blog article by id or slug, including draft/unpublished posts.')]
#[IsReadOnly]
class AdminGetBlogPost extends Tool
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
        ]);

        if (empty($validated['id']) && empty($validated['slug'])) {
            return Response::error('Provide id or slug.');
        }

        $article = Article::query()
            ->with('author:id,name,email')
            ->when(! empty($validated['id']), fn ($q) => $q->where('id', $validated['id']))
            ->when(! empty($validated['slug']), fn ($q) => $q->where('slug', $validated['slug']))
            ->first();

        if (! $article) {
            return Response::error('Article not found.');
        }

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
            'published' => $article->isPublished(),
            'published_at' => optional($article->published_at)?->toIso8601String(),
            'author' => [
                'id' => $article->author?->id,
                'name' => $article->author?->name,
                'email' => $article->author?->email,
            ],
            'admin_edit_url' => $editUrl,
            'preview_url' => route('article', $article),
        ]));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('Article id.'),
            'slug' => $schema->string()->description('Article slug.'),
        ];
    }
}
