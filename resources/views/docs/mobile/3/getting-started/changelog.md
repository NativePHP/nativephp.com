---
title: Changelog
order: 2
---

For changes prior to v3, see the [v2 documentation](/docs/mobile/2/getting-started/changelog).

## v3.1 — Persistent Runtime & Performance

### New Features

- **Persistent PHP Runtime** — Laravel boots once and the kernel is reused across requests, yielding ~5-30ms response times vs ~200-300ms previously.
- **ZTS (Thread-Safe) PHP** support enabling background queue workers
- **PHP Queue Worker** — a dedicated background thread runs queued Laravel jobs off the main thread on both iOS and Android. Just set `QUEUE_CONNECTION=database` and dispatch jobs as normal. See [Queues](../concepts/queues) for details.
- **Binary caching** — PHP binaries are cached in `nativephp/binaries` to avoid re-downloading on every build
- **Versions manifest** — binary URLs fetched from `versions.json` instead of being hardcoded
- **Android 8+ support** — minimum SDK lowered from Android 13 (API 33) to Android 8 (API 26), dramatically expanding device reach
- **PHP 8.3–8.5 support** — NativePHP now detects your app's PHP version from `composer.json` and matches it automatically, with PHP 8.3 as the lowest supported version
- **ICU/Intl support on iOS** — iOS now ships with full ICU support, enabling Filament and other packages that depend on the `intl` extension to work on both platforms
- **Configurable Android SDK versions** — `compile_sdk`, `min_sdk`, and `target_sdk` in your config
- **Plugin multi-register** — `native:plugin:register` discovers and registers multiple plugins in one pass
- **Unregistered plugin warnings** during `native:run`
- **`ios/i` and `android/a` flags** for the `native:jump` command

### Improvements

- Static linking on Android for better performance and reliability
- Plugin compilation during `native:package` builds
- URL encoding preserved on Android redirects
- Removed unused `react/http` and `react/socket` dependencies

### Developer Experience

- Laravel Boost skill support (shoutout Pushpak!) LINK TO PRS

## v3.0 — Plugin Architecture

- **Plugin-based architecture** — the framework is built around a modular plugin system
- **All core APIs shipped as plugins** — Camera, Biometrics, Dialog, and more are all individual plugins
- **`NativeServiceProvider`** for registering third-party plugins
- **Plugin management commands** — install, register, and manage plugins from the CLI
- **Free and open source**
