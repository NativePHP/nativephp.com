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

The manifest declares everything about your plugin. Platform-specific configuration is grouped under `android` and `ios`
keys:

```json
{
    "name": "vendor/my-plugin",
    "namespace": "MyPlugin",
    "version": "1.0.0",
    "description": "A sample plugin",
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
| `name` | Yes | Package name in vendor/package format |
| `namespace` | Yes | PHP namespace for the plugin (used for code generation) |
| `version` | No | Semantic version (default: 1.0.0) |
| `description` | No | Human-readable description |
| `bridge_functions` | No | Array of native function mappings |
| `events` | No | Event classes the plugin dispatches |
| `android` | No | Android-specific configuration |
| `ios` | No | iOS-specific configuration |
| `assets` | No | Declarative asset copying |
| `hooks` | No | Lifecycle hook commands |
| `secrets` | No | Required environment variables |
| `service_provider` | No | Fully-qualified service provider class |

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

<aside>

#### Validate Early and Often

Run `php artisan native:plugin:validate` to catch manifest errors, missing native code, or mismatched function
declarations before you try to build.

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

## Claude Code Plugin

If you're using [Claude Code](https://claude.com/claude-code), install the NativePHP plugin development tools from
the Claude Code Plugins marketplace. This gives you specialized agents and skills for writing native code.

### Installation

```shell
claude plugins:add https://github.com/NativePHP/ClaudePlugins/tree/main/nativephp-plugin-dev
```

### What's Included

The plugin provides:

- **Specialized Agents** — Expert agents for Kotlin/Android and Swift/iOS native code
- **Plugin Scaffold Command** — Run `/create-nativephp-plugin` to scaffold a complete plugin
- **Plugin Validator** — Run `/validate-nativephp-plugin` to check your plugin structure
- **Skills for Native Patterns** — Documentation for bridge functions, events, and architecture

### Usage

Once installed, you can:

- Ask Claude Code to "create a NativePHP plugin for [your use case]"
- Run `/create-nativephp-plugin` to scaffold a new plugin interactively
- Run `/validate-nativephp-plugin` to validate your plugin structure
- Ask about native code patterns — the agents understand NativePHP conventions

The agents are context-aware and will help you write correct Kotlin bridge functions, Swift implementations,
manifest configuration, and Laravel facades.
