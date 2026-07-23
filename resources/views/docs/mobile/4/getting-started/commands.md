---
title: Command Reference
order: 350
---

A complete reference of all `native:*` Artisan commands available in NativePHP Mobile.

## Development Commands

### native:install

Install NativePHP into your Laravel application.

```shell
php artisan native:install {platform?}
```

| Option | Description |
|--------|-------------|
| `platform` | Target platform: `android`, `ios`, or `both` |
| `--force` | Overwrite existing files |
| `--fresh` | Alias for `--force` |
| `--with-icu` | Include ICU support for Android (adds ~30MB) |
| `--without-icu` | Exclude ICU support for Android |
| `--skip-php` | Do not download PHP binaries |

### native:run

Build and run your app on a device or simulator.

```shell
php artisan native:run {os?} {udid?}
```

| Option | Description                                       |
|--------|---------------------------------------------------|
| `os` | Target platform: `ios/i` or `android/a`           |
| `udid` | Specific device/simulator UDID                    |
| `--build=debug` | Build type: `debug`, `release`, or `bundle`       |
| `--watch` | Enable hot reloading during development           |
| `--vite` | Start the Vite dev server for JS/CSS HMR (opt-in; off by default) |
| `--start-url=` | Initial URL/path to load (e.g., `/dashboard`)     |
| `--no-tty` | Disable TTY mode for non-interactive environments |

<aside>

Before building, `native:run` checks for unregistered plugins and warns you if any are found. You can register them
with `php artisan native:plugin:register`.

</aside>

### native:watch

Watch for file changes and sync to a running mobile app.

```shell
php artisan native:watch {platform?} {target?}
```

| Option | Description                             |
|--------|-----------------------------------------|
| `platform` | Target platform: `ios/i` or `android/a` |
| `target` | The device/simulator UDID to watch      |
| `--vite` | Start the Vite dev server for JS/CSS HMR (opt-in; off by default) |

<aside>

The Vite dev server is **opt-in** — pass `--vite` to `native:run`/`native:watch` to start it. It
was previously started automatically; the old `--no-vite` flag still exists but is now redundant. See the
[Upgrade Guide](upgrade-guide).

</aside>

### native:jump

Start the NativePHP development server for testing mobile apps without building.

```shell
php artisan native:jump
```

| Option | Description |
|--------|-------------|
| `--host=0.0.0.0` | Host address to serve on |
| `--ip=` <span class="ml-1 inline-flex items-center rounded bg-indigo-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">v3.3+</span> | IP address to display in the QR code (overrides auto-detection) |
| `--http-port=` | HTTP port to serve on (defaults to `nativephp.server.http_port`, typically `3000`) |
| `--ws-port=` <span class="ml-1 inline-flex items-center rounded bg-indigo-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">v3.3+</span> | WebSocket bridge port (defaults to `3001`) |
| `--bridge-port=` <span class="ml-1 inline-flex items-center rounded bg-indigo-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">v3.3+</span> | Internal TCP bridge port (defaults to `3002`) |
| `--vite-proxy-port=` <span class="ml-1 inline-flex items-center rounded bg-indigo-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">v3.3+</span> | Port Jump uses to proxy Vite HMR to the phone (defaults to `3003`) |
| `--no-serve` <span class="ml-1 inline-flex items-center rounded bg-indigo-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">v3.3+</span> | Do not start `artisan serve` automatically (use if running your own server) |
| `--laravel-port=` | Laravel dev server port (defaults to `8000`; auto-detected when `artisan serve` is managed) |
| `--no-mdns` | Disable mDNS service advertisement |

### native:open

Open the native project in Xcode or Android Studio.

```shell
php artisan native:open {os?}
```

| Option | Description                             |
|--------|-----------------------------------------|
| `os` | Target platform: `ios/i` or `android/a` |

### native:tail

Tail Laravel logs from a running Android app. (Android only)

```shell
php artisan native:tail
```

### native:version

Display the current NativePHP Mobile version.

```shell
php artisan native:version
```

### native:make

Create a new `NativeComponent` class and its Blade view.

```shell
php artisan native:make {name}
```

| Option | Description |
|--------|-------------|
| `name` | The component name (e.g. `Counter`, `Settings/Profile`) |
| `--force` | Overwrite if the component already exists |

### native:rm

Remove a `NativeComponent` class and its Blade view.

```shell
php artisan native:rm {name?}
```

| Option | Description |
|--------|-------------|
| `name` | The component to remove (e.g. `Counter`, `Settings/Profile`). Optional — omit to choose interactively |

### native:make-test

Create a Pest test for a `NativeComponent` screen.

```shell
php artisan native:make-test {name}
```

| Option | Description |
|--------|-------------|
| `name` | The component to test (e.g. `Counter`, `Settings/Profile`, or a FQCN) |
| `--force` | Overwrite if the test already exists |

### native:validate

Validate native EDGE components for common errors.

```shell
php artisan native:validate
```

| Option | Description |
|--------|-------------|
| `--component=` | Validate only a specific component |

### native:debug

Show debug information about your NativePHP Mobile environment.

```shell
php artisan native:debug
```

| Option | Description |
|--------|-------------|
| `--json` | Output as JSON |

### native:emulator

List and launch an emulator or simulator.

```shell
php artisan native:emulator {os}
```

| Option | Description |
|--------|-------------|
| `os` | Target platform: `android/a` or `ios/i` |

### native:sim

Inspect or manage the app on the booted iOS simulator. (macOS only)

```shell
php artisan native:sim {target?}
```

| Option | Description |
|--------|-------------|
| `target` | What to do: `data`, `app`, or `uninstall` (default: `data`) |
| `--bundle-id=` | Override the app bundle identifier |

