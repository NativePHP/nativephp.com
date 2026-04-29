---
title: Screen
order: 100
---

## Overview

A themed page-level container. Applies `theme.background` as the full-bleed backdrop and `theme.onBackground` as
the default content color for descendants. Adapts automatically to the system's light/dark mode.

Use it as the root of a page so background and default content color follow your theme everywhere — no need to
add `bg-...` classes to every screen.

@verbatim
```blade
<native:screen>
    <native:scroll-view>
        <native:column class="w-full p-4 gap-4">
            <native:text class="text-2xl font-bold">Welcome</native:text>
            <native:text class="text-base">Your dashboard.</native:text>
        </native:column>
    </native:scroll-view>
</native:screen>
```
@endverbatim

## Props

`<native:screen>` is intentionally props-less — it's a contextual backdrop, not a styled element. Layout
attributes (`width`, `margin`, etc.) are still accepted in case you need to adjust position inside a parent, but
typical usage is bare.

## Children

Accepts any EDGE elements as children. Most commonly wraps a single [`<native:scroll-view>`](scroll-view) or
[`<native:column>`](column).

<aside>

The background ignores the safe area so it extends edge-to-edge. Children render in the safe-area-aware region by
default — apply `safe-area` to your top-level container if you need explicit insets.

</aside>

## Element

```php
use Nativephp\NativeUi\Elements\Screen;

Screen::make();
```

- `make()` - Create a screen container
