---
title: PHP Binaries
order: 600
---
# Static PHP

At the heart of NativePHP are the platform-specific, single-file PHP binaries, which are portable, statically-compiled
versions of PHP.

These allow us to ship PHP to a user's device without forcing them to compile from source or manage a sprawling set of
dynamic libraries and configuration files.

It also means that your applications can each use an isolated version of PHP without depending on or interfering with
the version of PHP the user may already have installed on their machine or in another NativePHP app.

The binaries that ship with NativePHP are built to have a _minimal_ set of the most common PHP extensions required to
run almost any web application you can build with Laravel.

One key goal of NativePHP is to maintain feature parity across platforms so that you can reliably distribute your apps
to users on any device. This means that we will only ship PHP with extensions that can be supported across Windows,
macOS and Linux.

On top of this, fewer PHP extensions means a smaller application size and attack surface. Beware that installing more
extensions has both performance & [security](security) implications for your apps.

The extensions that are included in the default binaries are defined in the 
[`php-extensions.txt`](https://github.com/NativePHP/php-bin/blob/main/php-extensions.txt) in the `php-bin` repo.

If you think an extension is missing that would make sense as a default extension, feel free to
[make a feature request](https://github.com/nativephp/laravel/issues/new/choose) for it.

## Building custom binaries

NativePHP uses the awesome [`static-php-cli`](https://static-php.dev/) library to build distributable PHP binaries.

You may use this too to build your own binaries. Of course, you may build static binaries however you prefer.

Whichever method you use, you should aim to create a single-file executable that has statically linked all of its
dependencies for each platform and architecture that you wish your app to run on.

### Building apps with custom binaries

In order to use your custom binaries, you will need to instruct NativePHP where to find them.

To do this, you may use the `NATIVEPHP_PHP_BINARY_PATH` environment variable. You can set this in your `.env` file.
For example, if you store the binaries in a `bin` folder in the root of your application:

```dotenv
NATIVEPHP_PHP_BINARY_PATH=/path/to/your-nativephp-app/bin/
```

The binaries you are using need to be stored in a structure that mirrors the folder structure found in the `php-bin`
package:

![PHP binary folder structure](/img/docs/php-binaries.png)

Note how the platform is the first folder (`linux`, `mac`, `win`) and the architecture is provided as a subfolder
(`x64`, `arm64`, `x86`).

You do not need to build binaries for every PHP version or every platform; You only need binaries for the platforms you
wish to support and for the version of PHP that your application requires.

Make sure the binaries are named `php` (macOS/Linux) or `php.exe` (Windows) and zipped and named like so:

```shell
// macoS / Linux
zip php-[PHP_MAJOR_VERSION].[PHP_MINOR_VERSION].zip php

// Windows
powershell Compress-Archive -Path "php.exe" -DestinationPath "php-[PHP_MAJOR_VERSION].[PHP_MINOR_VERSION].zip"
```

NativePHP will then build your application using the relevant binaries found in this custom location.

## A note on safety & support

When using custom binaries, you should make every reasonable effort to secure your build pipeline so as not to allow an
attacker to introduce vulnerabilities into your PHP executables.

Further, any apps that use custom binaries will not be eligible for support via GitHub Issues.