## Building & Release Commands

<aside>

#### Skip the Complexity

Managing certificates, provisioning profiles, and keystores locally is tedious and error-prone.
[Bifrost](https://bifrost.nativephp.com) handles all of this for you — set credentials once, deploy with a single
command, and collaborate with your team effortlessly.

</aside>

### native:package

Package your app for distribution with signing.

```shell
php artisan native:package {platform}
```

| Option | Description                                       |
|--------|---------------------------------------------------|
| `platform` | Target platform: `android/a` or `ios/i`           |
| `--build-type=release` | Build type: `release` or `bundle`                 |
| `--output=` | Output directory for signed artifacts             |
| `--jump-by=` | Skip ahead in version numbering                   |
| `--no-tty` | Disable TTY mode for non-interactive environments |

**Android Options:**

| Option | Description |
|--------|-------------|
| `--keystore=` | Path to Android keystore file |
| `--keystore-password=` | Keystore password |
| `--key-alias=` | Key alias for signing |
| `--key-password=` | Key password |
| `--fcm-key=` | FCM Server Key for push notifications |
| `--google-service-key=` | Google Service Account Key file path |
| `--upload-to-play-store` | Upload to Play Store after packaging |
| `--play-store-track=internal` | Play Store track: `internal`, `alpha`, `beta`, `production` |
| `--test-push=` | Test Play Store upload with existing AAB file (skip build) |
| `--skip-prepare` | Skip prepareAndroidBuild() to preserve existing project files |

**iOS Options:**

| Option | Description |
|--------|-------------|
| `--export-method=app-store` | Export method: `app-store`, `ad-hoc`, `enterprise`, `development` |
| `--upload-to-app-store` | Upload to App Store Connect after packaging |
| `--test-upload` | Test upload existing IPA (skip build) |
| `--validate-only` | Only validate the archive without exporting |
| `--validate-profile` | Validate provisioning profile entitlements |
| `--rebuild` | Force rebuild by removing existing archive |
| `--clean-caches` | Clear Xcode and SPM caches before building |
| `--api-key=` | Path to App Store Connect API key file (.p8) |
| `--api-key-id=` | App Store Connect API key ID |
| `--api-issuer-id=` | App Store Connect API issuer ID |
| `--certificate-path=` | Path to distribution certificate (.p12/.cer) |
| `--certificate-password=` | Certificate password |
| `--provisioning-profile-path=` | Path to provisioning profile (.mobileprovision) |
| `--team-id=` | Apple Developer Team ID |

### native:release

Bump the version number in your `.env` file.

```shell
php artisan native:release {type}
```

| Option | Description |
|--------|-------------|
| `type` | Release type: `patch`, `minor`, or `major` |

### native:credentials

Generate signing credentials for iOS and Android.

```shell
php artisan native:credentials {platform?}
```

| Option | Description                                      |
|--------|--------------------------------------------------|
| `platform` | Target platform: `android/a`, `ios/i`, or `both` |
| `--reset` | Generate new keystore and PEM certificate        |

### native:check-build-number

Validate and suggest build numbers for your app.

```shell
php artisan native:check-build-number
```

## Plugin Commands

### native:plugin:create

Scaffold a new NativePHP plugin interactively.

```shell
php artisan native:plugin:create
```

### native:plugin:list

List all installed NativePHP plugins.

```shell
php artisan native:plugin:list
```

| Option | Description |
|--------|-------------|
| `--json` | Output as JSON |
| `--all` | Show all installed plugins, including unregistered |

### native:plugin:register

Register a plugin in your NativeServiceProvider. When called without arguments, discovers all unregistered plugins and lets you register them.

```shell
php artisan native:plugin:register {plugin?}
```

| Option | Description |
|--------|-------------|
| `plugin` | Package name (e.g., `vendor/plugin-name`). Optional — omit to discover unregistered plugins |
| `--remove` | Remove the plugin instead of adding it |
| `--force` | Skip conflict warnings |

### native:plugin:uninstall

Completely uninstall a plugin.

```shell
php artisan native:plugin:uninstall {plugin}
```

| Option | Description |
|--------|-------------|
| `plugin` | Package name (e.g., `vendor/plugin-name`) |
| `--force` | Skip confirmation prompts |
| `--keep-files` | Do not delete the plugin source directory |

### native:plugin:validate

Validate a plugin's structure and manifest.

```shell
php artisan native:plugin:validate {path?}
```

| Option | Description |
|--------|-------------|
| `path` | Path to a specific plugin directory |

### native:plugin:make-hook

Create lifecycle hook commands for a plugin.

```shell
php artisan native:plugin:make-hook
```

### native:plugin:boost

Create Boost AI guidelines for a plugin.

```shell
php artisan native:plugin:boost {plugin?}
```

| Option | Description |
|--------|-------------|
| `plugin` | Plugin name or path |
| `--force` | Overwrite existing guidelines |

### native:plugin:install-agent

Install AI agents for plugin development.

```shell
php artisan native:plugin:install-agent
```

| Option | Description |
|--------|-------------|
| `--force` | Overwrite existing agent files |
| `--all` | Install all agents without prompting |

### native-ui:generate-icons

Generate the `App\Icons\Ios`, `App\Icons\Android`, and `App\Icons\AndroidOutlined` enums so you can reference icons as
typed, autocompletable enum cases. Ships with the [native-ui](https://github.com/nativephp/native-ui) plugin.

```shell
php artisan native-ui:generate-icons
```

| Option | Description |
|--------|-------------|
| `--refresh-material` | Fetch the latest Material Icons catalog from Google before regenerating |
| `--output=` | Override the output directory (default: `app/Icons`) |
| `--namespace=` | Override the generated namespace (default: `App\Icons`) |
