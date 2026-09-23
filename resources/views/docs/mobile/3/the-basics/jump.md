---
title: Jump
order: 55
---

[Jump](https://bifrost.nativephp.com/jump) is a companion mobile app that lets you preview your NativePHP app on a real
device without ever opening Xcode or Android Studio. You write code on your machine; Jump renders it on your phone.

Combined with the `native:jump` Artisan command, it gives you a fast, hot-reloading development loop that works on both
iOS and Android with a single command.

## How it works

When you run `native:jump`, NativePHP starts three things on your machine:

- **A PHP dev server** that serves a QR code page and proxies HTTP requests through to your Laravel app
- **An `artisan serve` process** running your Laravel application (unless you opt out — see [Bring your own server](#bring-your-own-server))
- **A WebSocket bridge** that the Jump app on your phone connects to, used to invoke native APIs and to deliver Vite
  HMR updates

When you scan the QR code with the Jump app, your phone connects back to your machine over Wi-Fi and starts rendering
your Laravel app inside Jump's WebView. Calls to NativePHP APIs (e.g. `Camera::open()`) are sent over the bridge to the
device, executed natively, and the result is sent back to PHP — exactly as it would behave in a packaged build.

## Quick start

1. Install Jump on your device from the
   [App Store](https://apps.apple.com/us/app/bifrost-jump/id6757173334) or
   [Google Play](https://play.google.com/store/apps/details?id=com.bifrosttech.jump). You'll need **Jump v2 or
   later** to work with NativePHP Mobile v3.3.
2. Make sure your phone and your computer are on the same Wi-Fi network.
3. From your Laravel project, run:

```shell
php artisan native:jump
```

4. Scan the QR code shown in your terminal with the Jump app.

Your app will load on your device. Any changes you make to Blade views, Livewire components or your CSS/JS bundle will
reload automatically.

<aside>

#### New to NativePHP?

If you're just starting out, the [Quick Start](/docs/mobile/3/getting-started/quick-start) walks you through installing
NativePHP and running Jump for the first time.

</aside>

## Network requirements

Jump connects from your phone to your computer over your local network. For this to work:

- Both devices need to be on the **same Wi-Fi network** (or one device tethered to the other)
- Your firewall must allow inbound connections to the HTTP, WebSocket, bridge and Vite proxy ports (defaults: `3000`,
  `3001`, `3002`, `3003`)
- If you're on a public/guest network that isolates clients from each other, Jump won't be able to connect — switch to a
  private network or use a personal hotspot

NativePHP also advertises the dev server via mDNS so the Jump app can discover it automatically. If your network blocks
multicast traffic, you can disable this with `--no-mdns` and scan the QR code instead.

## Hot reload

Jump uses Vite's HMR pipeline. When you run `npm run dev` alongside `native:jump`, the dev server's HMR WebSocket is
proxied through Jump's own port (`3003` by default) so your phone can subscribe to updates without you having to
reconfigure `vite.config.js` for network access.

You don't need to do anything special — just start Vite the way you normally would and Jump will pick it up.

## The Bridge

In a normal mobile build, calls like `Camera::open()` execute in-process on the device. With Jump, PHP runs on your
machine but the native APIs still live on the phone. NativePHP makes this transparent: when Jump is active, the
`nativephp_call()` function dials the bridge port over TCP, the WebSocket server relays the call to the connected
device, and the device replies with the result.

This means you can develop and test the vast majority of native functionality — sensors, dialogs, camera, biometrics,
file pickers and more — without ever building the app.

<aside>

A few APIs that depend on long-running state on the device (e.g. background tasks) behave best in a packaged build. For
day-to-day development everything else "just works".

</aside>

### Bridge logs

The bridge writes to `storage/logs/jump-bridge.log`. You can tail it to watch native calls in real time:

```shell
tail -f storage/logs/jump-bridge.log
```

The path is also printed under **Bridge log** in the `native:jump` terminal output.

## Configuration

The dev server is configured under the `server` key in your `config/nativephp.php` file:

```php
'server' => [
    'http_port' => env('NATIVEPHP_HTTP_PORT', 3000),
    'ws_port' => env('NATIVEPHP_WS_PORT', 8081),
    'service_name' => env('NATIVEPHP_SERVICE_NAME', 'NativePHP Server'),
    'open_browser' => env('NATIVEPHP_OPEN_BROWSER', true),
],
```

See [Configuration](/docs/mobile/3/getting-started/configuration#development-server) for the full reference.

Per-run options (ports, host, IP, mDNS) can all be overridden on the command line — see the
[`native:jump` command reference](/docs/mobile/3/getting-started/commands#nativejump) for the full list of flags.

### Multiple network interfaces

If your machine has more than one IP address (e.g. Wi-Fi and Ethernet, or a VPN is active), `native:jump` will prompt
you to choose which one to advertise in the QR code. Pick the IP that your phone can actually reach — usually the Wi-Fi
one.

You can also pre-select it with `--ip=`:

```shell
php artisan native:jump --ip=192.168.1.42
```

### Bring your own server

By default `native:jump` starts an `artisan serve` for you. If you're already running your own server (for example
[Herd](https://herd.laravel.com), Valet, Sail, or `php artisan serve` in another tab), pass `--no-serve` and tell Jump
which port your server is on:

```shell
php artisan native:jump --no-serve --laravel-port=8000
```

You'll also need to export the bridge port when starting your own server, so that `nativephp_call()` inside Laravel
dials the right TCP port. `native:jump` prints the exact command to copy:

```shell
JUMP_BRIDGE_PORT=3002 php artisan serve --port=8000
```

### Custom ports

If something else on your machine is already using a port, NativePHP will automatically find the next available one and
print a message. If you want to pin the ports yourself, all of them can be set explicitly:

```shell
php artisan native:jump \
    --http-port=3000 \
    --ws-port=3001 \
    --bridge-port=3002 \
    --vite-proxy-port=3003 \
    --laravel-port=8000
```

## Stopping the server

Press `Ctrl+C` in the terminal where `native:jump` is running. NativePHP will shut down the bridge, the PHP server and
the managed `artisan serve` process cleanly.

## When to use Jump vs `native:run`

| Use Jump when... | Use `native:run` when... |
|------------------|--------------------------|
| You want the fastest possible iteration loop | You need to test a packaged build |
| You don't have Xcode or Android Studio installed | You're testing release/bundle builds for store submission |
| You're _not_ working on a Mac or don't have an Apple Developer account but want to test on a real iOS device | You're testing native code in plugins you're authoring |
| You want to share a preview with a teammate over the same network | You need to test app start-up behaviour or bundled assets |

For most day-to-day development, Jump is the fastest way to see changes on a real device. Once you're ready to ship,
use [`native:package`](/docs/mobile/3/getting-started/commands#nativepackage) to build a signed binary.
