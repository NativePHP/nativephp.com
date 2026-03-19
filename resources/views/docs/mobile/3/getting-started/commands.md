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

| Option | Description |
|--------|-------------|
| `os` | Target platform: `ios` or `android` |
| `udid` | Specific device/simulator UDID |
| `--build=debug` | Build type: `debug`, `release`, or `bundle` |
| `--watch` | Enable hot reloading during development |
| `--start-url=` | Initial URL/path to load (e.g., `/dashboard`) |
| `--no-tty` | Disable TTY mode for non-interactive environments |

### native:watch

Watch for file changes and sync to a running mobile app.

```shell
php artisan native:watch {platform?} {target?}
```

| Option | Description |
|--------|-------------|
| `platform` | Target platform: `ios` or `android` |
| `target` | The device/simulator UDID to watch |

### native:jump

Start the NativePHP development server for testing mobile apps without building.

```shell
php artisan native:jump
```

| Option | Description |
|--------|-------------|
| `--platform=` | Target platform: `android` or `ios` |
| `--host=0.0.0.0` | Host address to serve on |
| `--http-port=` | HTTP port to serve on |
| `--laravel-port=8000` | Laravel dev server port to proxy to |
| `--no-mdns` | Disable mDNS service advertisement |
| `--skip-build` | Skip building if `app.zip` exists |

### native:open

Open the native project in Xcode or Android Studio.

```shell
php artisan native:open {os?}
```

| Option | Description |
|--------|-------------|
| `os` | Target platform: `ios` or `android` |

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

| Option | Description |
|--------|-------------|
| `platform` | Target platform: `android` or `ios` |
| `--build-type=release` | Build type: `release` or `bundle` |
| `--output=` | Output directory for signed artifacts |
| `--jump-by=` | Skip ahead in version numbering |
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

| Option | Description |
|--------|-------------|
| `platform` | Target platform: `android`, `ios`, or `both` |
| `--reset` | Generate new keystore and PEM certificate |

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

Register a plugin in your NativeServiceProvider.

```shell
php artisan native:plugin:register {plugin}
```

| Option | Description |
|--------|-------------|
| `plugin` | Package name (e.g., `vendor/plugin-name`) |
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
