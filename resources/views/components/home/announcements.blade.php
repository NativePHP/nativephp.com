{{-- Announcements Section: Plugins (full width) + Bifrost (50%) & Mimi/Jump (50% stacked) --}}
<section class="mt-4" aria-label="New announcements">
    <div class="flex flex-col gap-4">
        {{-- Plugins Announcement (Full Width) --}}
        <x-home.plugins-announcement />

        {{-- Mimi & Jump + Bifrost Row --}}
        <div class="grid gap-4 xl:grid-cols-2">
            {{-- Left Column - Mimi & Jump --}}
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-1">
                {{-- Mimi Card --}}
                <x-home.mimi-card />

                {{-- Jump Card --}}
                <x-home.jump-card />
            </div>

            {{-- Bifrost Banner (Right - 50%) --}}
            <x-home.bifrost-card />
        </div>
    </div>
</section>