---
title: Native Components
order: 150
---

## Living on the EDGE

NativePHP for Mobile also supports rendering native UI components! Starting with v2, we've introduced a few key
navigation components to give your apps an even more native feel.

We call this EDGE - Element Generation and Definition Engine.

EDGE components are **truly native** navigation elements that match each platform's design guidelines.

EDGE gives you the power of native UI components with the simplicity of Blade. What's more, they're fully compatible
with hot reoloading, which means you can swap them in and out at runtime without needing to recompile your app!

@verbatim
```blade
<native:bottom-nav>
    <native:bottom-nav-item id="home" icon="home" label="Home" url="/home" />
</native:bottom-nav>
```
@endverbatim

You can find out all about EDGE and the available components in the [EDGE Components](../edge-components/) section.
