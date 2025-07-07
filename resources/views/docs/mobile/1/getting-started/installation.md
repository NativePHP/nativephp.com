---
title: Installation
order: 150
---

## Requirements

1. PHP 8.3+
2. Laravel 10 or higher
3. [A NativePHP for Mobile license](https://nativephp.com/mobile)

For platform-specific development environment setup (iOS and Android), see the [Environment Setup](/docs/mobile/1/getting-started/environment-setup) page.

## Laravel

NativePHP for Mobile is built to work with Laravel. You can install it into an existing Laravel application, or
[start a new one](https://laravel.com/docs/installation). The most painless way to get PHP up and running on Mac and Windows is with
[Laravel Herd](https://herd.laravel.com). It's fast and free!


## Install NativePHP for Mobile

To make NativePHP for Mobile a reality has taken a lot of work and will continue to require even more. For this reason,
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
*If you experience a cURL error when running this command make sure you are running PHP v8.3+ in your CLI.*

**Windows Performance Tip:** Add `C:\temp` to your Windows Defender exclusions list to significantly speed up composer installs during app compilation. This prevents real-time scanning from slowing down the many temporary files created during the build process.

If this is the first time you're installing the package, you will be prompted to authenticate. Your username is the
email address you used when purchasing your license. Your password is your license key.

This package contains all the libraries, classes, commands, and interfaces that your application will need to work with
iOS and Android.

**Before** running the `install` command it is important to set the following variables in your `.env`:

```shell
NATIVEPHP_APP_ID=com.yourcompany.yourapp
NATIVEPHP_APP_VERSION="DEBUG"
NATIVEPHP_APP_VERSION_CODE="1"
```

**Important: the NATIVEPHP_APP_ID must not contain any special characters or spaces**

## Run the NativePHP installer

```shell
php artisan native:install
```

The NativePHP installer takes care of setting up and configuring your Laravel application to work with iOS and Android.

## ICU Support (Android Only)

If you are wanting to run [Filament](https://filamentphp.com) or use some of the Number or I18n methods within Laravel you will need to install NativePHP with the optional ICU supported binaries, more on that [here](/docs/mobile/1/the-basics/icu-support). 

## Start your app

**Heads up!** Before starting your app in a native context, try running it in the browser. You may bump into exceptions
which need addressing before you can run your app natively, and may be trickier to spot when doing so.

Once you're ready:

```shell
php artisan native:run
```

This will start compiling your application and boot it on whichever device you select.

And that's it! You should now see your Laravel application running as a native app! 🎉

For information about running on real devices, see the [Environment Setup](/docs/mobile/1/getting-started/environment-setup) page.
