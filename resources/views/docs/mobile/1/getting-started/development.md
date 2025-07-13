---
title: Development
order: 300
---

## Building

Building your NativePHP apps can be done completely in the browser with the workflows you're already familiar with.

This allows you to iterate rapidly on parts like the UI and major functionality, even using your favorite tools for
testing etc.

But when you want to test _native_ features, then you must run it on a real/emulated device.

Whether you run your native app on an emulated or real device, it will always require compilation after changes have
been made.

We've worked incredibly hard to ensure that your apps will operate almost identically across both platforms - aside
from obvious platform differences. For the most part, you should be able to build without thinking about which platform
your app is running on, but if you do need to, you can check by using one of the following helper methods:

```php
use Native\Mobile\Facades\System;

System::isIos() // -> `true` on iOS 
System::isAndroid() // -> `true` on Android
```

To compile and run your app, simply run:

```shell
php artisan native:run --build=debug
```

This single command takes care of everything and allows you to run new builds of your application without having to
learn any new editors or platform-specific tools.

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

#### Rule of Thumb

During development, keep `NATIVEPHP_APP_VERSION=DEBUG` to always refresh the Laravel application inside the native app.

</aside>

## Working with Xcode or Android Studio

On occasion, it is useful to compile your app from inside the target platform's dedicated development tools, Android
Studio and Xcode.

If you're familiar with these tools, you can easily open the projects using the following Artisan command:

```shell
php artisan native:open
```

## Hot Reloading (Experimental)

You can enable hot reloading by adding the `--watch` flag when running the `native:run` command:

```shell
php artisan native:run --watch
```

This is useful during development for quickly testing changes without rebuilding the entire app.

### Caveats

- This feature is currently best suited for **Blade** and **Livewire** applications.
- It does **not** currently detect or sync **compiled frontend assets**, such as those built with Vite or used by
    **Inertia.js**.
- If you're working with a JavaScript-heavy stack (Vue, React, Inertia), you should continue
    [building your frontend](/docs/mobile/1/the-basics/assets) before launching the app with `native:run`.

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
