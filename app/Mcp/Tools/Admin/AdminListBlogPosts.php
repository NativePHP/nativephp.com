<?php

namespace App\Mcp\Tools\Admin;

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

#[Name('admin-list-blog-posts')]
#[Description('List blog articles. Filter by status: all, published, or draft (unpublished / null published_at).')]
#[IsReadOnly]
class AdminListBlogPosts extends Tool
{
    use RequiresAdmin;

    public function handle(Request $request): Response
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:all,published,draft'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $status = $validated['status'] ?? 'all';
        $limit = (int) ($validated['limit'] ?? 25);

        $query = Article::query()->with('author:id,name,email')->latest('id');

        if ($status === 'published') {
            $query->published();
        } elseif ($status === 'draft') {
            $query->whereNull('published_at');
        }

        $articles = $query->limit($limit)->get()->map(fn (Article $article): array => [
            'id' => $article->id,
            'slug' => $article->slug,
            'title' => $article->title,
            'published' => $article->isPublished(),
            'published_at' => optional($article->published_at)?->toIso8601String(),
            'author_email' => $article->author?->email,
        ])->all();

        return Response::text($this->toJson([
            'status' => $status,
            'count' => count($articles),
            'articles' => $articles,
        ]));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'status' => $schema->string()->description('all | published | draft (default all).'),
            'limit' => $schema->integer()->description('Max results (default 25, max 100).'),
        ];
    }
}
