<?php

namespace App\Support;

use App\Http\Controllers\ApplinksController;

/**
 * Public identity of Bifrost's Jump preview app.
 *
 * Single source of truth for the app's store URLs and platform identifiers,
 * shared by the deep-link association files ({@see ApplinksController})
 * and the docs "open in Jump" UI. These are public identifiers, not secrets.
 */
final class JumpApp
{
    /**
     * Jump's iOS app identity: <TeamID>.<BundleID>.
     */
    public const IOS_APP_ID = 'J68WFCX458.com.bifrosttech.jump';

    public const IOS_APP_STORE_URL = 'https://apps.apple.com/us/app/bifrost-jump/id6757173334';

    public const ANDROID_PACKAGE = 'com.bifrosttech.jump';

    /**
     * SHA-256 of Jump's Play app-signing cert (public, not a secret).
     */
    public const ANDROID_SHA256 = 'D8:31:4E:55:E5:FF:06:17:D8:49:EA:3B:1F:BF:6C:58:B3:8D:AD:2C:30:CA:13:D2:CA:42:B0:85:B4:7D:CB:38';

    public const ANDROID_PLAY_STORE_URL = 'https://play.google.com/store/apps/details?id=com.bifrosttech.jump';

    /**
     * The only domain whose deep links open in Jump.
     */
    public const CANONICAL_DOMAIN = 'https://nativephp.com';

    /**
     * Marks a visit as originating from a scanned QR code.
     */
    public const QR_PARAM = 'jump=qr';

    /**
     * Build the scannable deep link for a docs page.
     *
     * Deliberately pinned to the canonical domain rather than the current host:
     * Jump only claims links for nativephp.com (see {@see ApplinksController}),
     * and a phone can't resolve a local or preview hostname anyway — so a QR
     * built from url() would be unscannable everywhere except production.
     */
    public static function docsDeepLink(string $path): string
    {
        return self::CANONICAL_DOMAIN.'/'.ltrim($path, '/').'?'.self::QR_PARAM;
    }
}
