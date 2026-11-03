---
title: Quick Start
order: 2
---

## Jump in

Don't waste hours downloading, installing, and configuring Xcode and Android Studio; just
[Jump](https://bifrost.nativephp.com/jump):

1. Install the Jump app on your iOS or Android device
2. Run the following commands:

### New Laravel app

The recommended way to start is our starter kit:

```bash
laravel new my-app --using=nativephp/mobile-starter --no-node

cd my-app

php artisan native:jump
```

`--no-node` skips the `npm install && npm run build` the installer runs by default, which a SuperNative app doesn't
need. `laravel new` also installs [Laravel Boost](https://laravel.com/ai/boost), which sets up AI agent guidelines for
the project; pass `--no-boost` to skip it.

The starter kit already installs and registers `nativephp/mobile-ui`.

<aside>

Running `composer create-project nativephp/mobile-starter` directly? Add `--stability=dev`, or you'll get an older
v3 release of the starter kit.

</aside>

### Existing Laravel app

If you already have a Laravel app:

```bash
composer require nativephp/mobile nativephp/mobile-ui

php artisan vendor:publish --tag=nativephp-plugins-provider
php artisan native:plugin:register nativephp/mobile-ui

php artisan native:jump
```

`nativephp/mobile-ui` provides the native UI elements (text, buttons, lists, inputs and so on). It must be registered
in `app/Providers/NativeServiceProvider.php` or your screens will render blank. See
[Installation](installation#install-the-ui-components).

Scan the QR code with Jump and you're off!

## Install & run

If you've already got your [environment set up](environment-setup) to build mobile apps using Xcode and/or Android
Studio, you can build and run your app locally:

```bash
# Install NativePHP for Mobile and its UI components into a new Laravel app
composer require nativephp/mobile nativephp/mobile-ui

# Register the UI plugin so its native code is compiled in
php artisan vendor:publish --tag=nativephp-plugins-provider
php artisan native:plugin:register nativephp/mobile-ui

# Ready your app to go native
php artisan native:install

# Run your app on a mobile device
php artisan native:run
```

#### The `native` command

When you run `native:install`, NativePHP installs a `native` script helper that can be used as a convenient wrapper to
the `native` Artisan command namespace. Once this is installed you can do the following:

```shell
# Instead of...
php artisan native:run

# Do
php native run

# Or
./native run
```

## Need help?

- **Community** - Join our [Discord](/discord) for support and discussions.
- **Examples** - Check out the Kitchen Sink demo app
  on [Android](https://play.google.com/store/apps/details?id=com.nativephp.kitchensinkapp) and
  [iOS](https://testflight.apple.com/join/vm9Qtshy)!
