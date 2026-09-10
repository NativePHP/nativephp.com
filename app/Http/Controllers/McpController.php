<?php

namespace App\Http\Controllers;

use App\Http\Requests\McpPluginSearchRequest;
use App\Http\Requests\McpSearchRequest;
use App\Services\DocsSearchService;
use App\Services\PluginSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class McpController extends Controller
{
    public function __construct(
        protected DocsSearchService $docsSearch,
        protected PluginSearchService $pluginSearch,
    ) {}

    /**
     * JSON-RPC message endpoint for tool calls
     */
    public function message(Request $request): JsonResponse
    {
        $method = $request->input('method');
        $params = $request->input('params', []);
        $id = $request->input('id');

        try {
            $result = match ($method) {
                'initialize' => $this->handleInitialize($params),
                'notifications/initialized' => new \stdClass,
                'ping' => new \stdClass,
                'tools/list' => ['tools' => $this->getToolDefinitions()],
                'tools/call' => $this->handleToolCall(
                    $params['name'] ?? '',
                    $params['arguments'] ?? [],
                ),
                default => throw new \InvalidArgumentException("Unknown method: {$method}"),
            };

            return response()->json([
                'jsonrpc' => '2.0',
                'id' => $id,
                'result' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'jsonrpc' => '2.0',
                'id' => $id,
                'error' => [
                    'code' => -32000,
                    'message' => $e->getMessage(),
                ],
            ]);
        }
    }

    /**
     * Health check endpoint
     */
    public function health(): JsonResponse
    {
        $versions = $this->docsSearch->getVersions();
        $pageCount = count($this->docsSearch->search('', null, null, 1000));

        return response()->json([
            'status' => 'ok',
            'versions' => $versions,
            'pages' => $pageCount,
        ]);
    }

    // REST API endpoints for simpler integrations

    public function searchApi(McpSearchRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $results = $this->docsSearch->search(
            $validated['q'],
            $validated['platform'] ?? null,
            $validated['version'] ?? null,
            $validated['limit'] ?? 10
        );

        return response()->json(['results' => $results]);
    }

    public function pageApi(string $platform, string $version, string $path): JsonResponse
    {
        $page = $this->docsSearch->getPageByPath("{$platform}/{$version}/{$path}");

        if (! $page) {
            return response()->json(['error' => 'Page not found'], 404);
        }

        return response()->json(['page' => $page]);
    }

    public function apisApi(string $platform, string $version): JsonResponse
    {
        $apis = $this->docsSearch->listApis($platform, $version);

        return response()->json(['apis' => $apis]);
    }

    public function navigationApi(string $platform, string $version): JsonResponse
    {
        $nav = $this->docsSearch->getNavigation($platform, $version);

        return response()->json(['navigation' => $nav]);
    }

    public function pluginsSearchApi(McpPluginSearchRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $results = $this->pluginSearch->search(
            $validated['q'],
            $validated['type'] ?? null,
            $validated['limit'] ?? PluginSearchService::DEFAULT_LIMIT,
        );

        return response()->json(['plugins' => $results]);
    }

    public function pluginShowApi(string $vendor, string $package): JsonResponse
    {
        $plugin = $this->pluginSearch->getByVendorPackage($vendor, $package);

        if (! $plugin) {
            return response()->json(['error' => 'Plugin not found'], 404);
        }

        return response()->json(['plugin' => $plugin]);
    }

    protected function getToolDefinitions(): array
    {
        $latestVersions = $this->docsSearch->getLatestVersions();

        return [
            [
                'name' => 'search_docs',
                'description' => "Search NativePHP documentation. Latest versions: desktop v{$latestVersions['desktop']}, mobile v{$latestVersions['mobile']}.",
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => [
                            'type' => 'string',
                            'description' => 'Search query (e.g., "camera permissions", "window management")',
                        ],
                        'platform' => [
                            'type' => 'string',
                            'enum' => ['desktop', 'mobile'],
                            'description' => 'Filter by platform (optional)',
                        ],
                        'version' => [
                            'type' => 'string',
                            'description' => 'Filter by version number (optional)',
                        ],
                        'limit' => [
                            'type' => 'number',
                            'description' => 'Max results to return (default: 10)',
                        ],
                    ],
                    'required' => ['query'],
                ],
            ],
            [
                'name' => 'get_page',
                'description' => 'Get full content of a documentation page by path (e.g., "mobile/4/plugins/core/camera")',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'path' => [
                            'type' => 'string',
                            'description' => 'Page path: platform/version/section/slug',
                        ],
                    ],
                    'required' => ['path'],
                ],
            ],
            [
                'name' => 'list_apis',
                'description' => 'List all native APIs for a platform/version',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'platform' => [
                            'type' => 'string',
                            'enum' => ['desktop', 'mobile'],
                            'description' => 'Platform to list APIs for',
                        ],
                        'version' => [
                            'type' => 'string',
                            'description' => 'Version number',
                        ],
                    ],
                    'required' => ['platform', 'version'],
                ],
            ],
            [
                'name' => 'get_navigation',
                'description' => 'Get the docs navigation structure for a platform/version',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'platform' => [
                            'type' => 'string',
                            'enum' => ['desktop', 'mobile'],
                            'description' => 'Platform',
                        ],
                        'version' => [
                            'type' => 'string',
                            'description' => 'Version number',
                        ],
                    ],
                    'required' => ['platform', 'version'],
                ],
            ],
            [
                'name' => 'search_plugins',
                'description' => 'Search the NativePHP plugin marketplace for approved, publicly listed plugins. Returns composer package names, free/paid type, price, and marketplace URLs.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => [
                            'type' => 'string',
                            'description' => 'Search query matched against plugin name and description (e.g., "camera", "push notifications")',
                        ],
                        'type' => [
                            'type' => 'string',
                            'enum' => ['free', 'paid'],
                            'description' => 'Filter by marketplace type (optional)',
                        ],
                        'limit' => [
                            'type' => 'number',
                            'description' => 'Max results to return (default: 10, max: 25)',
                        ],
                    ],
                    'required' => ['query'],
                ],
            ],
            [
                'name' => 'get_plugin',
                'description' => 'Get details for one marketplace plugin by composer name (vendor/package) or vendor + package path args.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => [
                            'type' => 'string',
                            'description' => 'Composer package name, e.g. "nativephp/camera"',
                        ],
                        'vendor' => [
                            'type' => 'string',
                            'description' => 'Composer vendor segment (use with package)',
                        ],
                        'package' => [
                            'type' => 'string',
                            'description' => 'Composer package segment (use with vendor)',
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function handleInitialize(array $params): array
    {
        return [
            'protocolVersion' => '2024-11-05',
            'capabilities' => [
                'tools' => new \stdClass,
            ],
            'serverInfo' => [
                'name' => 'nativephp-docs',
                'version' => '1.0.0',
            ],
        ];
    }

    protected function handleToolCall(string $name, array $args): array
    {
        return match ($name) {
            'search_docs' => $this->toolSearchDocs($args),
            'get_page' => $this->toolGetPage($args),
            'list_apis' => $this->toolListApis($args),
            'get_navigation' => $this->toolGetNavigation($args),
            'search_plugins' => $this->toolSearchPlugins($args),
            'get_plugin' => $this->toolGetPlugin($args),
            default => [
                'content' => [['type' => 'text', 'text' => "Unknown tool: {$name}"]],
                'isError' => true,
            ],
        };
    }

    protected function toolSearchDocs(array $args): array
    {
        $query = $args['query'] ?? '';
        $platform = $args['platform'] ?? null;
        $version = $args['version'] ?? null;
        $limit = $args['limit'] ?? 10;

        $results = $this->docsSearch->search($query, $platform, $version, $limit);

        if (empty($results)) {
            $filterDesc = '';
            if ($platform) {
                $filterDesc .= " in {$platform}";
            }
            if ($version) {
                $filterDesc .= " v{$version}";
            }

            return [
                'content' => [['type' => 'text', 'text' => "No results found for \"{$query}\"{$filterDesc}"]],
            ];
        }

        $formatted = collect($results)->map(function ($r, $i) {
            $num = $i + 1;

            return "{$num}. **{$r['title']}** ({$r['platform']}/v{$r['version']}/{$r['section']})\n   Path: {$r['id']}\n   {$r['snippet']}";
        })->join("\n\n");

        return [
            'content' => [['type' => 'text', 'text' => 'Found '.count($results)." results for \"{$query}\":\n\n{$formatted}"]],
        ];
    }

    protected function toolGetPage(array $args): array
    {
        $path = $args['path'] ?? '';
        $page = $this->docsSearch->getPageByPath($path);

        if (! $page) {
            return [
                'content' => [['type' => 'text', 'text' => "Page not found: {$path}"]],
            ];
        }

        $text = "# {$page['title']}\n\n";
        $text .= "**Platform:** {$page['platform']} | **Version:** {$page['version']} | **Section:** {$page['section']}\n\n";
        $text .= $page['content'];

        return [
            'content' => [['type' => 'text', 'text' => $text]],
        ];
    }

    protected function toolListApis(array $args): array
    {
        $platform = $args['platform'] ?? '';
        $version = $args['version'] ?? '';

        $apis = $this->docsSearch->listApis($platform, $version);

        if (empty($apis)) {
            return [
                'content' => [['type' => 'text', 'text' => "No APIs found for {$platform} v{$version}"]],
            ];
        }

        $formatted = collect($apis)->map(function ($api) {
            $desc = $api['description'] ?: 'No description';

            return "- **{$api['title']}** ({$api['slug']})\n  {$desc}";
        })->join("\n");

        return [
            'content' => [['type' => 'text', 'text' => "# {$platform} v{$version} APIs\n\n{$formatted}"]],
        ];
    }

    protected function toolGetNavigation(array $args): array
    {
        $platform = $args['platform'] ?? '';
        $version = $args['version'] ?? '';

        $nav = $this->docsSearch->getNavigation($platform, $version);

        if (empty($nav)) {
            return [
                'content' => [['type' => 'text', 'text' => "No navigation found for {$platform} v{$version}"]],
            ];
        }

        $formatted = collect($nav)->map(function ($pages, $section) {
            $pageList = collect($pages)->map(fn ($p) => "  - {$p['title']} ({$p['slug']})")->join("\n");

            return "## {$section}\n{$pageList}";
        })->join("\n\n");

        return [
            'content' => [['type' => 'text', 'text' => "# {$platform} v{$version} Navigation\n\n{$formatted}"]],
        ];
    }

    protected function toolSearchPlugins(array $args): array
    {
        $query = (string) ($args['query'] ?? '');
        $type = isset($args['type']) ? (string) $args['type'] : null;
        $limit = isset($args['limit']) ? (int) $args['limit'] : PluginSearchService::DEFAULT_LIMIT;

        if ($type !== null && ! in_array($type, ['free', 'paid'], true)) {
            return [
                'content' => [['type' => 'text', 'text' => 'Invalid type. Use "free" or "paid".']],
                'isError' => true,
            ];
        }

        $results = $this->pluginSearch->search($query, $type, $limit);

        if (empty($results)) {
            $filterDesc = $type ? " (type: {$type})" : '';

            return [
                'content' => [['type' => 'text', 'text' => "No marketplace plugins found for \"{$query}\"{$filterDesc}"]],
            ];
        }

        $formatted = collect($results)->map(function (array $plugin, int $i): string {
            return ($i + 1).'. '.$this->formatPluginResultText($plugin, detailed: false);
        })->join("\n\n");

        return [
            'content' => [['type' => 'text', 'text' => 'Found '.count($results)." marketplace plugins for \"{$query}\":\n\n{$formatted}"]],
        ];
    }

    protected function toolGetPlugin(array $args): array
    {
        $name = isset($args['name']) ? (string) $args['name'] : '';
        $vendor = isset($args['vendor']) ? (string) $args['vendor'] : '';
        $package = isset($args['package']) ? (string) $args['package'] : '';

        $plugin = null;

        if ($name !== '') {
            $plugin = $this->pluginSearch->getByName($name);
            $lookup = $name;
        } elseif ($vendor !== '' && $package !== '') {
            $plugin = $this->pluginSearch->getByVendorPackage($vendor, $package);
            $lookup = "{$vendor}/{$package}";
        } else {
            return [
                'content' => [['type' => 'text', 'text' => 'Provide either name (vendor/package) or both vendor and package.']],
                'isError' => true,
            ];
        }

        if (! $plugin) {
            return [
                'content' => [['type' => 'text', 'text' => "Plugin not found: {$lookup}"]],
                'isError' => true,
            ];
        }

        $text = "# {$plugin['name']}\n\n";
        if ($plugin['description']) {
            $text .= "{$plugin['description']}\n\n";
        }
        $text .= $this->formatPluginResultText($plugin, detailed: true);

        return [
            'content' => [['type' => 'text', 'text' => $text]],
        ];
    }

    /**
     * @param  array<string, mixed>  $plugin
     */
    protected function formatPluginResultText(array $plugin, bool $detailed): string
    {
        $lines = [];

        if (! $detailed) {
            $lines[] = "**{$plugin['name']}**";
        }

        if ($plugin['type'] === 'paid') {
            $lines[] = 'Type: paid';
            $lines[] = 'Price: '.($plugin['price'] ?: 'paid (price not listed)');
            $lines[] = "Marketplace: {$plugin['marketplace_url']}";
        } else {
            $lines[] = 'Type: free';
            $lines[] = "Marketplace: {$plugin['marketplace_url']}";
        }

        if ($detailed) {
            if ($plugin['latest_version']) {
                $lines[] = "Latest version: {$plugin['latest_version']}";
            }

            $flags = collect([
                $plugin['featured'] ? 'featured' : null,
                $plugin['is_official'] ? 'official' : null,
                $plugin['works_in_jump'] ? 'works in Jump' : null,
            ])->filter()->implode(', ');

            if ($flags !== '') {
                $lines[] = "Flags: {$flags}";
            }

            if (! empty($plugin['repository_url'])) {
                $lines[] = "Repository: {$plugin['repository_url']}";
            }

            if (! empty($plugin['packagist_url'])) {
                $lines[] = "Packagist: {$plugin['packagist_url']}";
            }
        } else {
            if ($plugin['description']) {
                $lines[] = $plugin['description'];
            }
            if ($plugin['latest_version']) {
                $lines[] = "Latest: {$plugin['latest_version']}";
            }
        }

        if ($detailed) {
            return implode("\n", $lines);
        }

        return implode("\n   ", $lines);
    }
}
