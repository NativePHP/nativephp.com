---
title: Quick Start
order: 2
---

## Jump in

Don't waste hours downloading, installing, and configuring Xcode and Android Studio; just
[Jump](https://bifrost.nativephp.com/jump):

1. Install the Jump app on your iOS or Android device
2. Run the following commands:

```bash
composer require nativephp/mobile

php artisan native:jump
```

Scan the QR code with Jump and you're off!

## Install & run

If you've already got your [environment set up](environment-setup) to build mobile apps using Xcode and/or Android
Studio, you can build and run your app locally:

```bash
# Install NativePHP for Mobile into a new Laravel app
composer require nativephp/mobile

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
