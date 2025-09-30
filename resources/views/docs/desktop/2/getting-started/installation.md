---
title: Installation
order: 100
---

## Requirements

1. PHP 8.3+
2. Laravel 11 or higher
3. Node 22+
4. Windows 10+ / macOS 12+ / Linux

### PHP & Node

The best development experience for NativePHP is to have PHP and Node running on your development machine directly.

If you're using Mac or Windows, the most painless way to get PHP and Node up and running on your system is with
[Laravel Herd](https://herd.laravel.com). It's fast and free!

Please note that, whilst it's possible to develop and run your application from a virtualized environment or container,
you may encounter more unexpected issues and have more manual steps to create working builds.

### Laravel

NativePHP is built to work best with Laravel. You can install it into an existing Laravel application, or
[start a new one](https://laravel.com/docs/installation).

## Install a NativePHP runtime

```shell
composer require nativephp/desktop
```

This package contains all the classes, commands, and interfaces that your application will need to work with the
Electron runtime.

## Run the NativePHP installer

```shell
php artisan native:install
```

The NativePHP installer takes care of publishing the NativePHP service provider, which bootstraps the necessary
dependencies for your application to work with the runtime you're using: Electron or Tauri.

It also publishes the NativePHP configuration file to `config/nativephp.php`.

It adds the `composer native:dev` script to your `composer.json`, which you are free to modify to suit your needs.

Then it installs the `php artisan native:install` script as a `post-update-cmd` so your environment is always up to date after a `composer update`.

Finally, it installs any other dependencies needed to run Electron.

**Whenever you set up NativePHP on a new machine or in CI, you should run the installer to make sure all the
necessary dependencies are in place to build your application.**

### Publishing the Electron project

If you need to make any specific adjustments to the underlying Electron app, you are free to publish it using `php artisan native:install --publish`. This will export the Electron project to `{project-root}/nativephp/electron` and allow you to fully control all of NativePHP's inner workings. 

Additionally this will modify your `post-update-cmd` script to keep your project up to date, but mind it's possible you'll need to cherry-pick any adjustments you've made after a `composer update`.

## Start the development server

**Heads up!** Before starting your app in a native context, try running it in the browser. You may bump into exceptions
which need addressing before you can run your app natively, and may be trickier to spot when doing so.

Once you're ready:

```shell
php artisan native:serve
```

And that's it! You should now see your Laravel application running in a native desktop window. 🎉
