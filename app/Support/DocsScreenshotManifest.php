<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\DocsScreenshotCrop;
use InvalidArgumentException;

/**
 * The docs-reference screens captured by `docs:capture-screenshots`, one
 * entry per screen in `NativePHP/super-native`'s "Edge Component showcase"
 * route group. Adding a new prop-variant screen there means adding its
 * entry here too.
 *
 * @phpstan-type Screen array{route: string, ios: string, android: string, requires_drawer_open: bool, crop: DocsScreenshotCrop}
 */
final class DocsScreenshotManifest
{
    /**
     * @var array<string, Screen>
     */
    private const SCREENS = [
        'top-bar' => [
            'route' => '/edge-components/top-bar',
            'ios' => 'edge-top-bar-ios.png',
            'android' => 'edge-top-bar-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Top,
        ],
        'top-bar-large-title' => [
            'route' => '/edge-components/top-bar-large-title',
            'ios' => 'edge-top-bar-large-title-ios.png',
            'android' => 'edge-top-bar-large-title-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Top,
        ],
        'top-bar-search' => [
            'route' => '/edge-components/top-bar-search',
            'ios' => 'edge-top-bar-search-ios.png',
            'android' => 'edge-top-bar-search-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Top,
        ],
        'top-bar-destructive-action' => [
            'route' => '/edge-components/top-bar-destructive-action',
            'ios' => 'edge-top-bar-destructive-action-ios.png',
            'android' => 'edge-top-bar-destructive-action-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Top,
        ],
        'top-bar-logo' => [
            'route' => '/edge-components/top-bar-logo',
            'ios' => 'edge-top-bar-logo-ios.png',
            'android' => 'edge-top-bar-logo-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Top,
        ],
        'bottom-nav' => [
            'route' => '/edge-components/bottom-nav',
            'ios' => 'edge-bottom-nav-ios.png',
            'android' => 'edge-bottom-nav-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Bottom,
        ],
        'bottom-nav-search-item' => [
            'route' => '/edge-components/bottom-nav-search-item',
            'ios' => 'edge-bottom-nav-search-item-ios.png',
            'android' => 'edge-bottom-nav-search-item-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Bottom,
        ],
        'side-nav' => [
            'route' => '/edge-components/side-nav',
            'ios' => 'edge-side-nav-ios.png',
            'android' => 'edge-side-nav-android.png',
            'requires_drawer_open' => true,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'side-nav-header-image' => [
            'route' => '/edge-components/side-nav-header-image',
            'ios' => 'edge-side-nav-header-image-ios.png',
            'android' => 'edge-side-nav-header-image-android.png',
            'requires_drawer_open' => true,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'bottom-sheet' => [
            'route' => '/explore/sheets',
            'ios' => 'edge-bottom-sheet-ios.png',
            'android' => 'edge-bottom-sheet-android.png',
            'requires_drawer_open' => true,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'modal' => [
            'route' => '/explore/sheets',
            'ios' => 'edge-modal-ios.png',
            'android' => 'edge-modal-android.png',
            'requires_drawer_open' => true,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'menus' => [
            'route' => '/explore/menus',
            'ios' => 'edge-menus-ios.png',
            'android' => 'edge-menus-android.png',
            'requires_drawer_open' => true,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'list' => [
            'route' => '/mail-demo',
            'ios' => 'edge-list-ios.png',
            'android' => 'edge-list-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'refreshable' => [
            'route' => '/refreshable-demo',
            'ios' => 'edge-refreshable-ios.png',
            'android' => 'edge-refreshable-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'virtual-list' => [
            'route' => '/explore/icons',
            'ios' => 'edge-virtual-list-ios.png',
            'android' => 'edge-virtual-list-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'slider' => [
            'route' => '/explore/forms',
            'ios' => 'edge-slider-ios.png',
            'android' => 'edge-slider-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'toggle' => [
            'route' => '/explore/forms',
            'ios' => 'edge-toggle-ios.png',
            'android' => 'edge-toggle-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'checkbox' => [
            'route' => '/explore/forms',
            'ios' => 'edge-checkbox-ios.png',
            'android' => 'edge-checkbox-android.png',
            'requires_drawer_open' => true,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'select' => [
            'route' => '/explore/forms',
            'ios' => 'edge-select-ios.png',
            'android' => 'edge-select-android.png',
            'requires_drawer_open' => true,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'radio-group' => [
            'route' => '/explore/forms',
            'ios' => 'edge-radio-group-ios.png',
            'android' => 'edge-radio-group-android.png',
            'requires_drawer_open' => true,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'web-view' => [
            'route' => '/webview-demo',
            'ios' => 'edge-web-view-ios.png',
            'android' => 'edge-web-view-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'gesture-area' => [
            'route' => '/animate',
            'ios' => 'edge-gesture-area-ios.png',
            'android' => 'edge-gesture-area-android.png',
            'requires_drawer_open' => true,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'tab-row' => [
            'route' => '/twitter/profile/0',
            'ios' => 'edge-tab-row-ios.png',
            'android' => 'edge-tab-row-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'text' => [
            'route' => '/explore/typography',
            'ios' => 'edge-text-ios.png',
            'android' => 'edge-text-android.png',
            'requires_drawer_open' => false,
            'crop' => DocsScreenshotCrop::Full,
        ],
    ];

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(self::SCREENS);
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, self::SCREENS);
    }

    /**
     * @return Screen
     */
    public static function get(string $key): array
    {
        if (! self::has($key)) {
            throw new InvalidArgumentException("Unknown docs screenshot screen: {$key}");
        }

        return self::SCREENS[$key];
    }
}
