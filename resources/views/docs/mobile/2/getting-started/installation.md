---
title: Installation
order: 100
---

## Get a license

Before you begin, you will need to [purchase a license](/mobile).

To make NativePHP for Mobile a reality has taken a lot of work and will continue to require even more. For this reason,
it's not open source, and you are not free to distribute or modify its source code.

Your license fee goes straight back into the NativePHP project and community, enabling us to:
- Develop premium features for everyone.
- Provide first-class support.
- Sponsor our dependencies.
- Donate to our contributors.
- Support community events.
- Ensure that the whole NativePHP project remains viable for a long time to come.

Thank you for supporting the project in this way! 🙏

## Install the Composer package

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

#### We love Laravel

NativePHP for Mobile is built to work with Laravel. We recommend that you create a
[new Laravel application](https://laravel.com/docs/installation) for your NativePHP application.

</aside>

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
<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

If you experience a cURL error when running this command, make sure you are running PHP 8.3+ in your CLI.

</aside>

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

#### WSL Not Supported

NativePHP does NOT work in WSL (Windows Subsystem for Linux). You must install and run NativePHP directly on Windows.

</aside>

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

#### Windows Performance Tip

Add `C:\temp` as well as the project folder to your Windows Defender exclusions list to significantly speed up Composer
installs during app compilation. This prevents its real-time scanning from processing the many temporary files created
during the build process, which slows the process considerably.

</aside>

If this is the first time you're installing the package, you will be prompted to authenticate. Your username is the
email address you used when purchasing your license. Your password is your license key.

This package contains all the libraries, classes, commands, and interfaces that your application will need to work with
iOS and Android.

## Run the NativePHP installer

**Before** running the `install` command, it is important to set the following variables in your `.env`:

```dotenv
NATIVEPHP_APP_ID=com.yourcompany.yourapp
NATIVEPHP_APP_VERSION="DEBUG"
NATIVEPHP_APP_VERSION_CODE="1"
```

Find out more about these options in
[Configuration](/docs/getting-started/configuration#codenativephp-app-idcode).

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

#### Setting your Apple Developer Team ID

It may be useful to set your development team. You can do this via your `.env` file. Your development team ID can be
found in your [Apple Developer account](https://developer.apple.com/account), under 'Membership details'.

![](/img/docs/team-id.png)

```dotenv
NATIVEPHP_DEVELOPMENT_TEAM={your team ID}
```

</aside>


```shell
php artisan native:install
```

The NativePHP installer takes care of setting up and configuring your Laravel application to work with iOS and Android.

You may be prompted about whether you would like to install the ICU-enabled PHP binaries. You should install these if
your application relies on the `intl` PHP extension.

If you don't need `intl` or are not sure, choose the default, non-ICU builds.

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

#### Planning to use Filament?

Filament requires `intl` support so you will need ICU-supported binaries.

</aside>

### The `nativephp` Directory

After running: `php artisan native:install` you’ll see a new `nativephp` directory at the root of your Laravel project
as well as a `config/nativephp.php` config file.

The `nativephp` folder contains the native application project files needed to build your app for the desired platforms.

You should not need to manually open or edit any native project files under normal circumstances. NativePHP handles
the heavy lifting for you.

**You should treat this directory as ephemeral.** When upgrading the NativePHP package, it will be necessary to run
`php artisan native:install --force`, which completely rebuilds this directory, deleting all files within.

For this reason, we also recommend you add the `nativephp` folder to your `.gitignore`.

## Start your app

**Heads up!** Before starting your app in a native context, try running it in the browser. You may bump into exceptions
which need addressing before you can run your app natively, and may be trickier to spot when doing so.

Once you're ready:

```shell
php artisan native:run
```

Just follow the prompts! This will start compiling your application and boot it on whichever device you select.

### Running on a real device

#### On iOS
If you want to run your app on a real iOS device, you need to make sure it is in
[Developer Mode](https://developer.apple.com/documentation/xcode/enabling-developer-mode-on-a-device)
and that it's been added to your Apple Developer account as
[a registered device](https://developer.apple.com/account/resources/devices/list).

#### On Android
On Android you need to [enable developer options](https://developer.android.com/studio/debug/dev-options#enable)
and have USB debugging (ADB) enabled.

And that's it! You should now see your Laravel application running as a native app! 🎉
