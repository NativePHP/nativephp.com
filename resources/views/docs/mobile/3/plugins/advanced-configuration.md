---
title: Advanced Configuration
order: 750
---

<aside>

#### Let AI Handle the Complexity

Advanced plugin configuration can be tricky to get right. The [NativePHP Plugin Dev Kit](/products/plugin-dev-kit)
generates correct manifest configurations, Gradle dependencies, CocoaPods specs, and platform-specific code — so you
don't have to memorize these docs.

</aside>

## Secrets & Environment Variables

Plugins that require API keys, tokens, or other sensitive configuration can declare required environment variables using
the `secrets` field. NativePHP validates these before building.

```json
{
    "secrets": {
        "MAPBOX_DOWNLOADS_TOKEN": {
            "description": "Mapbox SDK download token from mapbox.com/account/access-tokens",
            "required": true
        },
        "FIREBASE_API_KEY": {
            "description": "Firebase project API key",
            "required": false
        }
    }
}
```

Each secret has:
- **description** — Instructions for obtaining the value
- **required** — Whether the build should fail if missing (default: `true`)

### Using Secrets

Reference secrets anywhere in your manifest using `${ENV_VAR}` syntax:

```json
{
    "android": {
        "repositories": [
            {
                "url": "https://api.mapbox.com/downloads/v2/releases/maven",
                "credentials": {
                    "password": "${MAPBOX_DOWNLOADS_TOKEN}"
                }
            }
        ]
    }
}
```

Placeholders are substituted at build time. If a required secret is missing, the build fails with a helpful message
telling users exactly which variables to set in their `.env` file.

## Android Manifest Components

Plugins can register Android components (Activities, Services, Receivers, Providers) that get merged into the app's
`AndroidManifest.xml`:

```json
{
    "android": {
        "activities": [
            {
                "name": ".MyPluginActivity",
                "theme": "@style/Theme.AppCompat.Light.NoActionBar",
                "exported": false,
                "configChanges": "orientation|screenSize"
            }
        ],
        "services": [
            {
                "name": ".BackgroundSyncService",
                "exported": false,
                "foregroundServiceType": "dataSync"
            }
        ],
        "receivers": [
            {
                "name": ".BootReceiver",
                "exported": true,
                "intent-filters": [
                    {
                        "action": "android.intent.action.BOOT_COMPLETED",
                        "category": "android.intent.category.DEFAULT"
                    }
                ]
            }
        ],
        "providers": [
            {
                "name": ".MyContentProvider",
                "authorities": "${applicationId}.myplugin.provider",
                "exported": false,
                "grantUriPermissions": true
            }
        ]
    }
}
```

### Component Names

Names starting with `.` are relative to your plugin's package. For example, if your plugin uses the package
`com.nativephp.plugins.mlplugin`, then `.MyActivity` becomes `com.nativephp.plugins.mlplugin.MyActivity`.

Use fully qualified names for components outside your plugin's package.

### Activity Attributes

| Attribute | Description |
|-----------|-------------|
| `name` | Component class name (required) |
| `theme` | Activity theme resource |
| `exported` | Whether other apps can start this activity |
| `configChanges` | Configuration changes the activity handles itself |
| `launchMode` | Launch mode (standard, singleTop, singleTask, singleInstance) |
| `screenOrientation` | Orientation lock (portrait, landscape, etc.) |
| `intent-filters` | Array of intent filter configurations |

### Service Attributes

| Attribute | Description |
|-----------|-------------|
| `name` | Component class name (required) |
| `exported` | Whether other apps can bind to this service |
| `permission` | Permission required to access the service |
| `foregroundServiceType` | Type for foreground services (camera, microphone, location, etc.). Supports array format for multiple types. |

## Android Features

Declare hardware or software features your plugin requires using the `features` array. These are added as
`<uses-feature>` elements in `AndroidManifest.xml`:

```json
{
    "android": {
        "features": [
            {"name": "android.hardware.camera", "required": true},
            {"name": "android.hardware.camera.autofocus", "required": false},
            {"name": "android.hardware.bluetooth_le", "required": true}
        ]
    }
}
```

