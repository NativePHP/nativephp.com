{{-- Never place inside a heading — HeadingRenderer slugs the heading's
     rendered contents into the anchor id, so injected markup would change
     existing deep links. --}}

@props([
    'since' => null,
    'changed' => null,
    'deprecated' => null,
    'removed' => null,
    'package' => null,
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

    // A package on its own release line (config('docs.packages')) is named in
    // its labels. Its baseline was current when the platform major shipped, so
    // labels at or below it never render, just like x.0. Neither does a
    // package missing from the config.
    $release = filled($package) ? (config('docs.packages')[$package] ?? null) : null;
@endphp

@if ($release && $state && version_compare($state['version'], $release['baseline']) > 0)
    <x-docs.badge
        :label="$state['prefix'].$package.' '.$state['version']"
        :variant="$state['variant']"
        :tooltip="$state['verb'].' '.$release['name'].' '.$state['version']"
        :href="\App\Support\DocsLabels::versioningPolicyUrl($package.'-labels')"
    />
@elseif (blank($package) && $state && $minor > 0)
    <x-docs.badge
        :label="$state['prefix'].$state['version']"
        :variant="$state['variant']"
        :tooltip="$state['verb'].' '.\App\Support\DocsLabels::productName().' '.$state['version']"
        :href="\App\Support\DocsLabels::versioningPolicyUrl()"
    />
@endif
