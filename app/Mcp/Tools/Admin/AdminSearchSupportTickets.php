<?php

namespace App\Mcp\Tools\Admin;

use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Models\SupportTicket;
use App\SupportTicket\Status;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('admin-search-support-tickets')]
#[Description('Search support tickets by subject/mask/email and optional status/product filters. Read-only summary.')]
#[IsReadOnly]
class AdminSearchSupportTickets extends Tool
{
    use RequiresAdmin;

    public function handle(Request $request): Response
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $validated = $request->validate([
            'query' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:open,in_progress,on_hold,responded,closed'],
            'product' => ['nullable', 'string', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int) ($validated['limit'] ?? 25);

        $query = SupportTicket::query()
            ->with('user:id,name,email')
            ->latest('id');

        if (! empty($validated['status'])) {
            $query->where('status', Status::from($validated['status']));
        }

        if (! empty($validated['product'])) {
            $query->where('product', $validated['product']);
        }

        if (! empty($validated['query'])) {
            $like = '%'.$validated['query'].'%';
            $query->where(function ($builder) use ($like): void {
                $builder->where('subject', 'like', $like)
                    ->orWhere('mask', 'like', $like)
                    ->orWhere('message', 'like', $like)
                    ->orWhereHas('user', function ($user) use ($like): void {
                        $user->where('email', 'like', $like)
                            ->orWhere('name', 'like', $like);
                    });
            });
        }

        $tickets = $query->limit($limit)->get()->map(fn (SupportTicket $ticket): array => [
            'id' => $ticket->id,
            'mask' => $ticket->mask,
            'subject' => $ticket->subject,
            'status' => $ticket->status?->value ?? $ticket->status,
            'product' => $ticket->product,
            'issue_type' => $ticket->issue_type,
            'user_email' => $ticket->user?->email,
            'created_at' => optional($ticket->created_at)?->toIso8601String(),
        ])->all();

        return Response::text($this->toJson([
            'count' => count($tickets),
            'tickets' => $tickets,
        ]));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Search subject, mask, message, or user email/name.'),
            'status' => $schema->string()->description('open|in_progress|on_hold|responded|closed'),
            'product' => $schema->string()->description('Product filter, e.g. mobile, desktop, bifrost.'),
            'limit' => $schema->integer()->description('Max results (default 25, max 100).'),
        ];
    }
}
