---
title: Changelog
order: 2
---

For changes prior to v4, see the [v3 documentation](/docs/mobile/3/getting-started/changelog).

@forelse (\App\Support\GitHub::mobileAir()->releasesFrom('4.0.0') as $release)
## {{ $release->name ?: $release->tag_name }}
**Released: {{ \Carbon\Carbon::parse($release->published_at)->format('F j, Y') }}**

{{ $release->getBodyForMarkdown() }}
---
@empty
@endforelse

## v4.0 — SuperNative (beta)

### New Features

- **[SuperNative](../architecture/super-native)** — fully native UI powered by SwiftUI on iOS and Jetpack Compose on Android, and the new default for v4 apps
- **Livewire-like native components** — each screen is a PHP `NativeComponent` class holding its state and behavior, re-rendering the native UI as your properties change
- **Shared memory between PHP and the native layer** — no serialization overhead or web view bridge between your code and the UI
- **[Web view as a component](../edge-components/web-view)** — the classic web-view-first approach is now opt-out: render a single native route with a full-screen web view to keep building the v3 way
- **[Layouts](../the-basics/layouts)** — declare shared chrome (nav bars, tab bars) once in a `NativeLayout` class and attach it to a route or group of routes
- **[Native navigation stack](../the-basics/routing)** — register screens with `Route::native()`, then push, pop, and replace them with native transitions
- **[Component testing suite](../testing/introduction)** — mount a `NativeComponent`, drive interactions, and assert on state and output entirely in-process, with no device or simulator

### Breaking Changes

- **Device, Dialog, File and System are now core built-ins** — the `nativephp/mobile-device`, `nativephp/mobile-dialog`, `nativephp/mobile-file`, and `nativephp/mobile-system` plugins have moved into `nativephp/mobile`. **Remove the standalone plugins before upgrading** (see the [Upgrade Guide](upgrade-guide)). The facades and events are unchanged, so no application code changes are needed.
- **The Vite dev server is now opt-in** — `native:run` / `native:watch` no longer start Vite automatically. Pass `--vite` to enable JS/CSS HMR. The `--no-vite` flag still works but is now redundant. See [Hot Reloading](development#hot-reloading).

### For Plugin Developers

- **No breaking changes** to the plugin architecture — add the `^4.0` constraint to your plugin's `nativephp/mobile` dependency and you're done
