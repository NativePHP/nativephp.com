---
title: Publishing
order: 200
---

## Publishing Your App

Publishing your app is similar to building, but in addition NativePHP will upload the build artifacts to your chosen
[updater provider](/docs/publishing/updating) automatically.

## Running a build

```shell
php artisan native:publish
```

This will build for the platform and architecture where you are running the build.

**Make sure you've bumped your app version in your .env file before building**

### Cross-compilation

You can also specify a platform to build for by passing the `os` argument, so for example you could build for Windows
whilst on a Mac:

```shell
php artisan native:publish win
```

Possible options are: `mac`, `win`, `linux`.

**Cross-compilation is not supported on all platforms.**

### GitHub Releases

If you use the GitHub [updater provider](/docs/publishing/updating), you'll need to create a draft release first.

Set the "Tag version" to the value of `version` in your application `.env` file, and prefix it with v. "Release title" can be anything you want.

Whenever you run `native:publish`, your build artifacts will be attached to your draft release. If you decide to rebuild before tagging the release, it will update the artifacts attached to your draft.
