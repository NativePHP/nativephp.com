---
title: Permissions & Dependencies
order: 700
---

## Declaring Permissions

If your plugin needs platform permissions (camera, microphone, location, etc.), declare them in the manifest.
NativePHP automatically merges these with the app's permissions during build.

## Android Permissions

List Android permissions as strings:

```json
{
    "permissions": {
        "android": [
            "android.permission.CAMERA",
            "android.permission.RECORD_AUDIO",
            "android.permission.ACCESS_FINE_LOCATION"
        ]
    }
}
```

These are added to the app's `AndroidManifest.xml` at build time.

## iOS Permissions

iOS requires usage descriptions for each permission. Provide these as key-value pairs:

```json
{
    "permissions": {
        "ios": {
            "NSCameraUsageDescription": "This app uses the camera for scanning",
            "NSMicrophoneUsageDescription": "This app records audio for transcription",
            "NSLocationWhenInUseUsageDescription": "This app needs your location"
        }
    }
}
```

These are merged into the app's `Info.plist`.

<aside>

Write clear, specific usage descriptions. Generic messages like "This app needs camera access" may cause App Store
rejection. Explain *why* you need the permission.

</aside>

## Android Dependencies

Add Gradle dependencies for Android:

```json
{
    "dependencies": {
        "android": {
            "implementation": [
                "com.google.mlkit:face-detection:16.1.5",
                "org.tensorflow:tensorflow-lite:2.13.0"
            ]
        }
    }
}
```

These are added to the app's `build.gradle` during compilation.

## iOS Dependencies

### Swift Packages

Add Swift Package dependencies for iOS:

```json
{
    "dependencies": {
        "ios": {
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

### CocoaPods

For libraries that only support CocoaPods, add them to the `cocoapods` array:

```json
{
    "dependencies": {
        "ios": {
            "cocoapods": [
                "GoogleMLKit/FaceDetection",
                "TensorFlowLiteSwift"
            ]
        }
    }
}
```

NativePHP generates a `Podfile` and runs `pod install` during the iOS build process.

<aside>

#### Prefer Swift Packages

When a library supports both, prefer Swift Packages over CocoaPods. They integrate more cleanly and build faster.

</aside>

<aside>

#### Prefer Platform APIs

When possible, use built-in platform APIs instead of third-party dependencies. This keeps your plugin lightweight
and avoids version conflicts with other plugins.

</aside>

## Full Example

Here's a complete permissions and dependencies section for an ML plugin:

```json
{
    "permissions": {
        "android": [
            "android.permission.CAMERA"
        ],
        "ios": {
            "NSCameraUsageDescription": "Camera is used for real-time object detection"
        }
    },
    "dependencies": {
        "android": {
            "implementation": [
                "com.google.mlkit:object-detection:17.0.0"
            ]
        },
        "ios": {
            "swift_packages": []
        }
    }
}
```