Each feature has:
- **name** — The feature name (e.g., `android.hardware.camera`)
- **required** — Whether the app requires this feature (default: `true`)

Setting `required: false` allows your app to be installed on devices without the feature, but you must check
for availability at runtime.

## Android Meta-Data

Add application-level `<meta-data>` elements for SDK configuration:

```json
{
    "android": {
        "meta_data": [
            {
                "name": "com.google.android.geo.API_KEY",
                "value": "${GOOGLE_MAPS_API_KEY}"
            },
            {
                "name": "com.google.firebase.messaging.default_notification_icon",
                "value": "@drawable/ic_notification"
            }
        ]
    }
}
```

Each entry has:
- **name** — The meta-data key
- **value** — The value (supports `${ENV_VAR}` placeholders)

## Declarative Assets

Copy static files to the native projects using the `assets` field. This is simpler than writing a `copy_assets` hook for
basic file copying:

```json
{
    "assets": {
        "android": {
            "models/detector.tflite": "assets/ml/detector.tflite",
            "config/settings.xml": "res/raw/plugin_settings.xml"
        },
        "ios": {
            "models/detector.mlmodel": "Resources/ml/detector.mlmodel",
            "config/settings.plist": "Resources/plugin_settings.plist"
        }
    }
}
```

The format is `"source": "destination"`:
- **source** — Relative path from your plugin's `resources/` directory
- **destination** — Where to place the file in the native project

### Android Destinations

- `assets/...` — App assets (accessible via `AssetManager`)
- `res/raw/...` — Raw resources (accessible via `R.raw.*`)
- `res/drawable/...` — Drawable resources

### iOS Destinations

- `Resources/...` — Bundle resources

### Placeholder Substitution

Text-based assets (XML, JSON, plist, etc.) support `${ENV_VAR}` placeholders that are replaced with environment
variable values during the build:

```xml
<!-- resources/config/api.xml -->
<config>
    <api-key>${MY_PLUGIN_API_KEY}</api-key>
</config>
```

<aside>

Use [lifecycle hooks](lifecycle-hooks) for complex asset handling like downloading large files, unzipping archives,
or conditional asset placement.

</aside>

## iOS Background Modes

Enable background execution capabilities with the `background_modes` array. These values are added to
`UIBackgroundModes` in `Info.plist`:

```json
{
    "ios": {
        "background_modes": ["audio", "fetch", "processing", "location"]
    }
}
```

Common values:
- `audio` — Audio playback or recording
- `fetch` — Background fetch
- `processing` — Background processing tasks
- `location` — Location updates
- `remote-notification` — Push notification processing
- `bluetooth-central` — Bluetooth LE central mode
- `bluetooth-peripheral` — Bluetooth LE peripheral mode

<aside>

Background modes require corresponding entitlements and App Store review. Only request modes your plugin actually needs.

</aside>

## iOS Entitlements

Configure app entitlements for capabilities like Maps, App Groups, HealthKit, or iCloud:

```json
{
    "ios": {
        "entitlements": {
            "com.apple.developer.maps": true,
            "com.apple.security.application-groups": ["group.com.example.shared"],
            "com.apple.developer.associated-domains": ["applinks:example.com"],
            "com.apple.developer.healthkit": true
        }
    }
}
```

Values can be:
- **Boolean** — `true`/`false` for simple capabilities
- **Array** — For capabilities requiring multiple values (App Groups, Associated Domains)
- **String** — For single-value entitlements

Entitlements are written to `NativePHP.entitlements`. If the file doesn't exist, it's created automatically.

<aside>

Many entitlements require corresponding capabilities enabled in your Apple Developer account and Xcode project settings.

</aside>

## iOS Capabilities

Declare iOS capabilities your plugin requires. These are separate from entitlements and are used for Xcode project
configuration:

```json
{
    "ios": {
        "capabilities": ["push-notifications", "background-modes", "healthkit"]
    }
}
```

