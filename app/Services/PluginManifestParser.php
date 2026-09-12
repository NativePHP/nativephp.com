<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AndroidPermission;

final class PluginManifestParser
{
    public function sdkConstraint(?array $composerData): ?string
    {
        return $composerData['require']['nativephp/mobile'] ?? null;
    }

    public function iosMinVersion(?array $nativephpData): ?string
    {
        return $nativephpData['ios']['min_version'] ?? null;
    }

    public function androidMinVersion(?array $nativephpData): ?string
    {
        $minVersion = $nativephpData['android']['min_version'] ?? null;

        return $minVersion === null ? null : (string) $minVersion;
    }

    /**
     * @return array{android_permissions: array<int, string>, android_features: array<int, array<string, mixed>>, ios_capabilities: array<int, string>, ios_entitlements: array<string, mixed>, ios_background_modes: array<int, string>, ios_info_plist: array<string, mixed>}
     */
    public function permissionManifest(?array $nativephpData): array
    {
        return [
            'android_permissions' => $nativephpData['android']['permissions'] ?? [],
            'android_features' => $nativephpData['android']['features'] ?? [],
            'ios_capabilities' => $nativephpData['ios']['capabilities'] ?? [],
            'ios_entitlements' => $nativephpData['ios']['entitlements'] ?? [],
            'ios_background_modes' => $nativephpData['ios']['background_modes'] ?? [],
            'ios_info_plist' => $nativephpData['ios']['info_plist'] ?? [],
        ];
    }

    /**
     * @param  array{android_permissions?: array<int, string>, ios_info_plist?: array<string, mixed>}  $permissionManifest
     * @return array<int, array{permission: string, expected_key: string}>
     */
    public function missingUsageDescriptions(array $permissionManifest, bool $supportsIos): array
    {
        if (! $supportsIos) {
            return [];
        }

        $declaredKeys = array_keys($permissionManifest['ios_info_plist'] ?? []);
        $missing = [];

        foreach ($permissionManifest['android_permissions'] ?? [] as $permission) {
            $expectedKey = AndroidPermission::tryFrom($permission)?->iosUsageDescriptionKey();

            if ($expectedKey !== null && ! in_array($expectedKey, $declaredKeys, true)) {
                $missing[] = ['permission' => $permission, 'expected_key' => $expectedKey];
            }
        }

        return $missing;
    }

    /**
     * @param  array{android_permissions?: array<int, string>, ios_capabilities?: array<int, string>, ios_background_modes?: array<int, string>, ios_entitlements?: array<string, mixed>}|null  $previous
     * @param  array{android_permissions?: array<int, string>, ios_capabilities?: array<int, string>, ios_background_modes?: array<int, string>, ios_entitlements?: array<string, mixed>}  $current
     */
    public function hasExpandedPermissions(?array $previous, array $current): bool
    {
        if ($previous === null) {
            return false;
        }

        foreach (['android_permissions', 'ios_capabilities', 'ios_background_modes'] as $listKey) {
            if (array_diff($current[$listKey] ?? [], $previous[$listKey] ?? []) !== []) {
                return true;
            }
        }

        return array_diff(
            array_keys($current['ios_entitlements'] ?? []),
            array_keys($previous['ios_entitlements'] ?? [])
        ) !== [];
    }
}
