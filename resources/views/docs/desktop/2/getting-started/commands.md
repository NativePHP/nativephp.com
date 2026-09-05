---
title: Command Reference
order: 375
---

A complete reference of the `native:*` Artisan commands available in NativePHP Desktop.

## Development Commands

### native:install

Install NativePHP into your Laravel application.

```shell
php artisan native:install
```

| Option | Description |
|--------|-------------|
| `--force` | Overwrite existing files by default |
| `--publish` | Publish the Electron project to your project's root |
| `--installer=npm` | The package installer to use: `npm` or `yarn` |

### native:run

Start the NativePHP development server, which builds and runs your Electron app locally.

```shell
php artisan native:run
```

| Option | Description |
|--------|-------------|
| `--no-queue` | Don't start a queue worker alongside the app |
| `--no-focus` | Don't focus the app window on launch |
| `-D`, `--no-dependencies` | Skip installing npm dependencies |
| `--installer=npm` | The package installer to use: `npm` or `yarn` |

<aside>

`native:serve` still exists but is deprecated in favor of `native:run` and will be removed in a future release.

</aside>

### native:debug

Generate debug information to include when opening an issue.

```shell
php artisan native:debug {output}
```

| Option | Description |
|--------|-------------|
| `output` | Where to send the debug output: `File`, `Clipboard`, or `Console` |

### native:reset

Clear all build and dist files, useful when you want a clean slate before rebuilding.

```shell
php artisan native:reset
```

| Option | Description |
|--------|-------------|
| `--with-app-data` | Also clear the app's stored data |

## Database Commands

These mirror Laravel's own `migrate`/`db:seed`/`db:wipe` commands, scoped to the database used by your NativePHP
development environment.

| Command | Description |
|---------|-------------|
| `native:migrate` | Run the database migrations |
| `native:migrate:fresh` | Drop all tables and re-run every migration |
| `native:seed` | Seed the database — accepts the same `--class=` option as `db:seed` |
| `native:db:wipe` | Wipe the database |

## Building & Release Commands

### native:build

Build the NativePHP application for a specific operating system and architecture.

```shell
php artisan native:build {os?} {arch?}
```

| Option | Description |
|--------|-------------|
| `os` | Operating system to build for: `all`, `linux`, `mac`, or `win` |
| `arch` | Processor architecture to build for: `x64` or `arm64` |
| `--publish` | Publish the app after building |

### native:publish

Build and publish the NativePHP app for a specific operating system and architecture. This is equivalent to running
`native:build` with `--publish`.

```shell
php artisan native:publish {os?} {arch?}
```

| Option | Description |
|--------|-------------|
| `os` | Operating system to build for: `all`, `linux`, `mac`, or `win` |
| `arch` | Processor architecture to build for: `x64` or `arm64` |

<aside>

Managing signing certificates and provisioning profiles locally is tedious and error-prone.
[Bifrost](https://bifrost.nativephp.com) can build and sign for you in the cloud instead of running these commands
locally.

</aside>
