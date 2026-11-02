<x-layouts.auth title="Connect NativePHP Admin">
    <div class="space-y-4">
        <flux:heading size="xl">Connect {{ $client->name }}?</flux:heading>

        <flux:text>
            Signed in as {{ $user->email }}. This connection grants <strong>NativePHP admin MCP access</strong>
            for site support (create unpublished blog posts, look up signups/users/companies, review plugins and
            support tickets). It never returns license keys, Stripe secrets, or passwords.
            Only site admins may approve this connection (<code>mcp:admin</code>).
        </flux:text>

        <div class="flex flex-col gap-3">
            <form method="POST" action="{{ route('passport.authorizations.approve') }}">
                @csrf
                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                <flux:button type="submit" variant="primary" class="w-full">
                    Connect admin assistant
                </flux:button>
            </form>

            <form method="POST" action="{{ route('passport.authorizations.deny') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                <flux:button type="submit" class="w-full">Cancel</flux:button>
            </form>
        </div>
    </div>
</x-layouts.auth>
