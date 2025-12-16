---
title: Development
order: 250
---

Developing your NativePHP apps can be done in the browser, using workflows with which you're already familiar.

This allows you to iterate rapidly on parts like the UI and major functionality, even using your favorite tools for
testing etc.

But when you want to test _native_ features, then you must run your app on a real or emulated device.

Whether you run your native app on an emulated or real device, it will require compilation after changes have been made.

<aside>

#### Platforms

We've worked incredibly hard to ensure that your apps will operate almost identically across both platforms - aside
from obvious platform differences. For the most part, you should be able to build without thinking about which platform
your app is running on, but if you do need to, you can check by using one of the following helper methods:

```php
use Native\Mobile\Facades\System;

System::isIos() // -> `true` on iOS 
System::isAndroid() // -> `true` on Android
```

</aside>

## Build your frontend

If you're using Vite or similar tooling to build any part of your UI (e.g. for React/Vue, Tailwind etc), you'll need
to run your asset build command _before_ compiling your app.

To facilitate ease of development, you should install the `nativephpMobile` Vite plugin.

### The `nativephpMobile` Vite plugin

To make your frontend build process works well with NativePHP, simply add the `nativephpMobile` plugin to your
`vite.config.js`:

```js
import { nativephpMobile, nativephpHotFile } from './vendor/nativephp/mobile/resources/js/vite-plugin.js'; // [tl! focus]

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            hotFile: nativephpHotFile(), // [tl! focus]
        }),
        tailwindcss(),
        nativephpMobile(), // [tl! focus]
    ]
});
```

Once that's done, you'll need to adjust your Vite build command when creating builds for each platform — simply add the
`--mode=[ios|android]` option. Run these before compiling your app for each platform in turn:

```shell
npm run build -- --mode=ios

npm run build -- --mode=android
```

## Compile your app

To compile and run your app, simply run:

```shell
php artisan native:run
```

This single command takes care of everything and allows you to run new builds of your application without having to
learn any new editors or platform-specific tools.

<aside>

#### Rule of thumb

During development, keep `NATIVEPHP_APP_VERSION=DEBUG` to always refresh the Laravel application inside the native app.
Your app will boot a little slower, but you will find that everything looks as you expect.

It's better than scratching your head for an hour trying to figure out why your changes aren't showing!

</aside>

## Working with Xcode or Android Studio

On occasion, it is useful to compile your app from inside the target platform's dedicated development tools, Android
Studio and Xcode.

If you're familiar with these tools, you can easily open the projects using the following Artisan command:

```shell
php artisan native:open
```

### Configuration

You can configure the folders that the `watch` command pays attention to in your `config/nativephp.php` file:

```php
'hot_reload' => [
    'watch_paths' => [
        'app',
        'routes',
        'config',
        'database',
        // Make sure "public" is listed in your config [tl! highlight:1]
        'public',  
    ],
]
```

<aside>

#### Skip the prompts

If you are tired of prompts, you can run most commands - like `native:run` - with arguments and options that allow you
to skip various prompts. Use the `--help` flag on a command to find out what values you can pass directly to it:

```shell
php artisan native:run --help
```

</aside>


## Hot Reloading

We've tried to make compiling your apps as fast as possible, but when coming from the 'make a change; hit refresh'-world
of typical browser-based PHP development that we all love, compiling apps can feel like a slow and time-consuming
process.

Hot reloading aims to make your app development experience feel just like home.

You can start hot reloading by running the following command:

```shell
php artisan native:watch
```

**Note:** When testing on a real device, hot reloading will reach back to the Vite server running on your development machine so you will need to ensure the Wi-Fi on the mobile device is connected to the same network as your development machine.

<aside>

#### 🔥 Hot Tip!

You can also pass the `--watch` option to the `native:run` command. This will build and deploy a fresh version of your
application to the target device and _then_ start the watcher, all in one go.

</aside>

This will start a long-lived process that watches your application's source files for changes, pushing them into the
emulator after any updates and reloading the current screen.

If you're using Vite, we'll also use your Node CLI tool of choice (`npm`, `bun`, `pnpm`, or `yarn`) to run Vite's HMR
server.

### Enabling HMR

To make HMR work, you'll need to add the `hot` file helper to your `laravel` plugin's config in your `vite.config.js`:

```js
import { nativephpMobile, nativephpHotFile } from './vendor/nativephp/mobile/resources/js/vite-plugin.js'; // [tl! focus]

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            hotFile: nativephpHotFile(), // [tl! focus]
        }),
        tailwindcss(),
        nativephpMobile(),
    ]
});
```

<aside>

#### Two at a time, baby!

If you're developing on macOS, you can run both Android and iOS watchers at the same time in separate terminals:

```shell
# Terminal 1
php artisan native:watch ios

# Terminal 2
php artisan native:watch android
```

This way you can see your changes reflected in real-time on both platforms **at the same time**. Wild.

</aside>

This is useful during development for quickly testing changes without re-compiling your entire app. When you make
changes to any files in your Laravel app, the web view will be reloaded and your changes should show almost immediately.

Vite HMR is perfect for apps that use SPA frameworks like Vue or React to build the UI. It even works on real devices,
not just simulators! As long as the device is on the same network as the development machine.

**Don't forget to add `public/ios-hot` and `public/android-hot` to your `.gitignore` file!**

<aside>

#### Real iOS Devices Support

Full hot reloading support works best on simulators. Full hot reloading support for non-JS changes on real iOS devices
is not yet available.

</aside>

## Laravel Boost

NativePHP for Mobile supports [Laravel Boost](https://github.com/laravel/boost) which aims to accelerate AI-assisted development by providing
the essential context and structure that AI needs to generate high-quality, Laravel-specific code.

After installing `nativephp/mobile` and `laravel/boost` simply run `php artisan boost:install` and follow the prompts
to activate NativePHP for Laravel Boost!
