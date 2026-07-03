---
title: Using Plugins
order: 200
---

## Installing a Plugin

Plugins are standard Composer packages. Install them like any other Laravel package:

```shell
composer require vendor/nativephp-plugin-name
```

The plugin's PHP service provider will be auto-discovered by Laravel, but the native code won't be included in builds
until you explicitly register it.

### Installing Premium Plugins

Premium plugins from the [NativePHP Plugin Marketplace](/plugins) are served via a private Composer repository.
You'll need to configure Composer before you can install them.

**1. Add the NativePHP plugins repository:**

```shell
composer config repositories.nativephp-plugins composer https://plugins.nativephp.com
```

**2. Configure your credentials:**

```shell
composer config http-basic.plugins.nativephp.com your-email@example.com your-license-key
```

You can find your credentials on your [Purchased Plugins](/dashboard/purchased-plugins) dashboard.

**3. Install the plugin:**

Once configured, install premium plugins just like any other:

```shell
composer require vendor/nativephp-premium-plugin
```

## Register the Plugin

For security, plugins must be explicitly registered before their native code is compiled into your app. This prevents
transitive dependencies from automatically including native code without your consent.

First, ensure you've published the NativeServiceProvider:

```shell
php artisan vendor:publish --tag=nativephp-plugins-provider
```

Then register the plugin:

```shell
php artisan native:plugin:register vendor/nativephp-plugin-name
```

This adds the plugin to your `app/Providers/NativeServiceProvider.php` file.

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

Each plugin provides its own facade for interacting with native functionality.

```php
use Vendor\PluginName\Facades\PluginName;

// Call a native function
$result = PluginName::doSomething(['option' => 'value']);
```

## Listening to Plugin Events

Plugins dispatch events to your Livewire components. Use the `#[OnNative]` attribute to listen for them:

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

## Removing a Plugin

To completely uninstall a plugin:

```shell
php artisan native:plugin:uninstall vendor/nativephp-plugin-name
```

This command:
- Unregisters the plugin from your `NativeServiceProvider`
- Removes the package via Composer
- Removes the path repository from `composer.json` (if applicable)
- Optionally deletes the plugin source directory (for local path repositories)

Use `--force` to skip confirmation prompts, or `--keep-files` to preserve the source directory when uninstalling
a local plugin.

## Official Plugins & Dev Kit

Find ready-made plugins for common use cases, or get the Dev Kit to build your own.
[Visit the NativePHP Plugin Marketplace →](https://nativephp.com/plugins)
