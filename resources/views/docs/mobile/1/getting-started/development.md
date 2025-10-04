---
title: Development
order: 300
---

Developing your NativePHP apps can be done in the browser, using workflows with which you're already familiar.

This allows you to iterate rapidly on parts like the UI and major functionality, even using your favorite tools for
testing etc.

But when you want to test _native_ features, then you must run your app on a real/emulated device.

Whether you run your native app on an emulated or real device, it will always require compilation after changes have
been made.

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

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

### Inertia on iOS

Due to the way your apps are configured to work on iOS, we need to patch the Axios package to make Inertia work.

We've tried to make this as straightforward as possible. Simply run:

```shell
php artisan native:patch-inertia
```

This will backup your current `vite.config.js` and replace it with one that 'fixes' Axios.

You will just need to copy over any specific config (plugins etc) from your old Vite config to this new one.

Once that's done, you'll need to adjust your Vite build command for when you're creating iOS builds. _Only_ for iOS
builds. (If you try to run these builds on Android they probably won't work.)

Add the `--mode=ios` to your build command. Run it before compiling your app for iOS. Here's an example using `npm`:

```shell
npm run build -- --mode=ios
```

## Compile your app

To compile and run your app, simply run:

```shell
php artisan native:run --build=debug
```

This single command takes care of everything and allows you to run new builds of your application without having to
learn any new editors or platform-specific tools.

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

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

## Hot Reloading

We've tried to make compiling your apps as fast as possible, but when coming from the 'make a change; hit refresh'-world
of PHP development that we all love, compiling apps can feel like a slow and time-consuming process.

So we've released hot reloading, which aims to make your development experience feel just like home. 

You can enable hot reloading by running the following command:

```shell
php artisan native:watch {platform:ios|android}
```

This is useful during development for quickly testing changes without re-compiling your entire app. When you make
changes to any files in your Laravel app, the web view will be reloaded and your changes should show almost immediately.

### Implementation

The proper way to implement this is to first `run` your app on your device/emulator, then start HMR with `npm run dev` then in a separate terminal run the `native:watch` command. This will reload any Blade/Livewire files as well as any recompiled assets (css/js etc).

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

#### Note

 

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
```js
// And update your vite.config.ts
server: {
    port: 5173,
    cors: true,
    hmr: {
        host: '127.0.0.1',
    },
},
```

</aside>

## Releasing

To prepare your app for release, you should set the version number to a new version number that you have not used
before and increment the build number:

```dotenv
NATIVEPHP_APP_VERSION=1.2.3
NATIVEPHP_APP_VERSION_CODE=48
```

### Versioning

You have complete freedom in how you version your applications. You may use semantic versioning, codenames,
date-based versions, or any scheme that works for your project, team or business.

Remember that your app versions are usually public-facing (e.g. in store listings and on-device settings and update
screens) and can be useful for customers to reference if they need to contact you for help and support.

The build number is managed via the `NATIVEPHP_APP_VERSION` key in your `.env`.

### Build numbers

Both the Google Play Store and Apple App Store require your app's build number to increase for each release you submit. 

The build number is managed via the `NATIVEPHP_APP_VERSION_CODE` key in your `.env`.

### Run a `release` build

Then run a release build:

```shell
php artisan native:run --build=release
```

This builds your application with various optimizations that reduce its overall size and improve its performance, such
as removing debugging code and unnecessary features (i.e. Composer dev dependencies).

**You should test this build on a real device.** Once you're happy that everything is working as intended you can then
submit it to the stores for approval and distribution.

- [Google Play Store submission guidelines](https://support.google.com/googleplay/android-developer/answer/9859152?hl=en-GB#zippy=%2Cmaximum-size-limit)
- [Apple App Store submission guidelines](https://developer.apple.com/ios/submit/)

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

#### Skip the prompts

If you are tired of prompts, you can run most commands - like `native:run` - with the arguments and options that will
allow you to skip through the various prompts. Use the `--help` flag on a command to find out what values you can 
pass directly to it:

```shell
php artisan native:run --help
```

</aside>
