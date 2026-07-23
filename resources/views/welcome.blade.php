<x-layout title="Baking Delicious Native Apps">
    {{-- Hero --}}
    <x-home.hero />

    {{-- Testimonials --}}
    <x-home.testimonials />

    {{-- Explainer --}}
    <x-home.explainer />

    {{--
        Announcements: Plugins, Masterclass, Jump, Bifrost. All mobile-
        specific, so they drop out on the Desktop track. No x-cloak: mobile is
        the default, so the first paint is already correct for most visitors.
    --}}
    <div
        x-show="$store.platform.is('mobile')"
        data-platform-section="announcements"
    >
        <x-home.announcements />
    </div>

    {{-- Partners --}}
    <x-home.partners />

    {{-- Sponsors --}}
    <x-home.sponsors />

    {{-- Feedback --}}
    <x-home.feedback />
</x-layout>
