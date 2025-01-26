@php
    $isMobile = request()->is('docs/mobile-v1*');
    $href= $isMobile ? '/docs/1' : '/docs/mobile-v1';
@endphp

    <div {{ $attributes->class(['mb-6 p-3 text-sm space-y-2.5 rounded-md
            text-gray-600 bg-gray-50/85 border border-gray-200
            dark:text-gray-300 dark:bg-gray-800 dark:border-none
            ']) }}>
        <div>You're reading the documentation of NativePHP for {{ $isMobile? 'mobile' : 'desktop' }}.</div>
        <x-link-subtle class="flex w-full gap-2 items-center" href="{{ $href }}">
            @if($isMobile)
                <x-icons.computer-desktop class="size-4"/>
                Switch to desktop
            @else
                <x-icons.device-mobile-phone class="size-4"/>
                Switch to mobile
            @endif

        </x-link-subtle>
    </div>
