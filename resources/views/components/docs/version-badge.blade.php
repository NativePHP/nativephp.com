{{-- Never place inside a heading — HeadingRenderer slugs the heading's
     rendered contents into the anchor id, so injected markup would change
     existing deep links. --}}

@props([
    'since' => null,
    'changed' => null,
    'deprecated' => null,
    'removed' => null,
])

@php
    $states = [
        ['version' => $since, 'variant' => 'neutral', 'prefix' => '', 'verb' => 'Added in'],
        ['version' => $changed, 'variant' => 'info', 'prefix' => 'Changed ', 'verb' => 'Changed in'],
        ['version' => $deprecated, 'variant' => 'warning', 'prefix' => 'Deprecated ', 'verb' => 'Deprecated in'],
        ['version' => $removed, 'variant' => 'danger', 'prefix' => 'Removed ', 'verb' => 'Removed in'],
    ];

    $state = collect($states)->firstWhere(fn (array $state) => filled($state['version']));

    // x.0 never renders — everything in a major's tree was there at x.0
    // unless stated otherwise.
    $minor = (int) (explode('.', (string) ($state['version'] ?? ''))[1] ?? 0);
@endphp

@if ($state && $minor > 0)
    <x-docs.badge
        :label="$state['prefix'].$state['version']"
        :variant="$state['variant']"
        :tooltip="$state['verb'].' '.\App\Support\DocsLabels::productName().' '.$state['version']"
        :href="\App\Support\DocsLabels::versioningPolicyUrl()"
    />
@endif
