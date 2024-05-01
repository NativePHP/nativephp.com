---
title: Installation
order: 100
---

# Requirements

1. PHP 8.1+
2. Laravel 10 or higher
3. Node 20+
4. Windows 10+ / macOS 12+ / Linux

## PHP & Node

The best development experience for NativePHP is to have PHP and Node running on your development machine directly.

If you're using Mac or Windows, the most painless way to get PHP and Node up and running on your system is with
[Laravel Herd](https://herd.laravel.com). It's fast and free!

Please note that, whilst it's possible to develop and run your application from a virtualized environment or container,
you may encounter more unexpected issues and have more manual steps to create working builds.

## Laravel

NativePHP is built to work best with Laravel. You can install it into an existing Laravel application, or
[start a new one](https://laravel.com/docs/10.x/installation).

## Install a NativePHP runtime

```bash
composer require nativephp/electron
```

The Tauri runtime is coming soon.

## Run the NativePHP installer

```bash
php artisan native:install
```

The NativePHP installer takes care of publishing the NativePHP service provider, which bootstraps the necessary
dependencies for your application to work with the runtime you're using: Electron or Tauri. It also publishes the
NativePHP configuration file to `config/nativephp.php`.

Finally, it installs any other dependencies needed for the specific runtime you're using, e.g. for Electron it installs
the NPM dependencies.

**Whenever you set up NativePHP on a new machine or in CI, you should run the installer to make sure all of the
necessary dependencies are in place to build your application.**

## Start the development server

```bash
php artisan native:serve
```

This spins up a development build of your application for local testing.

And that's it! You should now see your Laravel application running in a native desktop window.
