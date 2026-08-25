@props(['action' => null, 'size' => 'flexible'])

@if (config('services.turnstile.site_key'))
    @once
        @push('head')
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endpush
    @endonce

    <div>
        <div
            {{ $attributes->class('cf-turnstile') }}
            data-sitekey="{{ config('services.turnstile.site_key') }}"
            @if ($action) data-action="{{ $action }}" @endif
            data-theme="auto"
            data-size="{{ $size }}"
        ></div>

        <flux:error name="cf-turnstile-response" />
    </div>
@endif
