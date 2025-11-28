---
title: Native Components
order: 150
---

![](/img/docs/edge.png)

NativePHP for Mobile also supports rendering native UI components!

Starting with v2, we've introduced a few key navigation components to give your apps an even more native feel.

We call this **EDGE** - Element Generation and Definition Engine.

## Living on the EDGE

EDGE components are **truly native** elements that match each platform's design guidelines.

Built on top of Laravel's Blade, EDGE gives you the power of native UI components with the simplicity you expect.

@verbatim
```blade
<native:bottom-nav>
    <native:bottom-nav-item id="home" icon="home" label="Home" url="/home" />
</native:bottom-nav>
```
@endverbatim

We take a single definition and turn it into fully native UI that works beautifully across all the supported mobile OS
versions, in both light and dark mode.

And they're fully compatible with hot reloading, which means you can swap them in and out at runtime without needing
to recompile your app!

You can find out all about EDGE and the available components in the [EDGE Components](../edge-components/) section.
