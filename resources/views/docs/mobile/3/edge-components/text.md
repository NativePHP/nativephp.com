---
title: Text
order: 300
---

## Overview

Displays text content using platform-native typography. Text supports font sizing, weight, color, alignment, and line
clamping. The text content is placed between the opening and closing tags.

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
- `font-weight` - Weight from 1-7: 1=thin, 2=light, 3=normal, 4=medium, 5=semibold, 6=bold, 7=extrabold (optional, int, default: `3`)
- `color` - Text color as hex string (optional, default: platform default)
- `text-align` - Alignment: `0`=start, `1`=center, `2`=end (optional, int, default: `0`)
- `max-lines` - Maximum number of lines before truncating with ellipsis (optional, int)

<aside>

`<native:text>` is **not** self-closing. Text content goes between the tags as slot content. HTML tags inside the slot
are stripped -- only plain text is rendered.

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
<native:text :font-weight="3" :font-size="18">Normal (3)</native:text>
<native:text :font-weight="4" :font-size="18">Medium (4)</native:text>
<native:text :font-weight="5" :font-size="18">SemiBold (5)</native:text>
<native:text :font-weight="6" :font-size="18">Bold (6)</native:text>
<native:text :font-weight="7" :font-size="18">ExtraBold (7)</native:text>
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

Blade expressions like `{{ $variable }}` work inside text slots. The content is evaluated by PHP before being passed to
the native renderer.

</aside>
