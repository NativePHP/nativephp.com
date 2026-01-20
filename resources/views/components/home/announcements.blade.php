{{-- Announcements Section: Plugins (50%) + Mimi & Jump (50% stacked) --}}
<section class="mt-4" aria-label="New announcements">
    <div class="grid gap-4 lg:grid-cols-2">
        {{-- Plugins Announcement (Left - 50%) --}}
        <x-home.plugins-announcement />

        {{-- Right Column - Mimi & Jump stacked --}}
        <div class="flex flex-col gap-4">
            {{-- Mimi Card --}}
            <div class="flex-1">
                <x-home.mimi-card />
            </div>

            {{-- Jump Card --}}
            <div class="flex-1">
                <x-home.jump-card />
            </div>
        </div>
    </div>
</section>