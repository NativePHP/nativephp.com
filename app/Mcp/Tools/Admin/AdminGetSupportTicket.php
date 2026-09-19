<?php

namespace App\Mcp\Tools\Admin;

use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Models\SupportTicket;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('admin-get-support-ticket')]
#[Description('Get a support ticket summary by id or mask, including recent replies (no attachment binaries).')]
#[IsReadOnly]
class AdminGetSupportTicket extends Tool
{
    use RequiresAdmin;

    public function handle(Request $request): Response
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $validated = $request->validate([
            'id' => ['nullable', 'integer', 'min:1'],
            'mask' => ['nullable', 'string', 'max:100'],
        ]);

        if (empty($validated['id']) && empty($validated['mask'])) {
            return Response::error('Provide id or mask.');
        }

        $ticket = SupportTicket::query()
            ->with(['user:id,name,email', 'replies' => fn ($q) => $q->latest('id')->limit(10)])
            ->when(! empty($validated['id']), fn ($q) => $q->where('id', $validated['id']))
            ->when(! empty($validated['mask']), fn ($q) => $q->where('mask', $validated['mask']))
            ->first();

        if (! $ticket) {
            return Response::error('Support ticket not found.');
        }

        return Response::text($this->toJson([
            'id' => $ticket->id,
            'mask' => $ticket->mask,
            'subject' => $ticket->subject,
            'message' => $ticket->message,
            'status' => $ticket->status?->value ?? $ticket->status,
            'product' => $ticket->product,
            'issue_type' => $ticket->issue_type,
            'user' => [
                'id' => $ticket->user?->id,
                'name' => $ticket->user?->name,
                'email' => $ticket->user?->email,
            ],
            'created_at' => optional($ticket->created_at)?->toIso8601String(),
            'updated_at' => optional($ticket->updated_at)?->toIso8601String(),
            'recent_replies' => $ticket->replies->map(fn ($reply): array => [
                'id' => $reply->id,
                'note' => (bool) ($reply->note ?? false),
                'pinned' => (bool) ($reply->pinned ?? false),
                'user_id' => $reply->user_id ?? null,
                'created_at' => optional($reply->created_at)?->toIso8601String(),
                'body_excerpt' => str($reply->message ?? '')->limit(500)->toString(),
            ])->values()->all(),
        ]));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('Ticket id.'),
            'mask' => $schema->string()->description('Public ticket mask/reference.'),
        ];
    }
}
