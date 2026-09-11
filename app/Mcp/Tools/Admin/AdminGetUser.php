<?php

namespace App\Mcp\Tools\Admin;

use App\Mcp\Tools\Concerns\RequiresAdmin;
use App\Models\License;
use App\Models\PluginLicense;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('admin-get-user')]
#[Description('User detail useful for support: identity, signup time, license counts, open support tickets. Never returns password hashes, license keys, or GitHub tokens.')]
#[IsReadOnly]
class AdminGetUser extends Tool
{
    use RequiresAdmin;

    public function handle(Request $request): Response
    {
        if ($denied = $this->ensureAdmin($request)) {
            return $denied;
        }

        $validated = $request->validate([
            'id' => ['nullable', 'integer', 'min:1'],
            'email' => ['nullable', 'email'],
        ]);

        if (empty($validated['id']) && empty($validated['email'])) {
            return Response::error('Provide id or email.');
        }

        $user = User::query()
            ->when(! empty($validated['id']), fn ($q) => $q->where('id', $validated['id']))
            ->when(! empty($validated['email']), fn ($q) => $q->where('email', $validated['email']))
            ->first();

        if (! $user) {
            return Response::error('User not found.');
        }

        $licenses = License::query()
            ->where('user_id', $user->id)
            ->get(['id', 'policy_name', 'expires_at', 'created_at', 'is_suspended', 'source', 'name'])
            ->map(fn (License $license): array => [
                'id' => $license->id,
                'policy_name' => $license->policy_name,
                'name' => $license->name,
                'source' => $license->source?->value ?? $license->source,
                'is_suspended' => (bool) ($license->is_suspended ?? false),
                'expires_at' => optional($license->expires_at)?->toIso8601String(),
                'created_at' => optional($license->created_at)?->toIso8601String(),
            ])
            ->all();

        $pluginLicenses = PluginLicense::query()
            ->with('plugin:id,name,status')
            ->where('user_id', $user->id)
            ->latest('purchased_at')
            ->limit(50)
            ->get()
            ->map(fn (PluginLicense $license): array => [
                'id' => $license->id,
                'plugin' => $license->plugin?->name,
                'plugin_status' => $license->plugin?->status?->value,
                'price_paid' => $license->price_paid,
                'currency' => $license->currency,
                'purchased_at' => optional($license->purchased_at)?->toIso8601String(),
                'expires_at' => optional($license->expires_at)?->toIso8601String(),
            ])
            ->all();

        $tickets = SupportTicket::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->limit(20)
            ->get(['id', 'mask', 'subject', 'status', 'product', 'created_at'])
            ->map(fn (SupportTicket $ticket): array => [
                'id' => $ticket->id,
                'mask' => $ticket->mask,
                'subject' => $ticket->subject,
                'status' => $ticket->status?->value ?? $ticket->status,
                'product' => $ticket->product,
                'created_at' => optional($ticket->created_at)?->toIso8601String(),
            ])
            ->all();

        return Response::text($this->toJson([
            'id' => $user->id,
            'name' => $user->name,
            'display_name' => $user->display_name,
            'email' => $user->email,
            'email_verified_at' => optional($user->email_verified_at)?->toIso8601String(),
            'created_at' => optional($user->created_at)?->toIso8601String(),
            'is_admin' => $user->isAdmin(),
            'github_username' => $user->github_username ?? null,
            'discord_username' => $user->discord_username ?? null,
            'licenses' => $licenses,
            'plugin_licenses' => $pluginLicenses,
            'support_tickets' => $tickets,
        ]));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('User id.'),
            'email' => $schema->string()->description('Exact email address.'),
        ];
    }
}
