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
use App\Mcp\Tools\Admin\AdminSalesSummary;
use App\Mcp\Tools\Admin\AdminSearchPlugins;
use App\Mcp\Tools\Admin\AdminSearchSupportTickets;
use App\Mcp\Tools\Admin\AdminSearchUsers;
use App\Mcp\Tools\Admin\AdminUpdateBlogPost;
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
        - Blog: create/update unpublished drafts in v1 (no publish tool; slug changes refused once published).
        - Other tools are read-only ops helpers (signups, users, companies, plugins, sales summaries, support).

        Tools:
        1. admin-create-blog-post — create an unpublished article.
        2. admin-update-blog-post — patch title/content/excerpt/slug (no publish/unpublish).
        3. admin-get-blog-post / admin-list-blog-posts — inspect drafts and published posts.
        4. admin-list-signups / admin-search-users / admin-get-user — user support lookups.
        5. admin-list-companies / admin-get-company — email-domain company rollups.
        6. admin-search-plugins / admin-sales-summary — marketplace/plugin ops (no secrets).
        7. admin-search-support-tickets / admin-get-support-ticket — support summaries.
    MARKDOWN;

    /**
     * @var array<int, class-string<Tool>>
     */
    protected array $tools = [
        AdminCreateBlogPost::class,
        AdminUpdateBlogPost::class,
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
