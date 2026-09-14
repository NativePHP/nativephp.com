@php
    $manifest = $manifest ?? [];
@endphp

<div class="space-y-4">
    @foreach ([
        'android_permissions' => 'Android Permissions',
        'android_features' => 'Android Features',
        'ios_capabilities' => 'iOS Capabilities',
        'ios_background_modes' => 'iOS Background Modes',
    ] as $key => $label)
        @if (! empty($manifest[$key]))
            <div>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $label }}</p>
                <ul class="mt-1 list-inside list-disc text-sm text-gray-600 dark:text-gray-400">
                    @foreach ($manifest[$key] as $item)
                        <li>{{ is_array($item) ? ($item['name'] ?? json_encode($item)) : $item }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endforeach

    @if (! empty($manifest['ios_entitlements']))
        <div>
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">iOS Entitlements</p>
            <ul class="mt-1 list-inside list-disc text-sm text-gray-600 dark:text-gray-400">
                @foreach ($manifest['ios_entitlements'] as $entitlement => $value)
                    <li>{{ $entitlement }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (empty(array_filter($manifest)))
        <p class="text-sm text-gray-500 dark:text-gray-400">This version declares no native permissions, entitlements, or background modes.</p>
    @endif
</div>
