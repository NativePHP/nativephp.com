---
title: Native UI
order: 150
---

## Overview

Your app's UI is fully native — SwiftUI on iOS, Jetpack Compose on Android — and it's all driven by PHP.

Two pieces work together to make that happen:

- **[SuperNative](../super-native/introduction)** is the engine. Each screen is a PHP component class — think
  Livewire, but for native views. It holds your screen's state and behavior, shares memory directly with the native
  side, and re-renders the UI whenever your properties change.
- **[EDGE](../edge-components/introduction)** (Element Definition and Generation Engine) is the component language.
  It's the full suite of Blade components — layout containers, typography, forms, navigation chrome, overlays — that
  your screens are built from.

You write familiar Blade; your users get truly native UI that matches each platform's design guidelines, in both
light and dark mode.

## Living on the EDGE

Every EDGE element is a Blade component under the `native:` namespace:

@verbatim
```blade
<native:screen>
    <native:column class="w-full p-4 gap-4">
        <native:text class="text-2xl font-bold">Welcome</native:text>
        <native:button label="Refresh" @press="refresh" />
    </native:column>
</native:screen>
```
@endverbatim

We take that single definition and turn it into fully native UI that works beautifully across all the supported
mobile OS versions. EDGE components are fully compatible with hot reloading, so you can iterate on your UI at
runtime without recompiling your app.

Browse the full catalogue in the [EDGE Components](../edge-components/introduction) section.

## Structuring your app

Once you're comfortable with the component syntax, two concepts organize your screens into an app:

### Navigation

[Navigation](navigation) works like a native navigation stack, because it is one. Register screens with
`Route::native()`, then push, pop, and replace them from inside your components — with native transitions, route
parameters, and data passing along the way.

### Layouts

[Layouts](layouts) wrap the screens routed beneath them with shared chrome — a top nav bar, a bottom tab bar, or
both — so individual screens stay focused on their content. Declare the chrome once in a `NativeLayout` class,
attach it to a route or group of routes, and the framework swaps between "tabs" and "stack" chrome automatically as
users move around.
