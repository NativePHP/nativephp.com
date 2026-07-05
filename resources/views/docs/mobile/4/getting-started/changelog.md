---
title: Changelog
order: 2
---

For changes prior to v4, see the [v3 documentation](/docs/mobile/3/getting-started/changelog).

@forelse (\App\Support\GitHub::mobileAir()->releasesAfter('4.0.0') as $release)
## {{ $release->name ?: $release->tag_name }}
**Released: {{ \Carbon\Carbon::parse($release->published_at)->format('F j, Y') }}**

{{ $release->getBodyForMarkdown() }}
---
@empty
@endforelse

## v4.0 — SuperNative (beta)

### New Features

- **[SuperNative](../super-native/introduction)** — fully native UI powered by SwiftUI on iOS and Jetpack Compose on Android, and the new default for v4 apps
- **Livewire-like native components** — each screen is a PHP `NativeComponent` class holding its state and behavior, re-rendering the native UI as your properties change
- **Shared memory between PHP and the native layer** — no serialization overhead or web view bridge between your code and the UI
- **[Web view as a component](../edge-components/web-view)** — the classic web-view-first approach is now opt-out: render a single native route with a full-screen web view to keep building the v3 way
- **[Layouts](../super-native/layouts)** — declare shared chrome (nav bars, tab bars) once in a `NativeLayout` class and attach it to a route or group of routes
- **[Native navigation stack](../super-native/navigation)** — register screens with `Route::native()`, then push, pop, and replace them with native transitions
- **[Component testing suite](../testing/introduction)** — mount a `NativeComponent`, drive interactions, and assert on state and output entirely in-process, with no device or simulator

### For Plugin Developers

- **No breaking changes** to the plugin architecture — add the `^4.0` constraint to your plugin's `nativephp/mobile` dependency and you're done
