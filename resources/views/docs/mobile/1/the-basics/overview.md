---
title: Overview
order: 50
---

NativePHP for Mobile is made up of multiple parts:

- A Laravel application (PHP) 
- The `nativephp/mobile` Composer package
- A custom build of PHP with custom NativePHP extension
- Native applications (Swift & Kotlin)

## Your Laravel app

You can build your Laravel application just as you normally would, for the most part, sprinkling native functionality
in where desired by using NativePHP's built-in APIs.

## `nativephp/mobile`

The package is a pretty normal Composer package. It contains the PHP code needed to interface with the NativePHP
extension, the tools to install and run your applications, and all the code for each native application - iOS and
Android.

## The PHP builds

When you run the `native:install` Artisan command, the package will fetch the appropriate versions of the custom-built
PHP binaries.

NativePHP for Mobile currently bundles **PHP 8.4**. You should ensure that your application is built to work with this
version of PHP.

These custom PHP builds have been compiled specifically to target the mobile platforms and cannot be used in other
contexts.

They are compiled as embeddable C libraries and embedded _into_ the native application. In this way, PHP doesn't run as
a separate process/service under a typical web server environment; essentially, the native application itself is
extended with the capability to execute your PHP code.

Your Laravel application is then executed directly by the native app, using the embedded PHP engine to run the code.
This runs PHP as close to natively as it can get. It is very fast and efficient on modern hardware.

## The native apps

NativePHP ships one app for iOS and one for Android. When you run the `native:run` Artisan command, your Laravel app is
packaged up and copied into one of these apps.

To build for both platforms, you must run the `native:run` command twice, targeting each platform.

Each native app "shell" runs a number of steps to prepare the environment each time your application is booted,
including:

- Checking to see if the bundled version of your Laravel app is newer than the installed version
  - Installing the newer version if necessary 
- Running migrations
- Clearing caches

Normally, this process takes just a couple of seconds in normal use. After your app has been updated, it will take a
few seconds longer.
