---
title: Installation
order: 100
---

## Requirements

1. PHP 8.3+
2. Laravel 10 or higher
3. An Apple Silicon Mac running macOS 12+ with Xcode 16+
4. An active [Apple Developer account](https://developer.apple.com/)
5. [A NativePHP for mobile license](https://nativephp.com/mobile)
6. _Optional_ iOS device


#### For iOS
1. An Apple Mac (ideally Silicon) running macOS 12+ with Xcode 16+ 
2. An active [Apple Developer account](https://developer.apple.com/)
3. You can download Xcode from the Mac App Store

#### For Android
1. [Android Studio Giraffe (or later)](https://developer.android.com/studio)
2. The following environment variables set.
3. You should be able to successfully run `java -v` and `adb devices` from the terminal.
4. **Windows only**: You must have [7zip](https://www.7-zip.org/) installed.

#### For macOS
```shell
export JAVA_HOME=$(/usr/libexec/java_home -v 17) // This isn't required if JAVA_HOME is already set in your environment variables (check using `printenv | grep JAVA_HOME`)
export ANDROID_SDK_ROOT=$HOME/Library/Android/sdk
export PATH=$PATH:$JAVA_HOME/bin:$ANDROID_SDK_ROOT/emulator:$ANDROID_SDK_ROOT/tools:$ANDROID_SDK_ROOT/tools/bin:$ANDROID_SDK_ROOT/platform-tools
```

#### For Windows
```shell
set ANDROID_SDK_ROOT=C:\Users\yourname\AppData\Local\Android\Sdk
set PATH=%PATH%;%JAVA_HOME%\bin;%ANDROID_SDK_ROOT%\platform-tools

# This isn't required if JAVA_HOME is already set in the Windows Env Variables
set JAVA_HOME=C:\Program Files\Microsoft\jdk-17.0.8.7-hotspot
```

> **Note** You cannot build iOS apps on Windows or Linux

You don't _need_ a physical iOS/Android device to compile your application and test it for your app, as NativePHP for mobile supports
the iOS Simulator and Android emulators. However, we highly recommend that you test your application on a real device before submitting to the
App/Google Play Store.

## Laravel

NativePHP for mobile is built to work with Laravel. You can install it into an existing Laravel application, or
[start a new one](https://laravel.com/docs/installation). The most painless way to get PHP and Node up and running on your system is with
[Laravel Herd](https://herd.laravel.com). It's fast and free!


## Install NativePHP for mobile

To make NativePHP for mobile a reality has taken a lot of work and will continue to require even more. For this reason,
it's not open source, and you are not free to distribute or modify its source code.

Before you begin, you will need to purchase a license.
Licenses can be obtained [here](https://nativephp.com/mobile).

Once you have your license, you will need to add the following to your `composer.json`:

```json
"repositories": [
    {
        "type": "composer",
        "url": "https://nativephp.composer.sh"
    }
],
```

Then run:
```shell
composer require nativephp/mobile
```

If this is the first time you're installing the package, you will be prompted to authenticate. Your username is the
email address you used when purchasing your license. Your password is your license key.

This package contains all the libraries, classes, commands, and interfaces that your application will need to work with
iOS and Android.

**Before** running the `install` command it is important to set the following variables in your `.env`:

```shell
NATIVEPHP_APP_ID=com.nativephp.yourapp
NATIVEPHP_APP_VERSION="DEBUG"
NATIVEPHP_APP_VERSION_CODE="1"
```

## Run the NativePHP installer

```shell
php artisan native:install
```

The NativePHP installer works similarly to NativePHP for desktop, taking care of setting up and configuring your Laravel
application to work with iOS and/or Android.

## Start your app

**Heads up!** Before starting your app in a native context, try running it in the browser. You may bump into exceptions
which need addressing before you can run your app natively, and may be trickier to spot when doing so.

Once you're ready:

```shell
php artisan native:run
```

This will start compiling your application and boot it on whichever device you select.

### Running on a real device

#### For iOS
If you want to run your app on a real mobile device, you need to make sure the device is in
[Developer Mode](https://developer.apple.com/documentation/xcode/enabling-developer-mode-on-a-device)
and that it's been added to your Apple Developer account as
[a registered device](https://developer.apple.com/account/resources/devices/list).

#### For Android
On Android you need to [enable developer options](https://developer.android.com/studio/debug/dev-options#enable)
and have USB debugging (ADB) enabled.

And that's it! You should now see your Laravel application running as a native app! 🎉
