<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\DocsScreenshotCrop;
use InvalidArgumentException;

/**
 * The docs-reference screens captured by `docs:capture-screenshots`, one
 * entry per screen in the `NativePHP/edge-components-demo` app. Each key is
 * served there at `/edge-components/{key}` from
 * `resources/views/native/edge-components/{key}.blade.php`, and the demo's
 * test suite keeps its list of keys in step with this one — adding a screen
 * there means adding its entry here too.
 *
 * `manual_step` is an instruction for anything that has to be done by hand
 * before the capture (EDGE can't open a side drawer or a menu from PHP), or
 * null when the screen is ready as soon as it launches.
 *
 * @phpstan-type Screen array{route: string, ios: string, android: string, manual_step: ?string, crop: DocsScreenshotCrop}
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
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Top,
        ],
        'top-bar-large-title' => [
            'route' => '/edge-components/top-bar-large-title',
            'ios' => 'edge-top-bar-large-title-ios.png',
            'android' => 'edge-top-bar-large-title-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Top,
        ],
        'top-bar-search' => [
            'route' => '/edge-components/top-bar-search',
            'ios' => 'edge-top-bar-search-ios.png',
            'android' => 'edge-top-bar-search-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Top,
        ],
        'top-bar-destructive-action' => [
            'route' => '/edge-components/top-bar-destructive-action',
            'ios' => 'edge-top-bar-destructive-action-ios.png',
            'android' => 'edge-top-bar-destructive-action-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Top,
        ],
        'top-bar-logo' => [
            'route' => '/edge-components/top-bar-logo',
            'ios' => 'edge-top-bar-logo-ios.png',
            'android' => 'edge-top-bar-logo-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Top,
        ],
        'bottom-nav' => [
            'route' => '/edge-components/bottom-nav',
            'ios' => 'edge-bottom-nav-ios.png',
            'android' => 'edge-bottom-nav-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Bottom,
        ],
        'bottom-nav-search-item' => [
            'route' => '/edge-components/bottom-nav-search-item',
            'ios' => 'edge-bottom-nav-search-item-ios.png',
            'android' => 'edge-bottom-nav-search-item-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Bottom,
        ],
        'side-nav' => [
            'route' => '/edge-components/side-nav',
            'ios' => 'edge-side-nav-ios.png',
            'android' => 'edge-side-nav-android.png',
            'manual_step' => 'Tap the ☰ button to open the side drawer',
            'crop' => DocsScreenshotCrop::Full,
        ],
        'side-nav-header-image' => [
            'route' => '/edge-components/side-nav-header-image',
            'ios' => 'edge-side-nav-header-image-ios.png',
            'android' => 'edge-side-nav-header-image-android.png',
            'manual_step' => 'Tap the ☰ button to open the side drawer',
            'crop' => DocsScreenshotCrop::Full,
        ],
        'bottom-sheet' => [
            'route' => '/edge-components/bottom-sheet',
            'ios' => 'edge-bottom-sheet-ios.png',
            'android' => 'edge-bottom-sheet-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'modal' => [
            'route' => '/edge-components/modal',
            'ios' => 'edge-modal-ios.png',
            'android' => 'edge-modal-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'menus' => [
            'route' => '/edge-components/menus',
            'ios' => 'edge-menus-ios.png',
            'android' => 'edge-menus-android.png',
            'manual_step' => 'Tap "Export" to open its menu',
            'crop' => DocsScreenshotCrop::Full,
        ],
        'list' => [
            'route' => '/edge-components/list',
            'ios' => 'edge-list-ios.png',
            'android' => 'edge-list-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'refreshable' => [
            'route' => '/edge-components/refreshable',
            'ios' => 'edge-refreshable-ios.png',
            'android' => 'edge-refreshable-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'virtual-list' => [
            'route' => '/edge-components/virtual-list',
            'ios' => 'edge-virtual-list-ios.png',
            'android' => 'edge-virtual-list-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'slider' => [
            'route' => '/edge-components/slider',
            'ios' => 'edge-slider-ios.png',
            'android' => 'edge-slider-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'toggle' => [
            'route' => '/edge-components/toggle',
            'ios' => 'edge-toggle-ios.png',
            'android' => 'edge-toggle-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'checkbox' => [
            'route' => '/edge-components/checkbox',
            'ios' => 'edge-checkbox-ios.png',
            'android' => 'edge-checkbox-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'select' => [
            'route' => '/edge-components/select',
            'ios' => 'edge-select-ios.png',
            'android' => 'edge-select-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'radio-group' => [
            'route' => '/edge-components/radio-group',
            'ios' => 'edge-radio-group-ios.png',
            'android' => 'edge-radio-group-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'web-view' => [
            'route' => '/edge-components/web-view',
            'ios' => 'edge-web-view-ios.png',
            'android' => 'edge-web-view-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'gesture-area' => [
            'route' => '/edge-components/gesture-area',
            'ios' => 'edge-gesture-area-ios.png',
            'android' => 'edge-gesture-area-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'tab-row' => [
            'route' => '/edge-components/tab-row',
            'ios' => 'edge-tab-row-ios.png',
            'android' => 'edge-tab-row-android.png',
            'manual_step' => null,
            'crop' => DocsScreenshotCrop::Full,
        ],
        'text' => [
            'route' => '/edge-components/text',
            'ios' => 'edge-text-ios.png',
            'android' => 'edge-text-android.png',
            'manual_step' => null,
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
