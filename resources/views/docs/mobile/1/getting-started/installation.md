---
title: Installation
order: 100
---

## Requirements

Right now, NativePHP for mobile only supports building iOS applications. Android is in the works already and coming soon!

Apple's tooling for building iOS apps requires that you compile your applications using macOS.

1. PHP 8.3+
2. Laravel 10 or higher
3. An Apple Silicon Mac running macOS 12+ with Xcode 16+
4. An active [Apple Developer account](https://developer.apple.com/)
5. [A NativePHP for mobile license](https://checkout.anystack.sh/nativephp)
6. _Optional_ iOS device

You don't _need_ a physical iOS device to compile your application and test it for iOS, as NativePHP for mobile supports
the iOS Simulator. However, we highly recommend that you test your application on a real device before submitting to the
App Store.

You can download Xcode from the Mac App Store.

The most painless way to get PHP and Node up and running on your system is with
[Laravel Herd](https://herd.laravel.com). It's fast and free!

### Laravel

NativePHP for mobile is built to work best with Laravel. You can install it into an existing Laravel application, or
[start a new one](https://laravel.com/docs/installation).

## Private package

To make NativePHP for mobile a reality has taken a lot of work and will continue to require even more. For this reason,
it's not open source and you are not free to distribute or modify its source code.

Before you begin, you will need to purchase a license.
Licenses can be obtained via [Anystack](https://checkout.anystack.sh/nativephp).

Once you have your license, you will need to add the following to your `composer.json`:

```json
"repositories": [
    {
        "type": "composer",
        "url": "https://nativephp.composer.sh"
    }
],
```

## Install NativePHP for mobile

```shell
composer require nativephp/ios
```
This package contains all the libraries, classes, commands, and interfaces that your application will need to work with
iOS.

If this is the first time you're installing the package, you will be prompted to authenticate.

Your username is the email address you registered with Anystack. 

Your password is your license key.

## Run the NativePHP installer

```shell
php artisan native:install
```

The NativePHP installer works similarly to NativePHP for desktop, taking care of setting up and configuring your Laravel
application to work with iOS.

After you've run this command, you'll see a new `ios` folder in the root of your Laravel project.

We'll come back to this later.

## Start your app

**Heads up!** Before starting your app in a native context, try running it in the browser. You may bump into exceptions
which need addressing before you can run your app natively, and may be trickier to spot when doing so.

Once you're ready:

```shell
php artisan native:run
```

This will start compiling your application and boot it in the iOS Simulator by default.

### Running on a real device

If you want to run your app on a real iOS device, you need to make sure the device is in
[Developer Mode](https://developer.apple.com/documentation/xcode/enabling-developer-mode-on-a-device) and that it's
been added to your Apple Developer account as
[a registered device](https://developer.apple.com/account/resources/devices/list).

Then you can simply run and choose your device from the list of available devices:

```shell
php artisan native:run
```

Alternatively, you may open the `ios/NativePHP.xcodeproj` file in Xcode and run builds using Xcode's UI.

And that's it! You should now see your Laravel application running as a native app! 🎉
