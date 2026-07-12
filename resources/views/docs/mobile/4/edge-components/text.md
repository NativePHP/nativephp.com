---
title: Text
order: 420
---

## Overview

Displays text content using platform-native typography. Text content goes between the opening and closing tags.

@verbatim
```blade
<native:text class="text-lg font-bold" color="#1E293B">
    Hello, world!
</native:text>
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported, plus:

- `font-size` - Text size in sp/pt (optional, float, default: `16`)
- `font-weight` - Weight 1-7 (optional, int, default: `3`):
    - `1` thin
    - `2` light
    - `3` regular (normal)
    - `4` medium
    - `5` semibold
    - `6` bold
    - `7` heavy (extrabold)

    Values outside 1-7 are clamped to the nearest supported weight.
- `color` - Text color as hex string (optional, default: `#000000`)
- `text-align` - Alignment: `0`=start, `1`=center, `2`=end (optional, int, default: `0`)
- `max-lines` - Maximum lines before truncating with ellipsis (optional, int)
- `font-style` - `0`=normal, `1`=italic (optional, int)
- `font-family` - Typeface: `0`=sans, `1`=serif, `2`=mono (optional, int)
- `underline` - Underline: `1`=on, `0`=off (optional, int)
- `line-through` - Strikethrough: `1`=on, `0`=off (optional, int)
- `text-transform` - Case: `0`=none, `1`=uppercase, `2`=lowercase, `3`=capitalize (optional, int)
- `letter-spacing` - Tracking in em relative to font size (optional, float)
- `font` - Custom font name — a font file from `resources/fonts/` without its extension (optional, string) — see [Custom fonts](#custom-fonts)

Line height is set with `leading-*` classes — see [Line height](#line-height).

<aside>

`<native:text>` is **not** self-closing — content goes between the tags. Nested `<native:text>` elements are
supported as [inline runs](#inline-runs); any other HTML tags in the slot are stripped.

</aside>

## Custom fonts

Ship a font with your app and use it by name. Drop `.ttf`, `.otf`, or `.ttc` files
into your app's `resources/fonts/` directory, then reference one by its filename
(without the extension) with the `font` attribute:

@verbatim
```blade
<native:text font="Inter-Bold" class="text-2xl">
    Custom heading
</native:text>
```
@endverbatim

So `resources/fonts/Inter-Bold.ttf` becomes `font="Inter-Bold"`. The build bundles
the files into the native project automatically — no configuration needed. On iOS
the font is registered and matched by its PostScript name; on Android it's loaded
from the app's assets. An unresolved name falls back to the system font.

`font` also works on [`<native:button>`](button) and the [text inputs](text-input),
and is available fluently as `->font('Inter-Bold')`.

### Downloading from Google Fonts

The `native:font` command downloads any [Google Fonts](https://fonts.google.com)
family straight into `resources/fonts/` — no API key needed:

```bash
php artisan native:font Lobster
php artisan native:font "Rock Salt" Inter
php artisan native:font Inter --weights=400,700 --italic
```

Files are named `<Family>-<Style>.ttf` (`Inter-Bold`, `Inter-BoldItalic`, …) —
ready to use as `font` tokens. Google Fonts are libre-licensed (OFL / Apache),
so bundling them in your app is permitted.

### App-wide default font

Set the theme's `font-family` token in `config/native-ui.php` to apply a bundled
font everywhere — text, buttons, inputs, and navigation chrome — without touching
individual elements:

```php
'font-family' => 'Inter-Regular',   // a resources/fonts/ token; 'System' = platform default
```

Per-element `font` attributes and explicit `font-serif` / `font-mono` classes
still win over the default. The `native:font` command offers to set this for you
(`--default`, or answer the prompt) and publishes the config if needed.

<aside>

Font size and weight still come from `text-*` / `font-*` classes (or `font-size` /
`font-weight`) and the theme — `font` only changes the typeface.

**Weights with single-file fonts**: a font file carries one weight, and the
platforms treat a missing weight differently — Android *synthesizes* a faux
bold (stretched glyphs), iOS ignores the weight and renders the file's native
weight. Until real multi-weight family support lands, avoid `font-bold` on
single-weight custom fonts; for a true bold, bundle the Bold file and reference
it directly (`font="Inter-Bold"`).

</aside>

## Line height

Control the leading (line spacing) with Tailwind `leading-*` classes — unitless
multipliers of the font size:

- `leading-none` (1) · `leading-tight` (1.25) · `leading-snug` (1.375)
- `leading-normal` (1.5) · `leading-relaxed` (1.625) · `leading-loose` (2)

Arbitrary values are supported too: `leading-[1.4]` (multiplier) or `leading-[24px]`
(absolute).

@verbatim
```blade
<native:text class="text-base leading-relaxed">
    A comfortably spaced paragraph that wraps across several lines with a little
    extra breathing room between them.
</native:text>
```
@endverbatim

Line height only affects multi-line text.

<aside>

On iOS, *increasing* leading (`relaxed`, `loose`, or a large `leading-[…px]`) is
exact; tightening below the font's natural line height (`none`, `tight`) is limited
by SwiftUI and may bottom out at the natural spacing. Android is exact both ways.

</aside>

## Text styling

Style text with Tailwind classes — these compose with `text-*` size and `font-*` weight:

- **Italic** — `italic`, `not-italic`
- **Decoration** — `underline`, `line-through`, `no-underline`. `underline` and
  `line-through` are independent flags, so they combine; `no-underline` clears both.
- **Transform** — `uppercase`, `lowercase`, `capitalize`, `normal-case`
- **Letter spacing (tracking)** — `tracking-tighter`, `tracking-tight`,
  `tracking-normal`, `tracking-wide`, `tracking-wider`, `tracking-widest` (in em,
  relative to the font size)
- **Font family** — `font-sans` (default), `font-serif`, `font-mono`

@verbatim
```blade
<native:text class="text-lg font-semibold italic underline tracking-wide">
    Styled with Tailwind
</native:text>
```
@endverbatim

## Inline runs

Nest `<native:text>` elements inside a `<native:text>` to style spans within a single paragraph. The nested runs
and the surrounding text compose into **one** attributed string that wraps together as a unit — each run carries
its own classes (weight, color, size):

@verbatim
```blade
<native:text class="text-base text-theme-on-surface">
    Use <native:text class="font-bold">bold</native:text> and
    <native:text class="text-theme-primary font-semibold">color</native:text> inline.
</native:text>
```
@endverbatim

Runs render in document order, so interleaved text and nested `<native:text>` stay in sequence. A `<native:text>`
with no nested runs behaves exactly like a plain string.

## Text selection

Text isn't selectable by default. Add `select-text` to make a subtree long-press-selectable (the native Copy
menu). It's container-scoped and inherited, so it covers every descendant — put it on a wrapping element to make a
whole region selectable. Use `select-none` to opt a nested subtree back out:

@verbatim
```blade
<native:column class="select-text">
    <native:text class="text-lg font-bold">Selectable heading</native:text>
    <native:text class="text-base">This body copy can be selected and copied.</native:text>

    <native:text class="select-none text-xs text-theme-on-surface-variant">Not selectable</native:text>
</native:column>
```
@endverbatim

<aside>

`select-text` / `select-none` work on **any** element, not just text — they scope selection for the whole
subtree. The programmatic equivalent is `->selectable()` / `->selectable(false)`.

</aside>

## Dark Mode

Use the `dark:` prefix with Tailwind classes or pass a dark-mode color override.

@verbatim
```blade
<native:text class="text-slate-900 dark:text-white text-lg">
    Adapts to dark mode
</native:text>
```
@endverbatim

## Examples

### Font weight scale

@verbatim
```blade
<native:text :font-weight="1" :font-size="18">Thin (1)</native:text>
<native:text :font-weight="2" :font-size="18">Light (2)</native:text>
<native:text :font-weight="3" :font-size="18">Regular (3)</native:text>
<native:text :font-weight="4" :font-size="18">Medium (4)</native:text>
<native:text :font-weight="5" :font-size="18">SemiBold (5)</native:text>
<native:text :font-weight="6" :font-size="18">Bold (6)</native:text>
<native:text :font-weight="7" :font-size="18">Heavy (7)</native:text>
```
@endverbatim

### Truncated text

@verbatim
```blade
<native:text class="text-base" :max-lines="2" color="#64748B">
    This text will be truncated with an ellipsis after two lines if it overflows
    the available space in its container.
</native:text>
```
@endverbatim

### Centered heading

@verbatim
```blade
<native:text class="text-3xl font-extrabold text-center" color="#7C3AED">
    Welcome Back
</native:text>
```
@endverbatim

### Tappable text

@verbatim
```blade
<native:text class="text-base font-semibold" color="#3B82F6" @press="openLink">
    Learn more
</native:text>
```
@endverbatim

### Dynamic content

@verbatim
```blade
<native:text class="text-lg font-semibold" color="#7C3AED">
    Score: {{ $score }}
</native:text>
```
@endverbatim

<aside>

Blade expressions like `@{{ $variable }}` work inside text slots. The content is evaluated by PHP before being passed
to the native renderer.

</aside>

## Element

```php
use Native\Mobile\Edge\Elements\Text;

Text::make('Hello')
    ->fontSize(18)
    ->fontWeight(6)
    ->color('#1E293B')
    ->textAlign(1)
    ->maxLines(2);
```

- `make(string $text = '')` - Create text with content
- `fontSize(float $size)` - Text size
- `font(string $name)` - Custom font from `resources/fonts/` (filename without extension)
- `fontWeight(int $weight)` - 1-7 (clamped to the nearest supported weight)
- `bold()` - Shortcut for `fontWeight(7)`
- `fontStyle(int $style)` - `0`=normal, `1`=italic
- `italic()` - Shortcut for `fontStyle(1)`
- `color(string $hex)` - Text color
- `textAlign(int $align)` - `0`=start, `1`=center, `2`=end
- `maxLines(int $lines)` - Truncate after N lines
- `selectable(bool $on = true)` - Make the subtree selectable (mirrors `select-text` / `select-none`)
