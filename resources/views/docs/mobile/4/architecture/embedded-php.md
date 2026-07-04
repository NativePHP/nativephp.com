---
title: Embedded PHP
order: 70
---

There's no server in a NativePHP app — and no separate PHP process either. PHP ships *inside* your app as a
library, compiled specifically for mobile devices and linked into the native application itself. This page
explains what that build is, why we produce it ourselves, and how it reaches your project.

## PHP as a library

Standard PHP is built to sit behind a web server. Mobile PHP is built with the **embed SAPI**: instead of a
`php` binary, the build produces a C library (`libphp`) that the Swift and Kotlin shells link against and call
directly. When your app boots, it initializes the PHP engine in-process, loads your Laravel app from the bundled
[app package](../getting-started/introduction), and keeps it resident as the
[persistent runtime](threading-model#lifecycle-booted-once-kept-warm).

Running in-process is what makes the rest of the architecture possible: shared memory only works when both sides
share a process. It's also simply fast — there's no FastCGI, no sockets, no per-request process to spawn.

NativePHP for Mobile currently bundles **PHP 8.4**, cross-compiled for iOS (device and simulator) and Android
(ARM64), thread-safe, and tuned for embedding.

## Built in lockstep with the framework

The renderer's [wire format](glossary#frame) has two sides: the [Element Runtime](glossary#element-runtime) that
writes it, and the native readers that decode it. The Element Runtime is a PHP **extension** — native code
compiled into `libphp` itself, alongside the engine.

That's why we build PHP ourselves, and why every release of `nativephp/mobile` pins exact binary builds: the PHP
engine, the NativePHP extension inside it, and the Swift/Kotlin readers around it are produced and shipped
**together**, from matching sources. Both sides of every boundary are always the same version — there is no
"which PHP works with which framework release" matrix to manage. The wire format's
[version handshake](cross-platform-implementation#failing-loud-not-weird) exists as a backstop, but lockstep
shipping is what makes it a formality.

The same extension also provides the [bridge functions](../plugins/bridge-functions) seam that device APIs and
plugins are built on — one native extension, both boundaries.

## What's inside

The builds include the extensions a Laravel app expects, statically compiled along with their dependencies:
bcmath, ctype, curl, dom, fileinfo, filter, intl, mbstring, openssl, pdo_sqlite, phar, session, simplexml,
sockets, sodium, sqlite3, tokenizer, xml, xmlreader, xmlwriter, zip and zlib — plus the `nativephp` extension
described above. SQLite is the bundled database, a CA certificate bundle is included so HTTPS works out of the
box, and ICU is included so localization and `intl`-powered formatting behave properly on device.

Everything is compiled from source for each platform — including OpenSSL, libxml2, SQLite and friends — so the
builds have no dependencies on whatever libraries happen to exist on a given device.

## How it reaches your app

You never build any of this yourself. When you run:

```shell
php artisan native:install
```

the framework downloads the prebuilt PHP bundles matching your `nativephp/mobile` version and places them into the
platform projects — static libraries for iOS, shared libraries for Android. `native:run` then compiles your app
with PHP inside it, exactly like any other native dependency.

<aside>

These builds are compiled specifically for mobile targets and can't be used as a general-purpose PHP — your
development machine keeps using its own PHP for `artisan`, tests and tooling. Just make sure your app code is
happy on the bundled PHP version.

</aside>
