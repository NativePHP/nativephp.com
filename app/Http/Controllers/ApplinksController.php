<?php

namespace App\Http\Controllers;

class ApplinksController extends Controller
{
    public function assetLinks()
    {
        $array = [
            [
                'relation' => [
                    'delegate_permission/common.handle_all_urls',
                ],
                'target' => [
                    'namespace' => 'android_app',
                    'package_name' => config('nativephp.app_id'),
                    'sha256_cert_fingerprints' => [
                        config('services.certFingerprint'),
                    ],
                ],
            ],
        ];

        return response('[
  {
    "relation": [
      "delegate_permission/common.handle_all_urls"
    ],
    "target": {
      "namespace": "android_app",
      "package_name": "com.nativephp.kitchensinkapp",
      "sha256_cert_fingerprints": [
        "D3:C4:F7:5E:B2:3C:95:90:89:BE:CB:47:0B:B2:9F:40:A5:22:6B:03:A3:C9:1D:B2:8B:B6:1F:06:87:C8:86:AA"
      ]
    }
  }
]', headers: ['Content-Type' => 'application/json']);
    }

    public function appSiteAssociation()
    {
        return response()->json([
            'applinks' => [
                'details' => [
                    [
                        'appIDs' => [config('services.apple.app_id')],
                        'paths' => ['*'],
                    ],
                ],
            ],
            'webcredentials' => [
                'apps' => [config('services.apple.webcredentials')],
            ],
        ]);
    }
}
