---
title: Feature Bundles
order: 725
---

## The Problem

A plugin that wraps a large SDK usually only needs a slice of it. The Firebase plugin can send push notifications,
report crashes, attest app integrity, trace performance, and display in-app messages — but an app that just wants push
should not link the crash reporter, the attestation provider, and the performance instrumentation into its binary.

Declaring everything at the top level of `nativephp.json` means every app pays for every capability: bigger downloads,
more SDK initialization at launch, more permissions to justify in review, and more surface to break a build.

**Feature bundles** let a plugin declare optional pieces of itself behind an environment variable. What an app doesn't
turn on is never compiled in.

## Declaring a Feature

Add a `features` map to `nativephp.json`. Each entry is a miniature manifest gated by one environment variable:

```json
{
    "namespace": "Firebase",
    "bridge_functions": [
        {"name": "PushNotification.GetToken", "ios": "PushNotificationFunctions.GetToken"}
    ],
    "ios": {
        "dependencies": {
            "swift_packages": [
                {
                    "url": "https://github.com/firebase/firebase-ios-sdk",
                    "version": "12.6.0",
                    "products": ["FirebaseCore", "FirebaseMessaging"]
                }
            ]
        }
    },

    "features": {
        "crashlytics": {
            "env": "NATIVEPHP_FIREBASE_CRASHLYTICS",
            "description": "Crash reports plus PHP exceptions as non-fatals",
            "bridge_functions": [
                {
                    "name": "Crashlytics.RecordException",
                    "ios": "CrashlyticsFunctions.RecordException",
                    "android": "com.nativephp.firebase.CrashlyticsFunctions.RecordException"
                }
            ],
            "ios": {
                "info_plist": {"FirebaseCrashlyticsCollectionEnabled": false},
                "dependencies": {
                    "swift_packages": [
                        {
                            "url": "https://github.com/firebase/firebase-ios-sdk",
                            "products": ["FirebaseCrashlytics"]
                        }
                    ]
                }
            },
            "android": {
                "dependencies": {"implementation": ["com.google.firebase:firebase-crashlytics"]}
            }
        }
    }
}
```

An app enables it in `.env`:

```dotenv
NATIVEPHP_FIREBASE_CRASHLYTICS=true
```

Then rebuilds. `php artisan native:plugin:list` shows the bridge functions appear and disappear as you flip the flag.

## What a Bundle May Declare

| Key | Behavior when enabled |
|---|---|
| `env` | The variable that gates the bundle. Omit it and the bundle is always on — useful purely for grouping. |
| `bridge_functions` | Appended to the plugin's own. |
| `events` | Merged with the plugin's own (deduplicated). |
| `ios` | Merged into the plugin's `ios` section. |
| `android` | Merged into the plugin's `android` section. |
| `description` | Documentation for humans; ignored by the compiler. |

Platform sections merge by shape: **lists concatenate** (permissions, capabilities, Gradle coordinates), **maps merge
key-wise** (`info_plist`, `entitlements`, `meta_data`), and `dependencies` recurses so a feature can add packages
without restating the plugin's.

Resolution happens when the manifest is parsed, before anything reads it — so the compilers, `native:plugin:list`, and
every other consumer see one effective manifest and need no awareness of features at all.

## Feature Source Files

Native sources for a feature live **outside** `resources/ios` and `resources/android`:

```
resources/
├── ios/                          # always compiled
│   └── PushNotificationFunctions.swift
├── android/                      # always compiled
│   └── PushNotificationFunctions.kt
└── features/
    └── crashlytics/              # only when the gate is on
        ├── ios/CrashlyticsFunctions.swift
        └── android/CrashlyticsFunctions.kt
```

<aside>

#### Why not a subdirectory of `resources/ios`?

Because those roots are copied **recursively**. A `resources/ios/crashlytics/` folder would compile into every app
regardless of the gate — and then reference SDK products the build never linked, failing compilation for apps that
never asked for the feature. Keeping feature sources in `resources/features/` is what makes the gate real rather than
decorative.

</aside>

Override the location per feature with `source_dir` if you need a different layout:

```json
"crashlytics": {
    "env": "NATIVEPHP_FIREBASE_CRASHLYTICS",
    "ios": {"source_dir": "resources/crashlytics-swift"},
    "android": {"source_dir": "resources/crashlytics-kotlin"}
}
```

## Sharing an SDK Package Between Features

Several features often come from one SDK. Declare the **same package url** in each bundle, listing only the products
that feature needs:

```json
"ios": {
    "dependencies": {
        "swift_packages": [
            {"url": "https://github.com/firebase/firebase-ios-sdk", "products": ["FirebasePerformance"]}
        ]
    }
}
```

The iOS compiler resolves a repeated url to the package reference already in the Xcode project and attaches the extra
products to the target, so there is no duplicate package entry — and no need to restate the version.

## Truthiness

The gate is read with Laravel's boolean rules, so `true`, `1`, `"true"`, and `on` enable a feature, while `false`,
`0`, `""`, and an unset variable leave it off. An unset variable is the default — features are opt-in.

## What a Disabled Feature Costs

Nothing. When the gate is off:

- its native sources are not copied into the project
- its SDK products and dependencies are not linked
- its bridge functions are not registered — so no generated registration code references classes that were never
  compiled
- its `info_plist`, `entitlements`, `meta_data`, and permissions entries never appear

The PHP surface of a feature (a facade, a service class) can still ship unconditionally: bridge calls degrade to a
no-op off-device, so `Crashlytics::log()` in an app that never enabled the feature returns `false` rather than
erroring.

<aside>

#### Version requirement

Feature bundles need a core that understands them. On an older core the `features` key is **silently ignored** —
sources are not copied and bridge functions never register, with no warning. If your plugin ships features, raise its
`nativephp/mobile` constraint to a version that supports them, and consider a test that builds a
`Native\Mobile\Plugins\PluginManifest` from your manifest and asserts a gated bridge function appears when the
variable is set.

</aside>

## Documenting Features for Your Users

An app developer cannot discover a feature by reading code. List each variable in your plugin's README with what it
adds and what it costs:

```markdown
| Variable | Adds |
|---|---|
| `NATIVEPHP_FIREBASE_CRASHLYTICS` | Crash reporting, PHP exceptions as non-fatals |
| `NATIVEPHP_FIREBASE_PERFORMANCE`  | Custom traces and automatic HTTP/startup metrics |
```

## Official Plugins & Dev Kit

Skip the configuration complexity — browse ready-made plugins or get the Dev Kit to build your own.
[Visit the NativePHP Plugin Marketplace →](https://nativephp.com/plugins)
