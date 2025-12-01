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

The manifest declares everything about your plugin:

```json
{
    "name": "vendor/my-plugin",
    "namespace": "MyPlugin",
    "bridge_functions": [
        {
            "name": "MyPlugin.DoSomething",
            "ios": "MyPluginFunctions.DoSomething",
            "android": "com.vendor.plugin.myplugin.MyPluginFunctions.DoSomething"
        }
    ],
    "events": ["Vendor\\MyPlugin\\Events\\SomethingHappened"],
    "permissions": {
        "android": ["android.permission.SOME_PERMISSION"],
        "ios": {
            "NSCameraUsageDescription": "We need camera access"
        }
    }
}
```

The key fields:

- **namespace** — Used to generate bridge function registration code
- **bridge_functions** — Maps PHP calls to native implementations
- **events** — Event classes your plugin dispatches
- **permissions** — Platform permissions your plugin requires

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

## Plugin Writer Agent

If you're building a complex plugin, install the plugin-writer agent to help with native code patterns:

```shell
php artisan native:plugin:install-agent
```

This copies a specialized agent configuration to your project's `.claude/agents/` directory. The agent understands
NativePHP plugin patterns and can help write Swift/Kotlin bridge functions, event dispatching, and hook commands.
