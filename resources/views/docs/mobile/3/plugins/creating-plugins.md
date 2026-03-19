---
title: Creating Plugins
order: 300
---

## Scaffolding a Plugin

The quickest way to create a plugin is with the interactive scaffolding command:

```shell
php artisan native:plugin:create
```

This walks you through naming, namespace selection, and feature options, then generates a complete plugin structure.

<aside>

#### Build Plugins 10x Faster

Writing native Kotlin and Swift code is the hardest part of plugin development. The
[NativePHP Plugin Development Kit](/products/plugin-dev-kit) for Claude Code can generate production-ready
plugins in minutes — complete with bridge functions, events, permissions, and platform-specific implementations.

[Get the Plugin Dev Kit →](/products/plugin-dev-kit)

</aside>

## Plugin Structure

A plugin follows a standard layout:

```
my-plugin/
├── composer.json          # Package metadata, type must be "nativephp-plugin"
├── nativephp.json         # Plugin manifest
├── src/
│   ├── MyPluginServiceProvider.php
│   ├── MyPlugin.php       # Main class
│   ├── Facades/
│   │   └── MyPlugin.php
│   ├── Events/
│   │   └── SomethingHappened.php
│   └── Commands/          # Lifecycle hook commands
├── resources/
│   ├── android/src/       # Kotlin bridge functions
│   ├── ios/Sources/       # Swift bridge functions
│   └── js/                # JavaScript library stubs
```

## Android Package Naming

Android/Kotlin code must declare a package at the top of each file. Use your own vendor-namespaced package to avoid
conflicts:

```kotlin
// resources/android/src/MyPluginFunctions.kt
package com.myvendor.plugins.myplugin

import com.nativephp.mobile.bridge.BridgeFunction
import com.nativephp.mobile.bridge.BridgeResponse

object MyPluginFunctions {
    class DoSomething : BridgeFunction {
        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            return BridgeResponse.success(mapOf("status" to "done"))
        }
    }
}
```

The compiler places files based on their package declaration, so `package com.myvendor.plugins.myplugin` results in the file
being placed at `app/src/main/java/com/myvendor/plugins/myplugin/MyPluginFunctions.kt`.

<aside>
Files without package declarations will be placed in a fallback location, but this is not recommended. Always declare
packages in your Kotlin files.
</aside>

Reference the full package path in your manifest's bridge functions:

```json
{
    "bridge_functions": [{
        "name": "MyPlugin.DoSomething",
        "android": "com.myvendor.plugins.myplugin.MyPluginFunctions.DoSomething"
    }]
}
```

## The composer.json

Your `composer.json` must specify the plugin type:

```json
{
    "name": "vendor/my-plugin",
    "type": "nativephp-plugin",
    "extra": {
        "laravel": {
            "providers": ["Vendor\\MyPlugin\\MyPluginServiceProvider"]
        },
        "nativephp": {
            "manifest": "nativephp.json"
        }
    }
}
```

The `type: nativephp-plugin` tells NativePHP to look for native code in this package.

## The nativephp.json Manifest

The manifest declares native-specific configuration for your plugin. Package metadata (`name`, `version`, `description`,
`service_provider`) comes from your `composer.json` — don't duplicate it here.

```json
{
    "namespace": "MyPlugin",
    "bridge_functions": [
        {
            "name": "MyPlugin.DoSomething",
            "ios": "MyPluginFunctions.DoSomething",
            "android": "com.nativephp.plugins.myplugin.MyPluginFunctions.DoSomething"
        }
    ],
    "events": ["Vendor\\MyPlugin\\Events\\SomethingHappened"],
    "android": {
        "permissions": ["android.permission.CAMERA"],
        "dependencies": {
            "implementation": ["com.google.mlkit:barcode-scanning:17.2.0"]
        }
    },
    "ios": {
        "info_plist": {
            "NSCameraUsageDescription": "Camera is used for scanning"
        },
        "dependencies": {
            "pods": [{"name": "GoogleMLKit/BarcodeScanning", "version": "~> 4.0"}]
        }
    }
}
```

### Manifest Fields

| Field | Required | Description |
|-------|----------|-------------|
| `namespace` | Yes | Namespace for the plugin (used for code generation and directory structure) |
| `bridge_functions` | No | Array of native function mappings |
| `events` | No | Event classes the plugin dispatches |
| `android.permissions` | No | Android permission strings |
| `android.features` | No | Android uses-feature declarations |
| `android.dependencies` | No | Gradle dependencies |
| `android.repositories` | No | Custom Maven repositories |
| `android.activities` | No | Activities to register in manifest |
| `android.services` | No | Services to register in manifest |
| `android.receivers` | No | Broadcast receivers to register |
| `android.providers` | No | Content providers to register |
| `android.meta_data` | No | Application meta-data entries |
| `android.min_version` | No | Minimum Android SDK version required |
| `android.init_function` | No | Native function to call during app initialization |
| `ios.info_plist` | No | Info.plist entries (permissions, API keys) |
| `ios.dependencies` | No | Swift packages and CocoaPods |
| `ios.background_modes` | No | UIBackgroundModes values |
| `ios.entitlements` | No | App entitlements |
| `ios.capabilities` | No | iOS capabilities for Xcode project |
| `ios.min_version` | No | Minimum iOS version required |
| `ios.init_function` | No | Native function to call during app initialization |
| `assets` | No | Declarative asset copying |
| `hooks` | No | Lifecycle hook commands |
| `secrets` | No | Required environment variables |

