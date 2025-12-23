---
title: Introduction
order: 100
---

## What are Plugins?

Plugins extend NativePHP for Mobile with native functionality that goes beyond the built-in APIs. Need on-device ML,
Bluetooth, or a custom hardware integration? Plugins let you add these capabilities without forking the core package.

A plugin is a Composer package that bundles:
- **PHP code** — Facades, events, and service providers you use in Laravel
- **Native code** — Swift (iOS) and Kotlin (Android) implementations
- **A manifest** — Declares what the plugin provides and needs

When you build your app, NativePHP automatically discovers installed plugins and compiles their native code into your
app alongside the built-in features.

## Why Use Plugins?

The built-in [APIs](../apis/) cover common functionality like camera, biometrics, and push notifications. But mobile
platforms offer much more — AR, ML, NFC, health sensors, and countless third-party SDKs.

Plugins let the community build and share these integrations. Install a plugin and its features become available to
your PHP code just like the built-in APIs.

## What Plugins Can Do

Plugins have full access to native platform capabilities:

- **Bridge functions** — Call Swift/Kotlin code from PHP and get results back
- **Events** — Dispatch events from native code to your Livewire components
- **Permissions** — Declare required permissions (camera, location, etc.)
- **Dependencies** — Include native libraries via Gradle, CocoaPods, or Swift Package Manager
- **Custom repositories** — Use private Maven repos for enterprise SDKs
- **Android components** — Register Activities, Services, Receivers, and Content Providers
- **Assets** — Bundle ML models, configuration files, and other resources
- **Lifecycle hooks** — Run code at build time to download models, validate config, etc.
- **Secrets** — Declare required environment variables with validation

## Plugin Architecture

Plugins follow the same patterns as NativePHP's core:

```php
use Vendor\MyPlugin\Facades\MyPlugin;

// Call native functions
MyPlugin::doSomething();

// Listen for events
#[OnNative(MyPlugin\Events\SomethingHappened::class)]
public function handleResult($data)
{
    // Handle it
}
```

The native code runs on-device, communicates with your PHP through the bridge, and dispatches events back to your
Livewire components. It's the same model you're already using.

## Getting Started

Ready to build your own plugin? Check out [Creating Plugins](./creating-plugins) for the full guide.

If you're using [Claude Code](https://claude.com/claude-code), you can install specialized plugin development tools:

```shell
claude plugins:add https://github.com/NativePHP/ClaudePlugins/tree/main/nativephp-plugin-dev
```

This gives you expert agents for Kotlin and Swift, interactive scaffolding commands, and skills for native code patterns.