## Minimum Platform Versions

Specify minimum platform versions your plugin requires:

```json
{
    "android": {
        "min_version": 33
    },
    "ios": {
        "min_version": "18.0"
    }
}
```

- **Android** — Minimum SDK version (integer, e.g., `33` for Android 13)
- **iOS** — Minimum iOS version (string, e.g., `"18.0"`)

NativePHP currently requires a minimum of Android SDK 33 and iOS 18. Your plugin's minimum versions cannot be lower
than these. Use this field when your plugin requires a higher version than NativePHP's baseline.

If a user's app targets a lower version than your plugin requires, they'll receive a warning during plugin validation.

## Initialization Functions

Plugins can specify native functions to call during app initialization. This is useful for SDKs that require early
setup before any bridge functions are called:

```json
{
    "android": {
        "init_function": "com.myvendor.plugins.myplugin.MyPluginInit.initialize"
    },
    "ios": {
        "init_function": "MyPluginInit.initialize"
    }
}
```

The init function is called once when the app starts, before any bridge functions are available. Use this for:
- SDK initialization that must happen early
- Setting up global state or singletons
- Registering observers or listeners

<aside>

Init functions run synchronously during app startup. Keep them fast to avoid slowing down app launch.

</aside>

<aside>

#### Skip the Guesswork

Building a plugin like this from scratch means learning Gradle, CocoaPods, Swift Package Manager, and two unfamiliar
languages. The [NativePHP Plugin Dev Kit](/products/plugin-dev-kit) handles all of it — describe what you want
and get a working plugin with correct configurations for both platforms.

</aside>

## Complete Example

Here's a complete manifest for a plugin that integrates Firebase ML Kit with a custom Activity:

```json
{
    "namespace": "FirebaseML",
    "bridge_functions": [
        {
            "name": "FirebaseML.Analyze",
            "android": "com.nativephp.plugins.firebaseml.AnalyzeFunctions.Analyze",
            "ios": "FirebaseMLFunctions.Analyze"
        }
    ],
    "events": [
        "Vendor\\FirebaseML\\Events\\AnalysisComplete"
    ],
    "android": {
        "permissions": [
            "android.permission.CAMERA",
            "android.permission.INTERNET"
        ],
        "features": [
            {"name": "android.hardware.camera", "required": true}
        ],
        "dependencies": {
            "implementation": [
                "com.google.firebase:firebase-ml-vision:24.1.0",
                "com.google.firebase:firebase-core:21.1.1"
            ]
        },
        "activities": [
            {
                "name": ".CameraPreviewActivity",
                "theme": "@style/Theme.AppCompat.Light.NoActionBar",
                "exported": false,
                "configChanges": "orientation|screenSize|keyboardHidden"
            }
        ],
        "meta_data": [
            {
                "name": "com.google.firebase.ml.vision.DEPENDENCIES",
                "value": "ocr"
            }
        ]
    },
    "ios": {
        "info_plist": {
            "NSCameraUsageDescription": "Camera is used for ML analysis"
        },
        "dependencies": {
            "pods": [
                {"name": "Firebase/MLVision", "version": "~> 10.0"},
                {"name": "Firebase/Core", "version": "~> 10.0"}
            ]
        },
        "background_modes": ["processing"],
        "entitlements": {
            "com.apple.developer.associated-domains": ["applinks:example.com"]
        }
    },
    "assets": {
        "android": {
            "google-services.json": "google-services.json"
        },
        "ios": {
            "GoogleService-Info.plist": "Resources/GoogleService-Info.plist"
        }
    },
    "secrets": {
        "FIREBASE_API_KEY": {
            "description": "Firebase API key from Firebase Console",
            "required": true
        }
    },
    "hooks": {
        "pre_compile": "nativephp:firebase-ml:setup"
    }
}
```

## Official Plugins & Dev Kit

Browse ready-made plugins for common use cases, or get the Plugin Dev Kit to build your own.
[Visit the NativePHP Plugin Marketplace →](https://nativephp.com/plugins)
