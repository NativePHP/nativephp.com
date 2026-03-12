---
title: Installation
order: 100
---

## Install the Composer package

NativePHP contains all the libraries, classes, commands, and interfaces that your application will need to work with
iOS and Android. And it's a single command away:

```shell
composer require nativephp/mobile
```

### We love Laravel

NativePHP for Mobile is built to work with Laravel. We recommend that you install it into a
[new Laravel application](https://laravel.com/docs/installation) for your NativePHP application.

### Notes for Windows users

#### Windows Defender

Add `C:\temp`, as well as your project folder, to your Windows Defender exclusions list to significantly speed up
Composer installs during app compilation. This prevents its real-time scanning from processing the many temporary files
created during the build process, which slows the process considerably.

#### No WSL support

NativePHP does not work in WSL (Windows Subsystem for Linux). You must install and run NativePHP directly on Windows.

## Run the NativePHP installer

**Before** running the `install` command, it is important to set the following variables in your `.env`:

```dotenv
NATIVEPHP_APP_ID=com.yourcompany.yourapp
NATIVEPHP_APP_VERSION="DEBUG"
NATIVEPHP_APP_VERSION_CODE="1"
```

Find out more about these options in
[Configuration](/docs/getting-started/configuration#codenativephp-app-idcode).

<aside>

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

If you're using an Inertia starter kit (React or Vue), the installer will offer to automatically configure the
[`nativephpMobile` Vite plugin](/docs/getting-started/development#the-codenativephpmobilecode-vite-plugin) in your
`vite.config.js`. This is required for HMR and correct asset loading on iOS. You can also pass `--with-vite` to skip
the prompt.

You may be prompted about whether you would like to install the ICU-enabled PHP binaries. You should install these if
your application relies on the `intl` PHP extension.

If you don't need `intl` or are not sure, choose the default, non-ICU builds.

<aside>

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
