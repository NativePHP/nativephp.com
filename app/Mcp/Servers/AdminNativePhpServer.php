<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\Admin\AdminCreateBlogPost;
use App\Mcp\Tools\Admin\AdminGetBlogPost;
use App\Mcp\Tools\Admin\AdminGetCompany;
use App\Mcp\Tools\Admin\AdminGetSupportTicket;
use App\Mcp\Tools\Admin\AdminGetUser;
use App\Mcp\Tools\Admin\AdminListBlogPosts;
use App\Mcp\Tools\Admin\AdminListCompanies;
use App\Mcp\Tools\Admin\AdminListSignups;
use App\Mcp\Tools\Admin\AdminPublishBlogPost;
use App\Mcp\Tools\Admin\AdminSalesSummary;
use App\Mcp\Tools\Admin\AdminSearchPlugins;
use App\Mcp\Tools\Admin\AdminSearchSupportTickets;
use App\Mcp\Tools\Admin\AdminSearchUsers;
use App\Mcp\Tools\Admin\AdminUpdateBlogPost;
use App\Mcp\Tools\Admin\AdminUploadMedia;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Tool;

class AdminNativePhpServer extends Server
{
    protected string $name = 'NativePHP Admin';

    protected string $version = '1.0.0';

    protected string $instructions = <<<'MARKDOWN'
        NativePHP Admin MCP is for site-admin support use only (OAuth scope `mcp:admin`).

        Rules:
        - Never return passwords, remember tokens, GitHub tokens, license keys, Stripe secrets, or raw API credentials.
        - Blog: create/update drafts; publish via admin-publish-blog-post (sets published_at like Filament; idempotent if already live). Slug changes refused once published. No unpublish tool yet.
        - Media: upload via admin-upload-media to the public disk. Pass `content` as a JSON string arg (raw base64, no data: prefix). Prefer image/webp. Default directory is website-images; pass directory: "blog/heroes" for Filament article heroes. Optionally attach in one shot with article_id/slug only when directory is blog/heroes. Do not put base64 in freeform prose (transport may mangle + into spaces; on invalid base64 the tool notes this). Or set hero on admin-update-blog-post via media_id/path or URL.
        - Other tools are read-only ops helpers (signups, users, companies, plugins, sales summaries, support).

        Tools:
        1. admin-create-blog-post — create an unpublished article.
        2. admin-upload-media — upload an image (JSON-arg raw base64 → public disk WebP; prefer image/webp; no data: prefix; do not put base64 in prose); optional directory (default website-images); for heroes pass directory: "blog/heroes" with slug/article_id in the same call to attach.
        3. admin-update-blog-post — patch title/content/excerpt/slug/hero (no publish/unpublish).
        4. admin-publish-blog-post — publish by id/slug (optional published_at; default now; idempotent).
        5. admin-get-blog-post / admin-list-blog-posts — inspect drafts and published posts.
        6. admin-list-signups / admin-search-users / admin-get-user — user support lookups.
        7. admin-list-companies / admin-get-company — email-domain company rollups.
        8. admin-search-plugins / admin-sales-summary — marketplace ops + attributable revenue (plugins, products, Ultra Cashier; no secrets).
        9. admin-search-support-tickets / admin-get-support-ticket — support summaries.
    MARKDOWN;

    /**
     * @var array<int, class-string<Tool>>
     */
    protected array $tools = [
        AdminCreateBlogPost::class,
        AdminUploadMedia::class,
        AdminUpdateBlogPost::class,
        AdminPublishBlogPost::class,
        AdminGetBlogPost::class,
        AdminListBlogPosts::class,
        AdminListSignups::class,
        AdminSearchUsers::class,
        AdminGetUser::class,
        AdminListCompanies::class,
        AdminGetCompany::class,
        AdminSearchPlugins::class,
        AdminSalesSummary::class,
        AdminSearchSupportTickets::class,
        AdminGetSupportTicket::class,
    ];
}
