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

#[Name('admin-create-blog-post')]
#[Description('Create an unpublished NativePHP blog article (published_at null). Sets author to the authenticated admin. Optional slug auto-generates from title and stays unique.')]
class AdminCreateBlogPost extends Tool
{
    use RequiresAdmin;

    public function handle(Request $request): Response
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:5000'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ]);

        $user = $this->user($request);
        $slug = $this->uniqueSlug($validated['slug'] ?? Str::slug($validated['title']));

        $excerpt = $validated['excerpt'] ?? null;
        if (! filled($excerpt)) {
            $excerpt = str($validated['content'])->stripTags()->squish()->limit(160)->toString();
        }

        $article = new Article([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $excerpt,
            'content' => $validated['content'],
            'published_at' => null,
        ]);
        $article->author_id = $user->id;
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
            'published' => false,
            'published_at' => null,
            'author_id' => $article->author_id,
            'admin_edit_url' => $editUrl,
            'preview_url' => route('article', $article),
            'preview_note' => 'Drafts are only visible to signed-in site admins on the public blog route.',
        ]));
    }

    protected function uniqueSlug(string $slug): string
    {
        $slug = Str::slug($slug) ?: 'article';
        $base = $slug;
        $i = 1;

        while (Article::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->description('Article title.')->required(),
            'content' => $schema->string()->description('Markdown body.')->required(),
            'excerpt' => $schema->string()->description('Optional short excerpt.'),
            'slug' => $schema->string()->description('Optional URL slug. Auto-generated from title when omitted; duplicates get a numeric suffix.'),
        ];
    }
}
