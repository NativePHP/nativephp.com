---
title: Publishing
order: 200
---
# Publishing Your App

Publishing your app is similar to building, but in addition NativePHP will upload the build artifacts to your chosen
[updater provider](/docs/publishing/updating) automatically.

## Running a build

```shell
php artisan native:publish
```

This will build for the platform and architecture where you are running the build.

### Cross-compilation

You can also specify a platform to build for by passing the `os` argument, so for example you could build for Windows
whilst on a Mac:

```shell
php artisan native:publish windows
```

Possible options are: `mac`, `win`, `linux`.

**Cross-compilation is not supported on all platforms.**
