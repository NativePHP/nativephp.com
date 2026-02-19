---
title: Best Practices
order: 900
---

## Overview

Building a plugin that works is only the first step. Building one that's easy to install, well-documented, and
tested across platforms and frontend stacks is what makes a plugin worth publishing.

This page covers the standards we expect from plugins listed on the
[NativePHP Plugin Marketplace](https://nativephp.com/plugins) and what makes the difference between a plugin
developers trust and one they abandon after 10 minutes.

## Documentation

Every plugin must ship with a comprehensive README. This is the first thing developers see and it determines
whether they'll bother installing your plugin at all.

### Required README Sections

Your README should include all of the following:

**Installation:**

```markdown
## Installation

composer require vendor/my-plugin

php artisan native:plugin:register vendor/my-plugin
```

**PHP usage with complete examples:**

```markdown
## Usage (PHP)

use Vendor\MyPlugin\Facades\MyPlugin;

// Basic usage
$result = MyPlugin::doSomething(['option' => 'value']);

// Listening for events in Livewire
#[OnNative(SomethingCompleted::class)]
public function handleResult($data)
{
    $this->result = $data['result'];
}
```

**JavaScript usage for SPA frameworks:**

```markdown
## Usage (JavaScript)

import { DoSomething } from 'vendor-my-plugin';

// In Vue/React components
const result = await DoSomething({ option: 'value' });
```

**Available methods, events, and required permissions** — document every public method your facade exposes,
every event your plugin dispatches, and every permission it requires. Don't make developers read your source
code to figure out what your plugin does.

**Environment variables and secrets** — if your plugin requires API keys or tokens, document exactly where to
get them and how to configure them.

### Keep Documentation Current

Update your README whenever you change your plugin's API. Outdated documentation is worse than no documentation — it
actively misleads developers and wastes their time. If a method signature changes, update the README in the same
commit.

## JavaScript Implementations

Every plugin must provide a JavaScript library alongside the PHP facade. Many NativePHP apps use Inertia with
Vue or React, and those developers need to call your native functions directly from their components without
going through Livewire.

Your `resources/js/` directory should export clean, documented functions:

```js
// resources/js/index.js
const baseUrl = '/_native/api/call';

async function bridgeCall(method, params = {}) {
    const response = await fetch(baseUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ method, params })
    });
    return response.json();
}

export async function DoSomething(options = {}) {
    return bridgeCall('MyPlugin.DoSomething', options);
}

export async function DoSomethingElse(id, options = {}) {
    return bridgeCall('MyPlugin.DoSomethingElse', { id, ...options });
}
```

Provide a named export for every bridge function your plugin exposes. If your plugin dispatches events, document
how to listen for them in both Livewire and SPA contexts.

### npm Package

Consider publishing your JavaScript library as an npm package so developers can `npm install` it and get proper
TypeScript definitions, autocompletion, and tree-shaking.

## Testing on Real Devices

Simulators and emulators are useful for development, but they don't catch everything. Many native APIs behave
differently — or don't work at all — on real hardware. Camera, biometrics, Bluetooth, NFC, GPS, and sensors all
require real device testing.

### Requirements

- **Android** — test on a physical Android device. Don't rely solely on the Android emulator.
- **iOS** — test on a physical iPhone or iPad. The iOS simulator doesn't support camera, biometrics, or many
  hardware features.
- Test on devices running both current and previous major OS versions when possible.

### Provide a Test App

Ideally, provide a link to a test build so that the NativePHP team and other developers can verify your plugin
works without having to set up the full build chain:

- **iOS** — distribute via [TestFlight](https://developer.apple.com/testflight/)
- **Android** — distribute via a [Google Play testing track](https://support.google.com/googleplay/android-developer/answer/9845334)
  (internal, closed, or open testing)

Include the test app link in your README and your plugin marketplace submission. This significantly speeds up the
review process and builds trust with users.

## Frontend Stack Compatibility

NativePHP apps use different frontend stacks. Your plugin must work with all of them.

### Test With

- **Livewire v3** — the most common stack for NativePHP apps. Test that `#[OnNative]` event listeners work,
  that facade calls from Livewire actions return correct data, and that loading states behave properly.
- **Livewire v4** — test forward compatibility.
- **Inertia + Vue** — test your JavaScript library imports and bridge calls from Vue components. Verify events
  are received correctly.
- **Inertia + React** — same as Vue. Test imports, bridge calls, and event handling from React components.

If your plugin only supports a subset of these stacks, document this clearly in your README. But aim for full
compatibility — it's the difference between a plugin that works for everyone and one that fragments the ecosystem.

### Example: Livewire Component

```php
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Vendor\MyPlugin\Facades\MyPlugin;
use Vendor\MyPlugin\Events\ScanComplete;

class Scanner extends Component
{
    public ?string $result = null;

    public function scan(): void
    {
        MyPlugin::startScan();
    }

    #[OnNative(ScanComplete::class)]
    public function handleScan($data): void
    {
        $this->result = $data['value'];
    }

    public function render()
    {
        return view('livewire.scanner');
    }
}
```

### Example: Vue Component (Inertia)

```vue
<script setup>
import { ref } from 'vue';
import { StartScan, OnScanComplete } from 'vendor-my-plugin';

const result = ref(null);

OnScanComplete((data) => {
    result.value = data.value;
});

async function scan() {
    await StartScan();
}
</script>

<template>
    <button @click="scan">Scan</button>
    <p v-if="result">@{{ result }}</p>
</template>
```

## Boost Guidelines

If your users use [Laravel Boost](https://laravel.com/ai/boost), providing Boost guidelines makes your
plugin dramatically easier to work with. When developers ask their assistant to use your plugin, it will
know exactly how — which methods to call, what events to listen for, and how to handle responses.

Generate guidelines with:

```shell
php artisan native:plugin:boost
```

This creates `resources/boost/guidelines/core.blade.php` in your plugin. Edit it to include:

- All available facade methods with descriptions and parameter types
- All events your plugin dispatches with their payload shapes
- JavaScript usage examples
- Common patterns and gotchas
- Required permissions and configuration

When users install your plugin and run `php artisan boost:install`, these guidelines are automatically loaded.

## Validation

Run the validation command before every release:

```shell
php artisan native:plugin:validate
```

This catches:
- Manifest syntax errors and missing required fields
- Bridge function declarations that don't match native code
- Hook commands that aren't registered
- Missing declared assets

Your plugin should pass validation with zero errors. If you're using the
[Plugin Dev Kit](/products/plugin-dev-kit), use the `/validate-nativephp-plugin` command which runs additional
checks beyond the Artisan command.

Fix every warning too — they often indicate issues that will cause confusing failures for your users at build
time or runtime.

## Automated Review Checks

When you submit your plugin, we run automated checks against your repository. These must all pass before
your plugin can be approved. You can also run `php artisan native:plugin:validate` locally to catch issues early.

### Required Items

**iOS native code** — Your plugin must include native Swift code in `resources/ios/Sources/`. See
[Bridge Functions](/docs/mobile/3/plugins/bridge-functions) for the implementation pattern.

**Android native code** — Your plugin must include native Kotlin code in `resources/android/src/`. See
[Bridge Functions](/docs/mobile/3/plugins/bridge-functions) for the implementation pattern.

**JavaScript library** — Your plugin must include a JavaScript library in `resources/js/` that exports
a function for every bridge function. This allows Inertia + Vue/React developers to call your native functions
directly. See the [JavaScript Implementations](#javascript-implementations) section above.

**Support email** — Your README must include a valid support email address so developers can reach you
with questions or issues.

**Require `nativephp/mobile`** — Your `composer.json` must require the `nativephp/mobile` SDK. This ensures
your plugin is properly integrated with the NativePHP build pipeline:

```json
{
    "require": {
        "nativephp/mobile": "^3.0"
    }
}
```

**iOS `min_version`** — Your `nativephp.json` must specify a minimum iOS version. See
[Advanced Configuration](/docs/mobile/3/plugins/advanced-configuration) for details:

```json
{
    "ios": {
        "min_version": "18.0"
    }
}
```

**Android `min_version`** — Your `nativephp.json` must specify a minimum Android SDK version. See
[Advanced Configuration](/docs/mobile/3/plugins/advanced-configuration) for details:

```json
{
    "android": {
        "min_version": 33
    }
}
```

## Checklist

Before submitting your plugin to the [NativePHP Plugin Marketplace](https://nativephp.com/plugins), verify:

**Automated checks (must pass):**

- [ ] iOS native code in `resources/ios/Sources/`
- [ ] Android native code in `resources/android/src/`
- [ ] JavaScript library in `resources/js/`
- [ ] Support email in your README
- [ ] `nativephp/mobile` required in `composer.json`
- [ ] iOS `min_version` set in `nativephp.json`
- [ ] Android `min_version` set in `nativephp.json`

**Documentation & quality:**

- [ ] README documents installation, PHP usage, and JS usage with complete examples
- [ ] README documents all public methods, events, and required permissions
- [ ] `php artisan native:plugin:validate` passes with zero errors
- [ ] Tested on a physical Android device
- [ ] Tested on a physical iOS device (if iOS is supported)
- [ ] Tested with Livewire v3 and v4
- [ ] Tested with Inertia + Vue
- [ ] Tested with Inertia + React
- [ ] Boost guidelines are included (`php artisan native:plugin:boost`)
- [ ] TestFlight and/or Google Play testing track link provided
- [ ] All secrets and environment variables are documented
- [ ] Changelog is maintained for version history

## Official Plugins & Dev Kit

The official NativePHP plugins follow all of these best practices and serve as reference implementations. Browse
them on the [Plugin Marketplace](https://nativephp.com/plugins) for examples of well-structured, well-documented
plugins.

Need help building your plugin to these standards? The [Plugin Dev Kit](/products/plugin-dev-kit) generates
production-ready plugins with proper structure, documentation, and Boost guidelines built in.

[Get the Plugin Dev Kit →](/products/plugin-dev-kit)