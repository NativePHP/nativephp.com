<x-layouts.auth title="Reset Password">
    <flux:heading size="xl" class="text-center">Set new password</flux:heading>

    <form action="{{ route('password.update') }}" method="POST" class="mt-8 space-y-6">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="space-y-4">
            <flux:field>
                <flux:label>Email address</flux:label>
                <flux:input name="email" type="email" autocomplete="email" required value="{{ old('email', request()->email) }}" placeholder="Enter your email address" :invalid="$errors->has('email')" />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>New Password</flux:label>
                <flux:input name="password" type="password" autocomplete="new-password" required viewable placeholder="Enter your new password" :invalid="$errors->has('password')" />
                <flux:error name="password" />
            </flux:field>

            <flux:field>
                <flux:label>Confirm New Password</flux:label>
                <flux:input name="password_confirmation" type="password" autocomplete="new-password" required viewable placeholder="Confirm your new password" />
            </flux:field>
        </div>

        <flux:button type="submit" variant="primary" class="w-full">Reset Password</flux:button>
    </form>
</x-layouts.auth>
