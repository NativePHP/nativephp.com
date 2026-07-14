---
title: Text Input
order: 430
---

## Overview

Native text input fields come in three variants:

- `<native:outlined-text-input>` — bordered field. Default, lower emphasis.
- `<native:filled-text-input>` — surface-fill background + bottom indicator line. Higher emphasis.
- `<native:bare-text-input>` — chromeless field with no Material chrome, for chat pills, search bars, and inline
  editors where the surrounding container supplies the visuals. See [Bare variant](#bare-variant).

All three share the same prop set and event API. Choose the outlined / filled pair based on emphasis, not behavior;
reach for bare when you want to style the input yourself.

On iOS the outlined and filled variants render as SwiftUI `TextField` / `SecureField` with Material3-style chrome; on
Android they map to `OutlinedTextField` / `TextField` (filled). Per Material 3 these two have no per-instance color or
border overrides — all chrome resolves from the theme. For fully custom input visuals reach for the bare variant, or
drop to [`<native:pressable>`](pressable) wrapping your own drawing.

@verbatim
```blade
@php $email = ''; @endphp

<native:outlined-text-input
    label="Email"
    placeholder="you@example.com"
    native:model="email"
    keyboard="email"
    leading-icon="email"
/>
```
@endverbatim

`email` is a public string property on your component — the `@php` line stands in for
`public string $email = '';` and seeds the inline preview.

## Props

All three variants accept the same shared prop set. The bare variant adds a `color` attribute on top — see
[Bare variant](#bare-variant).

### Content

- `value` - Current text value (optional, string)
- `placeholder` - Placeholder shown when empty (optional, string)
- `label` - Label rendered above the field (optional, string)
- `supporting` - Helper text rendered below the field (optional, string)

### State

- `disabled` - Disable the input (optional, boolean, default: `false`)
- `read-only` - Make the input read-only (optional, boolean, default: `false`)
- `is-error` - Show error styling (border / indicator + supporting text turn `theme.destructive`)
- `loading` - Show a spinner in the trailing position (optional, boolean, default: `false`)

### Behavior

- `keyboard` - Keyboard hint string: `text` (default), `number`, `email`, `phone`, `url`, `decimal`, `password`,
  `numberPassword`. On iOS `password` uses the standard keyboard; `secure` is the masking mechanism
- `secure` - Mask input for passwords (optional, boolean, default: `false`)
- `multiline` - Allow multiple lines (optional, boolean, default: `false`)
- `max-length` - Maximum character count (optional, int)
- `max-lines` - Maximum visible lines when `multiline` (optional, int)
- `min-lines` - Minimum visible lines when `multiline` (optional, int)
- `keep-focus-on-submit` - Keep the keyboard up after `@submit` instead of unfocusing the field on return — the chat
  "send and keep typing" pattern (optional, boolean, default: `false`)
- `sync-mode` - How change events dispatch back to your component: `live` (default), `blur`, or `debounce`. Usually
  set via the `native:model` modifiers below, but accepted directly too
- `debounce-ms` - Milliseconds of inactivity before a `debounce` sync fires (optional, int, default: `300`)

### Decorations

- `prefix` - Text rendered before the input (optional, string)
- `suffix` - Text rendered after the input (optional, string)
- `leading-icon` - Icon name rendered at the start (optional, string)
- `trailing-icon` - Icon name rendered at the end (optional, string)

### Typography

- `font` - Custom font: a `resources/fonts/` file token or a config alias like `accent` (optional, string) — see [Text › Custom fonts](text#custom-fonts)
- `leading-*` classes set line height for the typed text (multi-line only). Applies on Android; **iOS inputs don't reflect it** — SwiftUI's editable field ignores line spacing (it works on [`<native:text>`](text#line-height))
- `line-height` / `line-height-px` attributes are an alternative to the `leading-*` classes: `line-height` is a multiplier of the font size, `line-height-px` an absolute override

### Sizing & accessibility

- `size` - `sm | md (default) | lg`
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Component method called when the text changes. Receives the new value
- `@submit` - Component method called when the user submits (e.g. presses return). Receives the current value

<aside>

All three variants are self-closing. They do not accept children.

</aside>

<aside>

To keep an input visible while the keyboard is up — a chat composer, a search bar — place it in a
[`<native:bottom-bar>`](../the-basics/layouts#keyboard-aware-bottom-content), which pins above the keyboard
automatically. Avoid manually padding or shifting the screen yourself.

</aside>

## Two-way Binding

Use the `native:model` directive for automatic two-way binding with a component property. The directive expands to
`:value`, `@change="__syncProperty(...)"`, and a `sync-mode` prop driven by the modifier chain.

@verbatim
```blade
@php $name = 'Ada'; $email = ''; $search = ''; @endphp

<native:outlined-text-input label="Name" native:model="name" />
<native:outlined-text-input label="Email" native:model.blur="email" />
<native:outlined-text-input label="Search" native:model.debounce.500ms="search" />

<native:text class="text-sm text-theme-on-surface-variant">Hello, {{ $name }}!</native:text>
```
@endverbatim

`name`, `email`, and `search` are public string properties on your component — typing syncs them back
automatically, so the `{{ $name }}` echo updates as you type.

`sync-mode` semantics:

- `live` (default) — every keystroke fires `@change`
- `blur` — only fires on focus loss / submit
- `debounce` — fires after `debounce-ms` of inactivity (300ms when unset), or immediately on blur / submit

## Bare variant

`<native:bare-text-input>` is a chromeless input — no outline, no fill, no label, no Material chrome, just the typing
affordance. It's built for chat input pills, search bars, and inline editors where the surrounding container provides
the visuals. On iOS it renders as a plain SwiftUI `TextField`; on Android as a Compose `BasicTextField`.

It inherits the full shared prop set — `native:model`, `secure`, `multiline`, `keyboard`, `@submit`,
`keep-focus-on-submit`, `disabled`, `read-only`, and the rest — so it behaves exactly like the other variants.

Two things set it apart:

- **Class-based styling passes through.** Unlike the filled / outlined variants (which resolve all chrome from the
  theme), the bare variant lets element-level styling flow to the input directly: `bg`, `rounded-*`, borders, `glass`,
  opacity, elevation, and padding. So you can style the pill on the input itself, no wrapping row needed.
- **A `color` attribute** sets the text color — a hex value or a Tailwind token, with `dark:text-*` support for a
  light/dark pair. Useful when your wrapper overrides the background and the theme's default text color would vanish.

@verbatim
```blade static
@php $draft = ''; @endphp

<native:bare-text-input
    class="flex-1 glass rounded-full px-4 py-2 dark:text-slate-700"
    placeholder="Message"
    native:model="draft"
    @submit="send"
    keep-focus-on-submit
/>
```
@endverbatim

The `color` attribute can be set explicitly or picked up from a `text-*` class on the input:

@verbatim
```blade static
@php $query = ''; @endphp

<native:bare-text-input placeholder="Search" native:model="query" color="slate-700" />
<native:bare-text-input placeholder="Search" native:model="query" class="text-slate-700 dark:text-slate-300" />
```
@endverbatim

<aside>

`<native:bare-text-input>` is self-closing. It does not accept children.

</aside>

## Examples

### Login form

@verbatim
```blade
@php $email = ''; $password = ''; @endphp

<native:column class="w-full gap-4 p-4">
    <native:outlined-text-input
        label="Email"
        placeholder="you@example.com"
        native:model="email"
        keyboard="email"
        leading-icon="email"
    />
    <native:outlined-text-input
        label="Password"
        placeholder="Enter password"
        native:model="password"
        secure
        leading-icon="lock"
    />
    <native:button label="Sign In" @press="login" />
</native:column>
```
@endverbatim

### Filled variant with validation error

@verbatim
```blade
@php $email = 'not-an-email'; @endphp

<native:filled-text-input
    label="Email"
    native:model="email"
    is-error
    supporting="Please enter a valid email address"
/>
```
@endverbatim

### Multiline textarea

@verbatim
```blade
@php $message = ''; @endphp

<native:outlined-text-input
    label="Message"
    placeholder="Type your message..."
    native:model="message"
    multiline
    :min-lines="3"
    :max-lines="8"
/>
```
@endverbatim

### Search with submit

@verbatim
```blade
@php $query = ''; @endphp

<native:filled-text-input
    placeholder="Search..."
    native:model.debounce.300ms="query"
    @submit="submitSearch"
    leading-icon="search"
/>
```
@endverbatim

### Prefix and suffix

@verbatim
```blade
@php $price = '49'; @endphp

<native:outlined-text-input
    label="Price"
    native:model="price"
    prefix="$"
    suffix=".00"
    keyboard="decimal"
/>
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\OutlinedTextInput;
use Nativephp\NativeUi\Elements\FilledTextInput;
use Nativephp\NativeUi\Elements\BareTextInput;

OutlinedTextInput::make()
    ->label('Email')
    ->placeholder('you@example.com')
    ->value($email)
    ->keyboard('email')
    ->leadingIcon('email')
    ->onChange('updateEmail');
```

All three elements share the same fluent API (defined on `BaseTextInput`):

- `value(string $text)`, `placeholder(string $text)`, `label(string $text)`, `supporting(string $text)`
- `disabled(bool $value = true)`, `readOnly(bool $value = true)`, `error(bool $value = true)`, `loading(bool $value = true)`
- `keyboard(string|int $type)`, `secure(bool $value = true)`, `maxLength(int $length)`
- `multiline(bool $value = true)`, `maxLines(int $lines)`, `minLines(int $lines)`
- `keepFocusOnSubmit(bool $value = true)` - Keep the keyboard up after `@submit`
- `prefix(string $text)`, `suffix(string $text)`
- `leadingIcon(?string $name = null, IosSymbol|string|null $ios = null, AndroidSymbol|string|null $android = null)` -
  pass a shared `$name`, or per-platform `$ios` / `$android` symbols for a different icon on each platform
- `trailingIcon(?string $name = null, IosSymbol|string|null $ios = null, AndroidSymbol|string|null $android = null)` -
  same per-platform form as `leadingIcon()`
- `size(string $value)` - `sm | md | lg`
- `font(string $name)` - Custom font (file token or config alias)
- `a11yLabel(string $value)`, `a11yHint(string $value)`
- `syncMode(string $mode)`, `debounceMs(int $ms)`
- `onChange(string $method)`, `onSubmit(string $method)`

`BareTextInput` adds one method on top of the shared API:

- `color(string $color)` - Text color as a hex value or Tailwind token (with `dark:text-*` support)
