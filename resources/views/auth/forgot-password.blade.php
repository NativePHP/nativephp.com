<x-layouts.auth title="Reset Password">
    <flux:heading size="xl" class="text-center">Reset your password</flux:heading>
    <flux:text class="mt-2 text-center">
        Enter your email address and we'll send you a link to reset your password.
    </flux:text>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle" class="mt-6">
            <flux:callout.text>{{ session('status') }}</flux:callout.text>
        </flux:callout>
    @endif

    <form action="{{ route('password.email') }}" method="POST" class="mt-8 space-y-6">
        @csrf

        <flux:field>
            <flux:label>Email address</flux:label>
            <flux:input name="email" type="email" autocomplete="email" required value="{{ old('email') }}" placeholder="Enter your email address" :invalid="$errors->has('email')" />
            <flux:error name="email" />
        </flux:field>

        <flux:button type="submit" variant="primary" class="w-full">Send Password Reset Link</flux:button>

        <div class="text-center">
            <flux:link href="{{ route('customer.login') }}">Back to login</flux:link>
        </div>
    </form>
</x-layouts.auth>
