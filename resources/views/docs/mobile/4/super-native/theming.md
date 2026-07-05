---
title: Theming
order: 65
---

## Overview

Every SuperNative app has one visual identity, defined in a single theme. Instead of hard-coding colors on each
element, you name **semantic tokens** — `primary`, `surface`, `on-surface` — and reference them everywhere. Change
a token once and it updates across every screen, in both light and dark mode.

The theme is provided by the `nativephp/native-ui` plugin (which ships the components), but it governs the whole
app, so it's the visual contract for everything you build.

## Publishing the config

Publish the theme file to your app to customize it:

```shell
php artisan vendor:publish --tag=native-ui-config
```

That writes `config/native-ui.php`, which holds a `theme` array of `light` and `dark` token blocks plus radii and
font settings.

## Tokens

Colors come in **pairs**: a surface token and its `on-` counterpart — the color of content (text, icons) placed
on that surface. Pairing them this way is what keeps contrast correct across light and dark.

| Token | Used for |
| --- | --- |
| `primary` / `on-primary` | Filled buttons, active states, key accents |
| `secondary` / `on-secondary` | Muted/secondary actions |
| `surface` / `on-surface` | Cards, sheets, dialogs |
| `background` / `on-background` | The page root behind everything |
| `surface-variant` / `on-surface-variant` | Filled text fields, muted tonal surfaces / hint text |
| `outline` | Neutral borders — fields, dividers, cards |
| `destructive` / `on-destructive` | Destructive actions (`variant="destructive"`) |
| `accent` / `on-accent` | Highlights, badges, emphasis outside `primary` |

Plus non-color tokens: `radius-sm|md|lg|full`, `font-sm|md|lg|xl`, and `font-family` (`System` resolves to San
Francisco on iOS / Roboto on Android; set a family name to load a custom font).

## Using tokens in a screen

Reference any token from Blade with the `theme-{token}` class suffix, on background, text, and border utilities:

@verbatim
```blade
<native:column class="bg-theme-surface border border-theme-outline rounded-2xl p-4">
    <native:text class="text-theme-on-surface text-lg font-bold">Balance</native:text>
    <native:text class="text-theme-on-surface-variant">Updated just now</native:text>
</native:column>
```
@endverbatim

Because the tokens carry the theme, you rarely reach for raw palette classes like `bg-slate-800` — and your UI
stays correct when the system switches between light and dark.

## Light & dark

The `dark` block is **auto-derived** from `light` by luminance inversion, so a light-only theme already adapts.
Specify any token under `dark` to override just that value:

```php
'theme' => [
    'light' => [
        'primary' => '#0F766E',
        // ...
    ],
    'dark' => [
        // Everything else derives from light; only override what you want to tune.
        'primary' => '#14B8A6',
    ],
],
```

## Runtime theming

For per-tenant or user-selectable themes, merge tokens at runtime from a service provider with `Theme::merge()`.
It deep-merges over the config values, so you only pass what changes:

```php
use Nativephp\NativeUi\Theme;

Theme::merge([
    'light' => ['primary' => $tenant->brandColor],
]);
```

`Theme::merge()` layers on top of `config/native-ui.php`; `Theme::reset()` returns to the config defaults.

<aside>

Keep every `on-*` color at **4.5:1** contrast against its surface (WCAG AA). The shipped defaults already meet it —
if you customize, re-check the pairs you change. See [Accessibility](accessibility).

</aside>