See [Advanced Configuration](advanced-configuration) for detailed documentation on each field.

## Local Development

During development, add your plugin to your app's `composer.json` as a path repository:

```json
{
    "repositories": [
        {"type": "path", "url": "../packages/my-plugin"}
    ]
}
```

Then require it:

```shell
composer require vendor/my-plugin
```

Changes to your plugin's PHP code are picked up immediately. Changes to native code require a rebuild with
`php artisan native:run`.

When testing significant changes to your plugin's native code or manifest, you may need to force a fresh install of
the native projects:

```shell
php artisan native:install --force
```

This ensures the native projects are rebuilt from scratch with your latest plugin configuration.

<aside>

#### Validate Early and Often

Run `php artisan native:plugin:validate` to catch manifest errors, missing native code, or mismatched function
declarations before you try to build.

</aside>

## Registering Plugins

After installing a plugin with Composer, you need to register it so it gets compiled into your native builds.

### First Time Setup

Publish the NativeServiceProvider:

```shell
php artisan vendor:publish --tag=nativephp-plugins-provider
```

This creates `app/Providers/NativeServiceProvider.php`.

### Register a Plugin

```shell
php artisan native:plugin:register vendor/plugin-name
```

This automatically adds the plugin's service provider to your `plugins()` array:

```php
public function plugins(): array
{
    return [
        \Vendor\PluginName\PluginNameServiceProvider::class,
    ];
}
```

### List Plugins

```shell
# Show registered plugins
php artisan native:plugin:list

# Show all installed plugins (including unregistered)
php artisan native:plugin:list --all
```

### Remove a Plugin

```shell
php artisan native:plugin:register vendor/plugin-name --remove
```

<aside>
This explicit registration is a security measure. It prevents transitive dependencies from automatically
registering plugins without your consent.
</aside>

## JavaScript Library

Plugins can provide a JavaScript library for SPA frameworks. The scaffolding creates a stub in `resources/js/`:

```js
// resources/js/myPlugin.js
const baseUrl = '/_native/api/call';

async function bridgeCall(method, params = {}) {
    const response = await fetch(baseUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ method, params })
    });
    return response.json();
}

export async function doSomething(options = {}) {
    return bridgeCall('MyPlugin.DoSomething', options);
}
```

Users can then import your functions directly in Vue, React, or vanilla JS.

## NativePHP Plugin Development Kit

<aside>

#### The Fastest Way to Build Native Plugins

Most Laravel developers aren't Kotlin or Swift experts — and that's okay. The **NativePHP Plugin Development Kit** for
Claude Code turns natural language into production-ready native code.

Describe what you want ("a plugin that scans barcodes and returns product info") and get a complete, working plugin
with proper bridge functions, events, permissions, and both iOS and Android implementations.

[Get the Plugin Dev Kit →](/products/plugin-dev-kit)

</aside>

If you're using [Claude Code](https://claude.com/claude-code), the Plugin Development Kit supercharges your workflow
with specialized agents trained on NativePHP's architecture.

### What's Included

- **Kotlin/Android Expert Agent** — Writes correct bridge functions, handles Android lifecycles, configures Gradle
- **Swift/iOS Expert Agent** — Implements iOS bridge functions, manages Info.plist, configures SPM/CocoaPods
- **Plugin Architect Agent** — Designs plugin structure, manifest configuration, and Laravel integration
- **Interactive Commands** — `/create-nativephp-plugin` scaffolds complete plugins from a description
- **Validation Tools** — `/validate-nativephp-plugin` catches errors before you build

### Why It's Worth It

Writing native mobile code is hard. These agents understand:

- NativePHP's bridge function patterns and response formats
- Platform-specific APIs and how to expose them to PHP
- Permission declarations, entitlements, and manifest configuration
- Event dispatching from native code to Livewire components
- Dependency management across Gradle, CocoaPods, and SPM

Instead of learning two new languages and their ecosystems, describe what you need and let the agents handle the
implementation details.

[Get the Plugin Dev Kit →](/products/plugin-dev-kit)

## AI Development Tools

NativePHP includes built-in commands for AI-assisted plugin development.

### Install Development Agents

Install specialized AI agents for plugin development:

```shell
php artisan native:plugin:install-agent
```

This copies agent definition files to your project's `.claude/agents/` directory. Available agents include:

- **kotlin-android-expert** — Deep Android/Kotlin native development
- **swift-ios-expert** — Deep iOS/Swift native development
- **js-bridge-expert** — JavaScript/TypeScript client integration
- **plugin-writer** — General plugin scaffolding and structure
- **plugin-docs-writer** — Documentation and Boost guidelines

Use `--all` to install all agents without prompting, or `--force` to overwrite existing files.

### Create Boost Guidelines

If you're using [Boost](https://laravel.com/ai/boost), create AI guidelines for your plugin:

```shell
php artisan native:plugin:boost
```

This generates a `resources/boost/guidelines/core.blade.php` file in your plugin that documents:

- How to use your plugin's facade
- Available methods and their descriptions
- Events and how to listen for them
- JavaScript usage examples

When users install your plugin and run `php artisan boost:install`, these guidelines are automatically loaded,
helping AI assistants understand how to use your plugin correctly.

## Ready to Build?

You now have everything you need to create NativePHP plugins. For most developers, the
[Plugin Development Kit](/products/plugin-dev-kit) is the fastest path from idea to working plugin — it
handles the native code complexity so you can focus on what your plugin does, not how to write Kotlin and Swift.
