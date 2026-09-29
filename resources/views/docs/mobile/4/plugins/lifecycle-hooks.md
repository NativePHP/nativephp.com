---
title: Lifecycle Hooks
order: 600
---

## What are Lifecycle Hooks?

Hooks let your plugin run code at specific points during the build process. Need to download an ML model before
compilation? Copy assets to the right platform directory? Run validation? Hooks handle these scenarios.

<aside>

#### Declarative vs Programmatic Assets

For simple file copying, use the [declarative `assets` field](advanced-configuration#declarative-assets) in your
manifest. Use the `copy_assets` hook only when you need dynamic behavior like downloading files, unzipping archives, or
conditional copying.

</aside>

## Available Hooks

| Hook | When it Runs |
|------|--------------|
| `pre_compile` | Before native code compilation |
| `post_compile` | After compilation, before build |
| `copy_assets` | When copying assets to native projects (runs after declarative asset copying) |
| `post_build` | After a successful build |

## Creating Hook Commands

Generate a hook command with the scaffolding tool:

```shell
php artisan native:plugin:make-hook
```

This walks you through selecting your plugin and which hooks to create. It generates the command class, updates
your manifest, and registers the command in your service provider.

## Hook Command Structure

Hook commands extend `NativePluginHookCommand`:

```php
use Native\Mobile\Plugins\Commands\NativePluginHookCommand;

class CopyAssetsCommand extends NativePluginHookCommand
{
    protected $signature = 'nativephp:my-plugin:copy-assets';

    public function handle(): int
    {
        if ($this->isAndroid()) {
            $this->copyToAndroidAssets('models/model.tflite', 'models/model.tflite');
        }

        if ($this->isIos()) {
            $this->copyToIosBundle('models/model.mlmodel', 'models/model.mlmodel');
        }

        return self::SUCCESS;
    }
}
```

## Available Helpers

The base command provides helpers for common tasks:

**Platform Detection:**
- `$this->platform()` — Returns `'ios'` or `'android'`
- `$this->isIos()`, `$this->isAndroid()` — Boolean checks

**Paths:**
- `$this->buildPath()` — Path to the native project being built
- `$this->pluginPath()` — Path to your plugin package
- `$this->appId()` — The app's bundle ID (e.g., `com.example.app`)

**File Operations:**
- `$this->copyToAndroidAssets($src, $dest)` — Copy to Android assets
- `$this->copyToIosBundle($src, $dest)` — Copy to iOS bundle
- `$this->downloadIfMissing($url, $dest)` — Download a file if it doesn't exist
- `$this->unzip($zipPath, $extractTo)` — Extract a zip file

## Declaring Hooks in the Manifest

Add hooks to your `nativephp.json`:

```json
{
    "hooks": {
        "copy_assets": "nativephp:my-plugin:copy-assets",
        "pre_compile": "nativephp:my-plugin:pre-compile"
    }
}
```

The value is your Artisan command signature.

## Example: Downloading an ML Model

```php
public function handle(): int
{
    $modelPath = $this->pluginPath() . '/resources/models/model.tflite';

    // Download if not cached locally
    $this->downloadIfMissing(
        'https://example.com/models/v2/model.tflite',
        $modelPath
    );

    // Copy to the appropriate platform location
    if ($this->isAndroid()) {
        $this->copyToAndroidAssets('models/model.tflite', 'models/model.tflite');
        $this->info('Model copied to Android assets');
    }

    if ($this->isIos()) {
        $this->copyToIosBundle('models/model.tflite', 'models/model.tflite');
        $this->info('Model copied to iOS bundle');
    }

    return self::SUCCESS;
}
```

<aside>

Hooks run as normal Artisan commands. You have full access to Laravel's console helpers — `$this->info()`,
`$this->warn()`, progress bars, and more.

</aside>

## Official Plugins & Dev Kit

Browse ready-made plugins or get the Dev Kit to build your own.
[Visit the NativePHP Plugin Marketplace →](https://nativephp.com/plugins)
