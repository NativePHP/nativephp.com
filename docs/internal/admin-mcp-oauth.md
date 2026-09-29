# NativePHP Admin MCP (OAuth)

Internal note — this is **not** the public docs MCP.

## Endpoints

| Purpose | URL |
| --- | --- |
| Admin MCP (SSE/HTTP) | `/mcp/oauth/admin` |
| Protected resource metadata | `/.well-known/oauth-protected-resource/mcp/oauth/admin` |
| Authorization server metadata | `/.well-known/oauth-authorization-server` |
| Dynamic client registration | `POST /oauth/register` |

**Scope:** `mcp:admin` (site admins only — `User::isAdmin()` / `FILAMENT_USERS`).

The public docs MCP at `/api/mcp/message` stays unauthenticated and unchanged.

## Connect (Cursor etc.)

1. Ensure Passport keys exist: `php artisan passport:keys` (or set `PASSPORT_PRIVATE_KEY` / `PASSPORT_PUBLIC_KEY`).
2. Point the MCP client at `https://nativephp.com/mcp/oauth/admin` (or your local Herd URL).
3. Complete the OAuth PKCE flow when prompted; approve only while signed in as a Filament admin.
4. Clients discover auth via the well-known protected-resource document (`scope=mcp:admin`).

## Tools (v1)

- Blog: `admin-create-blog-post` (unpublished only), `admin-get-blog-post`, `admin-list-blog-posts`
- Users: `admin-list-signups`, `admin-search-users`, `admin-get-user`
- Companies: `admin-list-companies`, `admin-get-company`
- Plugins/sales: `admin-search-plugins`, `admin-sales-summary`
- Support: `admin-search-support-tickets`, `admin-get-support-ticket`

Never expect license keys, Stripe secrets, passwords, or GitHub tokens from these tools.
