---
title: Application Lifecycle
order: 1
---

# NativePHP Application Lifecycle

When your NativePHP application starts - whether it's in development or production - it performs a series of steps to get your application up and running.

1. The native shell (Electron or Tauri) is started.
2. NativePHP runs `php artisan migrate` to ensure your database is up-to-date.
3. NativePHP runs `php artisan serve` to start the PHP development server.
4. NativePHP boots your application by running the `boot()` method on your `NativeAppServiceProvider`.

In addition to the `boot()` method, NativePHP also dispatches a `Native\Laravel\Events\App\ApplicationBooted` event.

## The NativeAppServiceProvider

When running `php artisan native:install`, NativePHP publishes a `NativeAppServiceProvider` to `app/Providers/NativeAppServiceProvider.php`.

You may use this service provider to boostrap your application. 
For example, you may want to open a window, register global shortcuts, or configure your application menu.

The default `NativeAppServiceProvider` looks like this:

```php
namespace App\Providers;

use Native\Laravel\Facades\ContextMenu;
use Native\Laravel\Facades\Dock;
use Native\Laravel\Facades\Window;
use Native\Laravel\GlobalShortcut;
use Native\Laravel\Menu\Menu;

class NativeAppServiceProvider
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Menu::new()
            ->appMenu()
            ->submenu('About', Menu::new()
                ->link('https://nativephp.com', 'NativePHP')
            )
            ->submenu('View', Menu::new()
                ->toggleFullscreen()
                ->separator()
                ->toggleDevTools()
            )
            ->register();

        Window::open()
            ->width(800)
            ->height(800);
    }
}
```

## Customize php.ini With Provider

you may to customize you php.ini with `NativeAppServiceProvider`:

```php
<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Window::open();
    }


    public function phpIni(): array
    {
        return [
            'memory_limit' => '512M',
            'display_errors' => '1',
            'error_reporting' => 'E_ALL',
            'max_execution_time' => '0',
            'max_input_time' => '0',
        ];
    }
}
```

> **_NOTE:_** For customize php.ini, you must implement the `ProvidesPhpIni` contract.
