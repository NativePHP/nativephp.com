---
title: Quick Start
order: 2
---

## Let's go!

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

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

### 2. Set your app's identifier
You must set a `NATIVEPHP_APP_ID` in your `.env` file:

```dotenv
NATIVEPHP_APP_ID=com.cocacola.cokezero
```

### 3. Install & run

```bash
# Install NativePHP for Mobile into a new Laravel app
composer require nativephp/mobile

# Ready your app to go native
php artisan native:install

# Run your app on a mobile device
php artisan native:run
```

## Need help?

- **Community** - Join our [Discord](/discord) for support and discussions.
- **Examples** - Check out the Kitchen Sink demo app
    on [Android](https://play.google.com/store/apps/details?id=com.nativephp.kitchensinkapp) and
    [iOS](https://testflight.apple.com/join/vm9Qtshy)!
