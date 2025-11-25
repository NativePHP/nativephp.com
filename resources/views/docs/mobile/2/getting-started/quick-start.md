---
title: Quick Start
order: 2
---

## Let's go!

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-gradient-to-tl from-transparent to-violet-100/75 px-5 ring-1 ring-black/5 dark:from-slate-900/30 dark:to-indigo-900/35">

#### License Required!

Make sure you have an [active license](/mobile) before you install.

</aside>

If you've already got your [environment set up](environment-setup) to build mobile apps using Xcode and/or Android
Studio, then you can get building your first mobile app with NativePHP in minutes:

### 1. Update your `composer.json`
Add the NativePHP Composer repository:

```json
"repositories": [
    {
        "type": "composer",
        "url": "https://nativephp.composer.sh"
    }
]
```

#### Running Composer 2.9+?

If you're running Composer 2.9 or above, you can just use a single command, instead of copy-pasting the above:

```shell
composer repo add nativephp composer https://nativephp.composer.sh
```

### 2. Set your app's identifier
You must set a `NATIVEPHP_APP_ID` in your `.env` file:

```dotenv
NATIVEPHP_APP_ID=com.cocacola.cokezero
```

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-gradient-to-tl from-transparent to-violet-100/75 px-5 ring-1 ring-black/5 dark:from-slate-900/30 dark:to-indigo-900/35">

#### App ID Rules

Use only lowercase letters, numbers, and periods. Special characters (like hyphens, underscores, spaces or emoji) will
cause the build to fail.

</aside>

### 3. Install & run

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
