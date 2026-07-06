<?php

namespace App\Http\Controllers;

class ApplinksController extends Controller
{
    /**
     * Jump's iOS app identity: <TeamID>.<BundleID>.
     */
    private const IOS_APP_ID = 'J68WFCX458.com.bifrosttech.jump';

    /**
     * Jump's Android package + the SHA-256 of its Play app-signing cert.
     * These are public identifiers, not secrets.
     */
    private const ANDROID_PACKAGE = 'com.bifrosttech.jump';

    private const ANDROID_SHA256 = 'D8:31:4E:55:E5:FF:06:17:D8:49:EA:3B:1F:BF:6C:58:B3:8D:AD:2C:30:CA:13:D2:CA:42:B0:85:B4:7D:CB:38';

    /**
     * URL path prefixes that open in Jump.
     *
     * Only add a prefix here once Jump has a screen that can render it —
     * an unhandled deep link drops the user on a broken state. Each entry
     * added here needs a matching <data> intent-filter in Jump's Android
     * manifest (iOS scopes via this file; Android scopes via the manifest).
     */
    private const LINK_PATHS = [
        '/docs/*' => 'Open documentation pages in Jump',
        // '/blog/*' => 'Open blog posts in Jump',   // add when Jump can render them
    ];

    /**
     * Android App Links verification (served at /.well-known/assetlinks.json).
     *
     * Path scoping is declared in Jump's manifest intent-filter, not here —
     * this file only asserts that the app may handle links for this domain.
     */
    public function assetLinks()
    {
        return response()->json([
            [
                'relation' => [
                    'delegate_permission/common.handle_all_urls',
                    'delegate_permission/common.get_login_creds',
                ],
                'target' => [
                    'namespace' => 'android_app',
                    'package_name' => self::ANDROID_PACKAGE,
                    'sha256_cert_fingerprints' => [
                        self::ANDROID_SHA256,
                    ],
                ],
            ],
        ]);
    }

    /**
     * iOS Universal Links (served at /.well-known/apple-app-site-association).
     *
     * Claims only the LINK_PATHS prefixes; the rest of nativephp.com stays in
     * the browser. webcredentials is domain-wide for password autofill.
     */
    public function appSiteAssociation()
    {
        $components = [];

        foreach (self::LINK_PATHS as $pattern => $comment) {
            $components[] = [
                '/' => $pattern,
                'comment' => $comment,
            ];
        }

        return response()->json([
            'applinks' => [
                'details' => [
                    [
                        'appIDs' => [self::IOS_APP_ID],
                        'components' => $components,
                    ],
                ],
            ],
            'webcredentials' => [
                'apps' => [self::IOS_APP_ID],
            ],
        ]);
    }
}
