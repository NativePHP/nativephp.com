@props([
    'since' => null,
    'unavailable' => false,
])

@php
    $requirement = $unavailable ? false : $since;
@endphp

@unless (\App\Support\JumpApp::supports($requirement))
    <x-docs.badge
        variant="jump"
        :label="$requirement === false ? 'Not in Jump yet' : 'Jump '.$requirement.'+'"
        :tooltip="$requirement === false
            ? 'Not available in the Jump preview app yet — build to a device or simulator to try it'
            : 'Needs Jump '.$requirement.' or later; Jump currently ships '.\App\Support\JumpApp::currentVersion()"
        :href="\App\Support\DocsLabels::jumpUrl()"
    />
@endunless
