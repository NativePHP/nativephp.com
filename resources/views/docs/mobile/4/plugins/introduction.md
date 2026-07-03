---
title: Introduction
order: 100
---

## What are Plugins?

Plugins extend NativePHP for Mobile with native functionality. Need on-device ML,
Bluetooth, or a custom hardware integration? Plugins let you add these capabilities without forking the core package.

A plugin is a Composer package that bundles:
- **PHP code** — Facades, events, and service providers you use in Laravel
- **Native code** — Swift (iOS) and Kotlin (Android) implementations
- **A manifest** — Declares what the plugin provides and needs

When you build your app, NativePHP compiles registered plugins' native code into your app.

## Why Plugins?

All native functionality in NativePHP Mobile comes through plugins — including official plugins for camera, biometrics,
push notifications, and more. This architecture means:

- **Official plugins** provide core functionality and serve as reference implementations
- **Community plugins** extend the platform with new capabilities
- **Your own plugins** let you integrate proprietary SDKs or custom native code

Install a plugin and its native features become available to your PHP code through a simple facade.

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

Or browse the [NativePHP Plugin Marketplace](https://nativephp.com/plugins) for ready-made plugins and the Dev Kit
to build your own.
