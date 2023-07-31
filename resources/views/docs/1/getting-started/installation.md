---
title: Installation
order: 100
---

# Requirements

1. PHP 8.1
2. Laravel 10 or higher
3. NPM
4. Linux/MacOS

# Installation

```bash
composer require nativephp/electron
```

# Install Laravel

NativePHP is a Laravel Package. You can install it on an existing Laravel application, or [start a new one](https://laravel.com/docs/10.x/installation)


# Run the installer

The NativePHP installer takes care of publishing the NativePHP service provider, which takes care of bootstrapping your
native application. It also publishes the NativePHP configuration file.

```bash
php artisan native:install
```

# Start the development server

```bash
php artisan native:serve
```

And that's it! You should now see your application running in a native desktop window.
