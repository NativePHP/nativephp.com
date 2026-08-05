{{-- Announcements Section: Jump/Course (50% stacked) & Bifrost (50%) + Plugins (full width) --}}
<section class="mt-4" aria-label="New announcements">
    <div class="flex flex-col gap-4">
        {{-- Jump & Course + Bifrost Row --}}
        <div class="grid gap-4 xl:grid-cols-2">
            {{-- Left Column - Jump & Course --}}
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-1">
                {{-- Jump Card --}}
                <x-home.jump-card />

                {{-- Course Card --}}
                <x-home.course-card />
            </div>

            {{-- Bifrost Banner (Right - 50%) --}}
            <x-home.bifrost-card />
        </div>

        {{-- Plugins Announcement (Full Width) --}}
        <x-home.plugins-announcement />
    </div>
</section>