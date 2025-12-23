---
title: Advanced Configuration
order: 750
---

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
| `foregroundServiceType` | Type for foreground services (camera, microphone, location, etc.) |

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

Use [lifecycle hooks](lifecycle-hooks.md) for complex asset handling like downloading large files, unzipping archives,
or conditional asset placement.

</aside>

## Complete Example

Here's a complete manifest for a plugin that integrates Firebase ML Kit with a custom Activity:

```json
{
    "name": "vendor/firebase-ml-plugin",
    "namespace": "FirebaseML",
    "version": "2.0.0",
    "description": "Firebase ML Kit integration for NativePHP",
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
        ]
    },
    "ios": {
        "permissions": {
            "NSCameraUsageDescription": "Camera is used for ML analysis"
        },
        "dependencies": {
            "pods": [
                {"name": "Firebase/MLVision", "version": "~> 10.0"},
                {"name": "Firebase/Core", "version": "~> 10.0"}
            ]
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
    },
    "service_provider": "Vendor\\FirebaseML\\FirebaseMLServiceProvider"
}
```
