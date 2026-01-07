---
title: Permissions & Dependencies
order: 700
---

## Platform Configuration

Platform-specific settings are grouped under `android` and `ios` keys in your manifest. This keeps all configuration for
each platform together:

```json
{
    "android": {
        "permissions": [...],
        "dependencies": {...},
        "repositories": [...],
        "activities": [...],
        "services": [...]
    },
    "ios": {
        "info_plist": {...},
        "dependencies": {...}
    }
}
```

## Permissions

### Android Permissions

List Android permissions as strings under `android.permissions`:

```json
{
    "android": {
        "permissions": [
            "android.permission.CAMERA",
            "android.permission.RECORD_AUDIO",
            "android.permission.ACCESS_FINE_LOCATION"
        ]
    }
}
```

These are added to the app's `AndroidManifest.xml` at build time.

### iOS Info.plist Entries

iOS requires usage descriptions for each permission, plus any API keys or configuration tokens your plugin needs. Provide
these as key-value pairs under `ios.info_plist`:

```json
{
    "ios": {
        "info_plist": {
            "NSCameraUsageDescription": "This app uses the camera for scanning",
            "NSMicrophoneUsageDescription": "This app records audio for transcription",
            "NSLocationWhenInUseUsageDescription": "This app needs your location",
            "MBXAccessToken": "${MAPBOX_ACCESS_TOKEN}"
        }
    }
}
```

These are merged into the app's `Info.plist`. You can include:
- Permission usage descriptions (`NS*UsageDescription` keys)
- API tokens and configuration keys
- Any other Info.plist entries your plugin requires

Use `${ENV_VAR}` placeholders for sensitive values like API tokens.

<aside>

Write clear, specific usage descriptions. Generic messages like "This app needs camera access" may cause App Store
rejection. Explain *why* you need the permission.

</aside>

## Dependencies

### Android Dependencies

Add Gradle dependencies under `android.dependencies`:

```json
{
    "android": {
        "dependencies": {
            "implementation": [
                "com.google.mlkit:face-detection:16.1.5",
                "org.tensorflow:tensorflow-lite:2.13.0"
            ]
        }
    }
}
```

These are added to the app's `build.gradle.kts` during compilation. You can use any Gradle dependency type:

- `implementation` — Standard dependency
- `api` — Exposed to consumers
- `compileOnly` — Compile-time only
- `runtimeOnly` — Runtime only

### iOS Dependencies

#### CocoaPods

For CocoaPods dependencies, use the `pods` array:

```json
{
    "ios": {
        "dependencies": {
            "pods": [
                {"name": "GoogleMLKit/FaceDetection", "version": "~> 4.0"},
                {"name": "TensorFlowLiteSwift", "version": "~> 2.13"}
            ]
        }
    }
}
```

Each pod object accepts:
- `name` — The pod name (required)
- `version` — Version constraint (optional, e.g., `~> 4.0`, `>= 1.0`)

NativePHP generates a `Podfile` and runs `pod install` during the iOS build process.

#### Swift Packages

For Swift Package Manager dependencies:

```json
{
    "ios": {
        "dependencies": {
            "swift_packages": [
                {
                    "url": "https://github.com/example/SomePackage",
                    "version": "1.0.0"
                }
            ]
        }
    }
}
```

<aside>

#### Prefer Swift Packages

When a library supports both, prefer Swift Packages over CocoaPods. They integrate more cleanly and build faster.

</aside>

## Custom Repositories

Some dependencies require private or non-standard Maven repositories (like Mapbox). Add them under
`android.repositories`:

```json
{
    "android": {
        "repositories": [
            {
                "url": "https://api.mapbox.com/downloads/v2/releases/maven",
                "credentials": {
                    "username": "mapbox",
                    "password": "${MAPBOX_DOWNLOADS_TOKEN}"
                }
            }
        ]
    }
}
```

Repository configuration:
- `url` — The repository URL (required)
- `credentials` — Optional authentication
  - `username` — Username or token name
  - `password` — Password or token (supports `${ENV_VAR}` placeholders)

These are added to the app's `settings.gradle.kts`.

<aside>

#### Environment Variable Placeholders

Use `${ENV_VAR}` syntax for sensitive values. The placeholder is replaced with the environment variable value at build
time. Combine this with the `secrets` feature to validate required variables before building.

</aside>

## Full Example

Here's a complete manifest for an ML plugin that uses Mapbox maps:

```json
{
    "name": "vendor/ml-maps-plugin",
    "namespace": "MLMaps",
    "android": {
        "permissions": [
            "android.permission.CAMERA",
            "android.permission.ACCESS_FINE_LOCATION"
        ],
        "dependencies": {
            "implementation": [
                "com.google.mlkit:object-detection:17.0.0",
                "com.mapbox.maps:android:11.0.0"
            ]
        },
        "repositories": [
            {
                "url": "https://api.mapbox.com/downloads/v2/releases/maven",
                "credentials": {
                    "username": "mapbox",
                    "password": "${MAPBOX_DOWNLOADS_TOKEN}"
                }
            }
        ]
    },
    "ios": {
        "info_plist": {
            "NSCameraUsageDescription": "Camera is used for real-time object detection",
            "NSLocationWhenInUseUsageDescription": "Location is used to display your position on the map",
            "MBXAccessToken": "${MAPBOX_PUBLIC_TOKEN}"
        },
        "dependencies": {
            "pods": [
                {"name": "MapboxMaps", "version": "~> 11.0"}
            ]
        }
    },
    "secrets": {
        "MAPBOX_DOWNLOADS_TOKEN": {
            "description": "Mapbox SDK download token from mapbox.com/account/access-tokens",
            "required": true
        },
        "MAPBOX_PUBLIC_TOKEN": {
            "description": "Mapbox public access token for runtime API calls",
            "required": true
        }
    }
}
```
