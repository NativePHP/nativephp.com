---
title: MCP Docs Server
order: 275
---

The full NativePHP documentation — for both [Mobile](/docs/mobile/getting-started/introduction) and [Desktop](/docs/desktop/getting-started/introduction) — is available over [MCP](https://modelcontextprotocol.io). Agents such as Claude Code, Cursor, and Copilot can search and read the docs while they work, instead of relying on training-data memory alone.

It's hosted by NativePHP. There's nothing to install and no API key to create — just point your agent at this URL:

```
https://nativephp.com/api/mcp/message
```

## Quick connect

### Claude Code

```shell
claude mcp add --transport http nativephp-docs https://nativephp.com/api/mcp/message
```

Or commit `.mcp.json` to your repo so the whole team picks it up:

```json
{
    "mcpServers": {
        "nativephp-docs": {
            "type": "http",
            "url": "https://nativephp.com/api/mcp/message"
        }
    }
}
```

### Cursor

Create `.cursor/mcp.json` in your project, or `~/.cursor/mcp.json` to enable it everywhere:

```json
{
    "mcpServers": {
        "nativephp-docs": {
            "type": "http",
            "url": "https://nativephp.com/api/mcp/message"
        }
    }
}
```

## What your agent can do

Once connected, your agent gets these tools:

- **`search_docs`** — full-text search across every platform and version (use this for current Mobile v4 docs)
- **`get_page`** — fetch a full page by path (e.g. `mobile/4/plugins/core/camera`)
- **`get_navigation`** — the sidebar for a platform and version
- **`search_plugins`** — search the public plugin marketplace (where Mobile v3+ native APIs live)
- **`get_plugin`** — fetch one marketplace plugin by composer name
- **`list_apis`** — list pages in a version's legacy `apis` section (**Mobile v1/v2 only**). From Mobile v3 onward there is no `apis` section — native capabilities are documented under [Plugins](/docs/mobile/4/plugins/introduction). Prefer `search_docs`, `get_navigation`, or `search_plugins` for the latest docs.

## Pair it with Laravel Boost

This MCP tells your agent what NativePHP _can_ do. [Laravel Boost](https://laravel.com/ai/boost) tells it about _your_ application — routes, models, config, and package versions. Running both together works better than either alone.

## More clients and details

For VS Code / Copilot, `mcp-remote`, REST mirrors, rate limits, and the full client matrix, see the [Docs MCP Server](/mcp) page.
