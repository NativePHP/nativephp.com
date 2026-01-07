---
title: Using Plugins
order: 200
---

## Installing a Plugin

Plugins are standard Composer packages. Install them like any other Laravel package:

```shell
composer require vendor/nativephp-plugin-name
```

The plugin's service provider will be auto-discovered by Laravel.

## Verify Installation

Check that NativePHP sees your plugin:

```shell
php artisan native:plugin:list
```

You'll see the plugin name, version, and what it provides (bridge functions, events, hooks).

## Rebuild Your App

After installing a plugin, rebuild to compile its native code:

```shell
php artisan native:run
```

The plugin's Swift and Kotlin code gets compiled into your app automatically.

## Using Plugin Features

Each plugin provides its own facade and follows the same patterns as the built-in APIs.

```php
use Vendor\PluginName\Facades\PluginName;

// Call a native function
$result = PluginName::doSomething(['option' => 'value']);
```

## Listening to Plugin Events

Plugins dispatch events just like the core APIs. Use the `#[OnNative]` attribute in your Livewire components:

```php
use Native\Mobile\Attributes\OnNative;
use Vendor\PluginName\Events\SomethingCompleted;

#[OnNative(SomethingCompleted::class)]
public function handleCompletion($result)
{
    // React to the event
}
```

<aside>

#### Check the Plugin's Docs

Each plugin should document its available methods, events, and any required permissions. Look for a README or docs
in the plugin's repository.

</aside>

## Permissions

Some plugins require additional permissions. These are declared in the plugin's manifest and automatically merged
into your app's configuration during build.

If a plugin needs camera access, microphone, or other sensitive permissions, you'll see them listed when you run
`native:plugin:list`.
