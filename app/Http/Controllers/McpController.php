<?php

namespace App\Http\Controllers;

use App\Services\DocsSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class McpController extends Controller
{
    public function __construct(
        protected DocsSearchService $docsSearch
    ) {}

    /**
     * SSE endpoint for MCP clients
     */
    public function sse(Request $request): StreamedResponse
    {
        $sessionId = Str::uuid()->toString();

        return response()->stream(function () use ($sessionId) {
            // Send session info
            $this->sendSseEvent([
                'type' => 'session',
                'sessionId' => $sessionId,
            ]);

            // Send server info
            $this->sendSseEvent([
                'type' => 'serverInfo',
                'name' => 'nativephp-docs',
                'version' => '1.0.0',
                'capabilities' => ['tools' => new \stdClass],
            ]);

            // Send available tools
            $this->sendSseEvent([
                'type' => 'tools',
                'tools' => $this->getToolDefinitions(),
            ]);

            // Keep connection alive
            while (true) {
                if (connection_aborted()) {
                    break;
                }
                echo ": keepalive\n\n";
                ob_flush();
                flush();
                sleep(30);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

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
                'tools/call' => $this->handleToolCall($params['name'] ?? '', $params['arguments'] ?? []),
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

    public function searchApi(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $platform = $request->input('platform');
        $version = $request->input('version');
        $limit = (int) $request->input('limit', 10);

        if (empty($query)) {
            return response()->json(['error' => 'Missing query parameter: q'], 400);
        }

        $results = $this->docsSearch->search($query, $platform, $version, $limit);

        return response()->json(['results' => $results]);
    }

    public function pageApi(string $platform, string $version, string $section, string $slug): JsonResponse
    {
        $page = $this->docsSearch->getPage($platform, $version, $section, $slug);

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
                'description' => 'Get full content of a documentation page by path (e.g., "mobile/3/apis/camera")',
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

    protected function sendSseEvent(array $data): void
    {
        echo 'data: '.json_encode($data)."\n\n";
        ob_flush();
        flush();
    }
}
