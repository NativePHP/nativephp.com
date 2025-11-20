---
title: EDGE Components
order: 150
---

## What is EDGE?

EDGE (Element Definition and Generation Engine) is NativePHP Mobile's component system that transforms Blade syntax into platform-native UI elements.

Instead of rendering in the WebView, EDGE components are compiled into truly native navigation elements that match each platform's design guidelines - Material Design on Android and iOS native patterns on iOS.

## Available Components

EDGE provides navigation components for building native mobile interfaces:

- **[Bottom Navigation](/docs/mobile/2/edge-components/bottom-nav)** - Bottom navigation bar with up to 5 items
- **[Top Bar](/docs/mobile/2/edge-components/top-bar)** - Top app bar with title and action buttons
- **[Floating Action Button](/docs/mobile/2/edge-components/fab)** - Floating button for primary actions
- **[Side Navigation](/docs/mobile/2/edge-components/side-nav)** - Slide-out navigation drawer

## How It Works

Write familiar Blade components in your layouts:

@verbatim
```blade
<native:bottom-nav>
    <native:bottom-nav-item
        id="home"
        icon="home"
        label="Home"
        url="/home"
    />
</native:bottom-nav>
```
@endverbatim

EDGE processes these during the build and renders them as native components that sit outside your WebView, providing better performance and a truly native feel.

## Notes

- Components are rendered natively, not in the WebView
- Updates require a page reload or Livewire refresh
- Automatically adapt to platform design guidelines
