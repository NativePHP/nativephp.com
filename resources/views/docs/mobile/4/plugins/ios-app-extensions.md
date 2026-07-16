---
title: iOS App Extensions
order: 725
---

NativePHP plugins can ship isolated iOS app-extension targets alongside the host application. The initial supported
extension type is a WidgetKit widget extension.

App extensions have their own bundle identifier, sources, `Info.plist`, entitlements, build settings, and signing
profile. NativePHP generates the target and embeds its `.appex` product into both the device and simulator hosts.

## Plugin structure

Keep host bridge code and extension code in separate directories. Extension sources are compiled only into the extension
target and are not copied into the host application.

```text
resources/
└── ios/
    ├── Sources/
    │   └── WidgetBridge.swift
    └── extension/
        └── MyWidgets.swift
```

The extension source must contain a valid WidgetKit entry point, such as an `@main` `Widget` or `WidgetBundle`.

## Declaring a widget extension

Add an `extension_targets` list to the plugin's `ios` configuration:

```json
{
    "namespace": "MyWidgets",
    "ios": {
        "min_version": "17.0",
        "info_plist": {
            "NativePHPWidgetAppGroup": "group.${APP_ID}.widgets"
        },
        "entitlements": {
            "com.apple.security.application-groups": [
                "group.${APP_ID}.widgets"
            ]
        },
        "extension_targets": [
            {
                "name": "MyWidgetsExtension",
                "type": "widget-extension",
                "bundle_id_suffix": "widgets",
                "deployment_target": "17.0",
                "sources_dir": "extension",
                "info_plist": {
                    "CFBundleDisplayName": "My Widgets",
                    "NativePHPWidgetAppGroup": "group.${APP_ID}.widgets"
                }
            }
        ]
    }
}
```

This example generates the extension bundle identifier `{APP_ID}.widgets` and the shared App Group
`group.{APP_ID}.widgets`.

### Target fields

| Field | Required | Description |
|-------|----------|-------------|
| `name` | Yes | Unique Xcode target name using letters, numbers, and underscores |
| `type` | Yes | Must currently be `widget-extension` |
| `bundle_id_suffix` | Yes | Component appended to the host application identifier |
| `sources_dir` | Yes | Directory below `resources/ios` containing extension-only sources |
| `deployment_target` | No | Extension iOS deployment target; defaults to `ios.min_version` or `17.0` |
| `info_plist` | No | Additional extension `Info.plist` values |

NativePHP owns the bundle identifier, executable, version, package type, and other compiler-generated plist keys. A
manifest that attempts to override those keys fails validation.

## Manifest environment values

`${APP_ID}` is always available to extension plist and entitlement values. Every other `${ENV_VAR}` placeholder must be
declared in the plugin's top-level `secrets` allowlist:

```json
{
    "secrets": {
        "PUBLIC_WIDGET_API_KEY": {
            "description": "Public API key embedded in the widget extension",
            "required": true
        }
    },
    "ios": {
        "extension_targets": [
            {
                "name": "MyWidgetsExtension",
                "type": "widget-extension",
                "bundle_id_suffix": "widgets",
                "sources_dir": "extension",
                "info_plist": {
                    "PublicWidgetAPIKey": "${PUBLIC_WIDGET_API_KEY}"
                }
            }
        ]
    }
}
```

An undeclared placeholder fails the build instead of reading an arbitrary build-machine environment value. Values placed
in a plist or entitlement ship inside the built product, so never use this mechanism for signing credentials, private API
tokens, or server-side secrets.

## Sharing data with the host app

Widget extensions run outside the host application process. Use an App Group to share data between the NativePHP host
and the extension:

1. Declare the same `com.apple.security.application-groups` value in `ios.entitlements`.
2. Pass the generated group name to both the host and extension, commonly through `Info.plist`.
3. Read and write shared values with `UserDefaults(suiteName:)` or an App Group container.
4. Ask WidgetKit to reload the relevant timeline after the host writes new data.

Plugin entitlements merge into the existing host entitlement file. Existing host scalar values win, list values are
unioned without duplicates, and nested dictionaries merge recursively. Only the App Group entitlement is copied from the
plugin into the generated widget extension.

<aside>

App Groups must also exist for the host and extension identifiers in your Apple Developer account and provisioning
profiles.

</aside>

## Background widget updates

WidgetKit does not keep the NativePHP application or PHP runtime continuously running after the app closes. To update a
widget in the background, its `TimelineProvider` should return dated future entries. WidgetKit can display those entries
while the host process is terminated.

iOS controls refresh budgets and may delay requested reloads. Timeline dates are display guidance rather than real-time
scheduling guarantees. Fetch or prepare enough data for useful future entries and use an appropriate timeline reload
policy.

See Apple's documentation on
[keeping a widget up to date](https://developer.apple.com/documentation/widgetkit/keeping-a-widget-up-to-date).

## Signing and packaging

Development builds can use automatic signing. Manual distribution signing requires a provisioning profile for every
generated extension bundle identifier in addition to the host profile.

Set `IOS_EXTENSION_PROVISIONING_PROFILES` to a JSON object keyed by the complete extension bundle identifier. Values may
be profile paths or strict base64-encoded profile contents:

```shell
export IOS_EXTENSION_PROVISIONING_PROFILES='{
  "com.example.myapp.widgets": "/secure/profiles/MyWidgets.mobileprovision"
}'
```

NativePHP validates that each profile's application identifier exactly matches its extension bundle identifier before
installing or using it.

## Building and testing

Validate the plugin, then rebuild the native project:

```shell
php artisan native:plugin:validate
php artisan native:run
```

For a WidgetKit plugin, verify all of the following on a simulator or device:

1. The widget appears in the iOS widget gallery.
2. Each supported family renders real and placeholder data.
3. Host writes reach the extension through the App Group.
4. Widget taps open the expected deep link.
5. A timeline advances after the host application is terminated.
6. A release archive contains the extension under `PlugIns/*.appex`.

Opening the generated project in Xcode is supported. NativePHP uses deterministic object identifiers to recover and
recreate managed extension objects if Xcode rewrites the project and removes NativePHP's marker comments.

## Current limitations

- Only `widget-extension` targets are supported.
- This configuration is iOS-specific; Android widget components continue to use Android manifest and asset declarations.
- NativePHP manages the generated target. Add persistent extension configuration to `nativephp.json` and source files,
  rather than editing generated Xcode objects by hand.
