Everything published in the NativePHP documentation — for both
[Mobile](/docs/mobile/getting-started/introduction) and
[Desktop](/docs/desktop/getting-started/introduction) — is available over
[MCP](https://modelcontextprotocol.io). Any agent that speaks the protocol —
Claude Code, Cursor, Copilot, and friends — can search and read the docs while
it works, instead of relying on whatever it happened to memorise during training.

It's hosted by us. There's nothing to install and no API key to create; just
point your agent at this URL:

```
https://nativephp.com/api/mcp/message
```

## Adding it to your agent

### Claude Code

Add it from the terminal:

```shell
claude mcp add --transport http nativephp-docs https://nativephp.com/api/mcp/message
```

Or commit the config to your repo as `.mcp.json` so everyone on the project
picks it up automatically:

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

Create `.cursor/mcp.json` in your project, or `~/.cursor/mcp.json` to enable it
everywhere:

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

### VS Code and GitHub Copilot

Create `.vscode/mcp.json`. Note that VS Code uses `servers` where the others
use `mcpServers`:

```json
{
    "servers": {
        "nativephp-docs": {
            "type": "http",
            "url": "https://nativephp.com/api/mcp/message"
        }
    }
}
```

### Agents that only speak stdio

Some agents can't talk to a remote server directly. Bridge to it with
`mcp-remote`:

```json
{
    "mcpServers": {
        "nativephp-docs": {
            "command": "npx",
            "args": [
                "-y",
                "mcp-remote",
                "https://nativephp.com/api/mcp/message"
            ]
        }
    }
}
```

The server speaks the Streamable HTTP transport over a single endpoint, so
`/api/mcp/message` is the only URL your agent needs.

## What your agent can do

Once connected, your agent gets these tools.

### `search_docs`

Full-text search across every platform and version. Takes a `query`, plus an
optional `platform` (`mobile` or `desktop`), `version`, and `limit` (10 by
default). Every result comes back with its title, section, a matching snippet,
and the path you'd hand to `get_page`.

This is the one agents reach for most. _"How do I ask for camera permission?"_
gets answered from the docs rather than from memory.

### `get_page`

Fetches a full page by its `path`, in `platform/version/section/slug` form — for
example `mobile/4/the-basics/device`, or `mobile/4/plugins/core/camera` where the
section is itself nested. Paths come straight out of `search_docs` results, so
agents generally chain the two.

### `get_navigation`

Returns the whole sidebar for a `platform` and `version`, grouped by section and
in the order you see it on the site. Useful when an agent wants to orient itself
before searching, or to check whether a topic is documented at all.

### `list_apis`

Lists the pages in a version's `apis` section. That section only exists in the
Mobile v1 and v2 docs — from v3 onwards the native APIs are documented under
Plugins, and the Desktop docs have no `apis` section at all. For anything
current, use `get_navigation` or `search_docs` instead.

## Reading pages without MCP

Every docs page is served as raw markdown by adding `.md` to its URL, which is
often the quickest way to hand an agent one specific page:

```shell
curl https://nativephp.com/docs/mobile/4/plugins/core/camera.md
```

There's also a small REST API, if you'd rather script against it than wire up an
MCP client:

- `/api/mcp/search?q=camera&platform=mobile` — search results as JSON
- `/api/mcp/page/{platform}/{version}/{section}/{slug}` — a single page
- `/api/mcp/navigation/{platform}/{version}` — the docs navigation tree
- `/api/mcp/apis/{platform}/{version}` — the `apis` section listing
- `/api/mcp/health` — liveness check, and the versions currently published

Both the MCP and REST endpoints are rate limited to 60 requests per minute per
IP address.

## Pair it with Laravel Boost

This server tells your agent what NativePHP _can_ do.
[Laravel Boost](https://laravel.com/ai/boost) tells it about _your_ application:
your routes, models, config, and installed package versions. They complement
each other, and running both gives noticeably better results than either alone.
