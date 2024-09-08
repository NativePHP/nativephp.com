---
title: Building
order: 100
---
# Building Your App

Building your app is the process of compiling your application into a production-ready state. When building, NativePHP
attempts to sign and notarize your application. Once signed, your app is ready to be distributed.

The build process compiles your app for one platform at a time. It compiles your application along with the
Electron/Tauri runtime into a single executable.

Once built, you can distribute your app however you prefer, but NativePHP also provides a [publish command](publishing)
that will automatically upload your build artifacts to your chosen [provider](/docs/publishing/updating) - this allows
your app to provide automatic updates.

You should build your application for each platform you intend to support and test it on each platform _before_
publishing to make sure that everything works as expected.

## Versioning

For every build you create, you should change the version of your application in your app's `config/nativephp.php` file.

This can be any format you choose, but you may find that a simple incrementing build number is the easiest to manage.

**Migrations will only run on the user's machine if the version reference is _different_ to the currently-installed version.**

You may choose to have a different version number that uses a different scheme (e.g. SemVer) that you use for user-facing
releases.

## Running a build

```shell
php artisan native:build
```

This will build for the platform and architecture where you are running the build.

### Cross-compilation

You can also specify a platform to build for by passing the `os` argument, so for example you could build for Windows
whilst on a Mac:

```shell
php artisan native:build win
```

Possible options are: `mac`, `win`, `linux`.

**Cross-compilation is not supported on all platforms.**

#### Cross-compilation on Linux

Compiling Windows binaries is possible with [wine](https://www.winehq.org/).
NSIS requires 32-bit wine when building x64 applications.

```bash
# Example installation of wine for Debian based distributions (Ubuntu)
dpkg --add-architecture i386
apt-get -y update
apt-get -y install wine32
```

## Code signing
Both macOS and Windows require your app to be signed before it can be distributed to your users.

NativePHP makes this as easy for you as it can, but each platform does have slightly different requirements.

### Windows

[See the Electron documentation](https://www.electronforge.io/guides/code-signing/code-signing-windows) for more details.

### macOS

[See the Electron documentation](https://www.electronforge.io/guides/code-signing/code-signing-macos) for more details.

To prepare for signing and notarizing, please provide the following environment variables when running `php artisan native:build`:

```dotenv
NATIVEPHP_APPLE_ID=developer@abcwidgets.com
NATIVEPHP_APPLE_ID_PASS=app-specific-password
NATIVEPHP_APPLE_TEAM_ID=8XCUU22SN2
```

These can be added to your `.env` file as they will be stripped out when your app is built.
