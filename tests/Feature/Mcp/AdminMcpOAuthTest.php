<?php

namespace Tests\Feature\Mcp;

use App\Models\Article;
use App\Models\Plugin;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Tests\Concerns\InteractsWithMcpOAuth;
use Tests\TestCase;

class AdminMcpOAuthTest extends TestCase
{
    use InteractsWithMcpOAuth;
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configureMcpOAuthKeys();
        $this->admin = User::factory()->create(['email' => 'admin-oauth@nativephp.com']);
        config(['filament.users' => [$this->admin->email]]);
    }

    public function test_advertises_admin_oauth_protected_resource_with_mcp_admin_scope(): void
    {
        $this->getJson('/.well-known/oauth-protected-resource/mcp/oauth/admin')
            ->assertOk()
            ->assertJsonPath('resource', $this->mcpAdminOAuthResource())
            ->assertJsonPath('authorization_servers.0', rtrim(config('app.url'), '/'))
            ->assertJsonPath('scopes_supported', ['mcp:admin']);

        $this->getJson('/.well-known/oauth-authorization-server')
            ->assertOk()
            ->assertJsonPath('scopes_supported', ['mcp:admin']);

        $response = $this->callMcpWithBearer('invalid')
            ->assertUnauthorized()
            ->assertHeader('WWW-Authenticate');

        $this->assertStringStartsWith('Bearer ', $response->headers->get('WWW-Authenticate'));
        $this->assertStringContainsString(
            'resource_metadata="'.url('/.well-known/oauth-protected-resource/mcp/oauth/admin').'"',
            $response->headers->get('WWW-Authenticate')
        );
        $this->assertStringContainsString('scope="mcp:admin"', $response->headers->get('WWW-Authenticate'));
    }

    public function test_unauthenticated_admin_mcp_requests_are_unauthorized(): void
    {
        $this->postJson('/mcp/oauth/admin', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => (object) [],
        ], ['Accept' => 'application/json, text/event-stream'])->assertUnauthorized();
    }

    public function test_admin_can_connect_via_oauth_and_use_admin_mcp_server(): void
    {
        $client = $this->createMcpOAuthClient();
        $tokens = $this->issueMcpOAuthTokens($this->admin, $client);

        $jwt = (new Parser(new JoseEncoder))->parse($tokens['access_token']);
        $this->assertSame(
            [(string) $client->id, $this->mcpAdminOAuthResource()],
            $jwt->claims()->get('aud')
        );

        $this->callMcpWithBearer($tokens['access_token'])
            ->assertOk()
            ->assertJsonPath('result.serverInfo.name', 'NativePHP Admin');

        $tools = $this->callMcpWithBearer($tokens['access_token'], 'tools/list')
            ->assertOk();

        $names = collect($tools->json('result.tools'))->pluck('name')->all();

        $this->assertContains('admin-create-blog-post', $names);
        $this->assertContains('admin-upload-media', $names);
        $this->assertContains('admin-update-blog-post', $names);
        $this->assertContains('admin-list-signups', $names);
        $this->assertContains('admin-list-companies', $names);
        $this->assertContains('admin-search-plugins', $names);
        $this->assertContains('admin-search-support-tickets', $names);
    }

    public function test_guest_authorize_redirects_to_admin_login_instead_of_500(): void
    {
        $client = $this->createMcpOAuthClient();
        $parameters = $this->mcpOAuthAuthorizationParameters(
            $client,
            str_repeat('a', 64),
            'mcp:admin',
            $this->mcpAdminOAuthResource(),
        );

        $this->get('/oauth/authorize?'.http_build_query($parameters))
            ->assertRedirect(route('filament.admin.auth.login'));

        $this->assertSame(url('/oauth/login'), route('login'));
        $this->get(route('login'))->assertRedirect('/admin/login');
    }

    public function test_non_admins_cannot_approve_admin_mcp_scope(): void
    {
        $user = User::factory()->create();
        $client = $this->createMcpOAuthClient();
        $parameters = $this->mcpOAuthAuthorizationParameters(
            $client,
            str_repeat('a', 64),
            'mcp:admin',
            $this->mcpAdminOAuthResource(),
        );

        $this->actingAs($user, 'web')
            ->get('/oauth/authorize?'.http_build_query($parameters))
            ->assertForbidden();
    }

    public function test_public_docs_mcp_message_endpoint_still_works_without_auth(): void
    {
        $this->postJson('/api/mcp/message', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => [],
        ])->assertOk()
            ->assertJsonPath('jsonrpc', '2.0');
    }

    public function test_create_blog_post_creates_unpublished_article_for_admin_author(): void
    {
        $tokens = $this->issueMcpOAuthTokens($this->admin);

        $response = $this->callMcpTool($tokens['access_token'], 'admin-create-blog-post', [
            'title' => 'Hello from Admin MCP',
            'content' => "# Hello\n\nDraft body.",
            'excerpt' => 'A draft excerpt',
        ])->assertOk();

        $text = data_get($response->json(), 'result.content.0.text')
            ?? data_get($response->json(), 'result.content.0')
            ?? $response->json('result');

        if (is_array($text)) {
            $payload = $text;
        } else {
            $payload = json_decode((string) $text, true);
        }

        $this->assertIsArray($payload);
        $this->assertSame('Hello from Admin MCP', $payload['title']);
        $this->assertFalse($payload['published']);
        $this->assertNull($payload['published_at']);
        $this->assertSame($this->admin->id, $payload['author_id']);

        $article = Article::query()->findOrFail($payload['id']);
        $this->assertNull($article->published_at);
        $this->assertSame($this->admin->id, $article->author_id);
        $this->assertSame('hello-from-admin-mcp', $article->slug);
    }

    public function test_create_blog_post_handles_duplicate_slugs(): void
    {
        Article::factory()->create([
            'author_id' => $this->admin->id,
            'slug' => 'duplicate-slug',
            'published_at' => null,
        ]);

        $tokens = $this->issueMcpOAuthTokens($this->admin);

        $response = $this->callMcpTool($tokens['access_token'], 'admin-create-blog-post', [
            'title' => 'Anything',
            'content' => 'Body',
            'slug' => 'duplicate-slug',
        ])->assertOk();

        $text = data_get($response->json(), 'result.content.0.text');
        $payload = json_decode((string) $text, true);

        $this->assertSame('duplicate-slug-1', $payload['slug']);
        $this->assertDatabaseHas('articles', [
            'slug' => 'duplicate-slug-1',
            'author_id' => $this->admin->id,
            'published_at' => null,
        ]);
    }

    public function test_list_signups_returns_users_created_today_in_new_york(): void
    {
        $today = User::factory()->create([
            'email' => 'new@acme.com',
            'created_at' => now('America/New_York'),
        ]);
        User::factory()->create([
            'email' => 'old@acme.com',
            'created_at' => now('America/New_York')->subDay(),
        ]);

        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $response = $this->callMcpTool($tokens['access_token'], 'admin-list-signups')->assertOk();
        $payload = json_decode((string) data_get($response->json(), 'result.content.0.text'), true);

        $emails = collect($payload['users'])->pluck('email');
        $this->assertTrue($emails->contains($today->email));
        $this->assertFalse($emails->contains('old@acme.com'));
        $this->assertSame('acme.com', collect($payload['users'])->firstWhere('email', $today->email)['company_domain']);
    }

    public function test_list_companies_rolls_up_email_domains(): void
    {
        User::factory()->create(['email' => 'a@widgets.io']);
        User::factory()->create(['email' => 'b@widgets.io']);
        User::factory()->create(['email' => 'c@gmail.com']);

        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $response = $this->callMcpTool($tokens['access_token'], 'admin-list-companies')->assertOk();
        $payload = json_decode((string) data_get($response->json(), 'result.content.0.text'), true);
        $domains = collect($payload['companies'])->pluck('domain');

        $this->assertTrue($domains->contains('widgets.io'));
        $this->assertFalse($domains->contains('gmail.com'));
    }

    public function test_search_plugins_includes_pending(): void
    {
        $pending = Plugin::factory()->pending()->create();
        Plugin::factory()->approved()->create();

        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $response = $this->callMcpTool($tokens['access_token'], 'admin-search-plugins', [
            'status' => 'pending',
        ])->assertOk();
        $payload = json_decode((string) data_get($response->json(), 'result.content.0.text'), true);

        $this->assertSame(1, $payload['count']);
        $this->assertSame($pending->name, $payload['plugins'][0]['name']);
        $this->assertSame('pending', $payload['plugins'][0]['status']);
    }

    public function test_search_support_tickets_returns_summaries(): void
    {
        $ticket = SupportTicket::factory()->create([
            'subject' => 'MCP cannot connect',
            'user_id' => User::factory(),
        ]);

        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $response = $this->callMcpTool($tokens['access_token'], 'admin-search-support-tickets', [
            'query' => 'MCP cannot',
        ])->assertOk();
        $payload = json_decode((string) data_get($response->json(), 'result.content.0.text'), true);

        $this->assertGreaterThanOrEqual(1, $payload['count']);
        $this->assertSame($ticket->mask, $payload['tickets'][0]['mask']);
    }

    public function test_update_blog_post_patches_title_and_content_on_draft(): void
    {
        $article = Article::factory()->create([
            'author_id' => $this->admin->id,
            'slug' => 'draft-to-update',
            'title' => 'Old Title',
            'content' => 'Old content',
            'excerpt' => 'Old excerpt',
            'published_at' => null,
        ]);

        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $response = $this->callMcpTool($tokens['access_token'], 'admin-update-blog-post', [
            'id' => $article->id,
            'title' => 'Updated Title',
            'content' => "# Updated\n\nNew body.",
        ])->assertOk();

        $this->assertFalse($response->json('result.isError'));
        $payload = json_decode((string) data_get($response->json(), 'result.content.0.text'), true);

        $this->assertSame('Updated Title', $payload['title']);
        $this->assertSame("# Updated\n\nNew body.", $payload['content']);
        $this->assertSame('Old excerpt', $payload['excerpt']);
        $this->assertSame('draft-to-update', $payload['slug']);
        $this->assertFalse($payload['published']);
        $this->assertNull($payload['published_at']);

        $article->refresh();
        $this->assertSame('Updated Title', $article->title);
        $this->assertSame("# Updated\n\nNew body.", $article->content);
        $this->assertNull($article->published_at);
    }

    public function test_update_blog_post_by_slug_lookup(): void
    {
        $article = Article::factory()->create([
            'author_id' => $this->admin->id,
            'slug' => 'lookup-by-slug',
            'title' => 'Before',
            'published_at' => null,
        ]);

        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $response = $this->callMcpTool($tokens['access_token'], 'admin-update-blog-post', [
            'slug' => 'lookup-by-slug',
            'excerpt' => 'Patched excerpt only',
        ])->assertOk();

        $payload = json_decode((string) data_get($response->json(), 'result.content.0.text'), true);
        $this->assertSame('Patched excerpt only', $payload['excerpt']);
        $this->assertSame('Before', $payload['title']);
        $this->assertSame($article->id, $payload['id']);
    }

    public function test_update_blog_post_rejects_empty_update(): void
    {
        $article = Article::factory()->create([
            'author_id' => $this->admin->id,
            'published_at' => null,
        ]);

        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $response = $this->callMcpTool($tokens['access_token'], 'admin-update-blog-post', [
            'id' => $article->id,
        ])->assertOk();

        $this->assertTrue($response->json('result.isError'));
        $text = (string) data_get($response->json(), 'result.content.0.text');
        $this->assertStringContainsString('Provide at least one field to update', $text);
    }

    public function test_update_blog_post_not_found(): void
    {
        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $response = $this->callMcpTool($tokens['access_token'], 'admin-update-blog-post', [
            'id' => 999999,
            'title' => 'Nope',
        ])->assertOk();

        $this->assertTrue($response->json('result.isError'));
        $text = (string) data_get($response->json(), 'result.content.0.text');
        $this->assertStringContainsString('Article not found', $text);
    }

    public function test_update_blog_post_rejects_slug_change_when_published(): void
    {
        $article = Article::factory()->published()->create([
            'author_id' => $this->admin->id,
            'slug' => 'published-slug',
        ]);

        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $response = $this->callMcpTool($tokens['access_token'], 'admin-update-blog-post', [
            'id' => $article->id,
            'slug' => 'new-published-slug',
        ])->assertOk();

        $this->assertTrue($response->json('result.isError'));
        $text = (string) data_get($response->json(), 'result.content.0.text');
        $this->assertStringContainsString('cannot be changed after the article is published', $text);
        $this->assertSame('published-slug', $article->fresh()->slug);
    }

    public function test_update_blog_post_rejects_slug_conflict(): void
    {
        Article::factory()->create([
            'author_id' => $this->admin->id,
            'slug' => 'taken-slug',
            'published_at' => null,
        ]);
        $article = Article::factory()->create([
            'author_id' => $this->admin->id,
            'slug' => 'editable-slug',
            'published_at' => null,
        ]);

        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $response = $this->callMcpTool($tokens['access_token'], 'admin-update-blog-post', [
            'id' => $article->id,
            'slug' => 'taken-slug',
        ])->assertOk();

        $this->assertTrue($response->json('result.isError'));
        $text = (string) data_get($response->json(), 'result.content.0.text');
        $this->assertStringContainsString('already taken', $text);
        $this->assertSame('editable-slug', $article->fresh()->slug);
    }

    public function test_update_blog_post_can_rename_draft_slug(): void
    {
        $article = Article::factory()->create([
            'author_id' => $this->admin->id,
            'slug' => 'old-draft-slug',
            'published_at' => null,
        ]);

        $tokens = $this->issueMcpOAuthTokens($this->admin);
        $response = $this->callMcpTool($tokens['access_token'], 'admin-update-blog-post', [
            'id' => $article->id,
            'slug' => 'new-draft-slug',
        ])->assertOk();

        $this->assertFalse($response->json('result.isError'));
        $payload = json_decode((string) data_get($response->json(), 'result.content.0.text'), true);
        $this->assertSame('new-draft-slug', $payload['slug']);
        $this->assertSame('new-draft-slug', $article->fresh()->slug);
    }
}
