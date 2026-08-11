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
  `numberPassword`. On iOS `password` uses the standard keyboard; `secure` is the masking mechanism. The keyboard
  type also decides capitalization and autocorrect — see [Capitalization](#capitalization)
- `autocapitalize` - Override that capitalization: `none`, `sentences`, `words`, or `characters` (HTML's
  vocabulary). Leave it unset to let `keyboard` decide (optional, string)
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
- `selection-debounce-ms` - Coalescing window for `@selectionChange` events (optional, int, default: `150`). `0` or
  less means the default; positive values are floored at one frame (16ms). See
  [Caret and selection reporting](#caret-and-selection-reporting)

### Decorations

- `prefix` - Text rendered before the input (optional, string)
- `suffix` - Text rendered after the input (optional, string)
- `leading-icon` - Icon name rendered at the start (optional, string)
- `trailing-icon` - Icon name rendered at the end (optional, string)
- `ios-leading-icon` / `android-leading-icon` - Per-platform overrides for `leading-icon` (optional). See
  [Per-platform icons](#per-platform-icons)
- `ios-trailing-icon` / `android-trailing-icon` - Per-platform overrides for `trailing-icon` (optional)

### Typography

- `font` - Custom font: a `resources/fonts/` file token or a config alias like `accent` (optional, string) — see [Text › Custom fonts](text#custom-fonts)
- `leading-*` classes set line height for the typed text (multi-line only). Applies on Android; **iOS inputs don't reflect it** — SwiftUI's editable field ignores line spacing (it works on [`<native:text>`](text#line-height))
- `line-height` / `line-height-px` attributes are an alternative to the `leading-*` classes: `line-height` is a multiplier of the font size, `line-height-px` an absolute override

### Sizing & accessibility

- `size` - `sm | md (default) | lg`
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Capitalization

Declaring a `keyboard` type carries its typing behaviour with it, not just the key layout. Fields whose content is
case-sensitive or non-alphabetic never capitalize, and never autocorrect:

| `keyboard` | Capitalization | Autocorrect |
|------------|----------------|-------------|
| `text` (default) | sentences on iOS, none on Android | on |
| `email`, `url` | none | off |
| `number`, `decimal`, `phone`, `password`, `numberPassword` | none | off |

@verbatim
```blade static
{{-- Email keyboard AND no capitalized first letter — no extra attribute needed --}}
<native:outlined-text-input native:model="email" label="Email" keyboard="email" />
```
@endverbatim

Use `autocapitalize` for the cases a keyboard type can't imply:

@verbatim
```blade static
<native:outlined-text-input native:model="name" label="Full name" autocapitalize="words" />
<native:outlined-text-input native:model="code" label="Booking reference" autocapitalize="characters" />
```
@endverbatim

`autocapitalize` always wins over the derived value, and an unrecognised value falls back to the derived behaviour
rather than erroring.

<aside>

A plain text field with neither attribute set capitalizes sentences on iOS and nothing on Android — each platform's
own default, left as-is. Set `autocapitalize` explicitly when you need the two to match.

</aside>

## Events

- `@change` - Component method called when the text changes. Receives the new value
- `@submit` - Component method called when the user submits (e.g. presses return). Receives the current value
- `@selectionChange` - Component method called when the caret moves or the selection changes. Receives the full
  current text plus the selection start and end offsets — see
  [Caret and selection reporting](#caret-and-selection-reporting)

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
automatically, so the `@{{ $name }}` echo updates as you type.

`sync-mode` semantics:

- `live` (default) — every keystroke fires `@change`
- `blur` — only fires on focus loss / submit
- `debounce` — fires after `debounce-ms` of inactivity (300ms when unset), or immediately on blur / submit

## Caret and selection reporting

`@selectionChange` reports caret position and text selection back to your component — for the cases where `@change`
alone can't tell you *where* the user is typing. The handler receives the full current text plus the selection range:

```php
public function onCaretMove(string $text, int $selectionStart, int $selectionEnd)
{
    // $selectionStart === $selectionEnd when the caret is a plain cursor;
    // they differ when a range of text is selected.
}
```

Offsets are Unicode code points into the text — not UTF-16 units or bytes — so emoji count as one character and the
values are safe to feed straight into `mb_substr()`.

Events are coalesced on the native side: at most one every 150ms while the caret moves, and the trailing position
always fires. Tune the window per input with `selection-debounce-ms` (fluent: `->selectionDebounceMs()`) — `0` or
less means the default, positive values are floored at one frame (16ms).

The classic use is a mention / typeahead trigger:

@verbatim
```blade
@php $message = ''; @endphp

<native:outlined-text-input
    label="Message"
    native:model="message"
    @selectionChange="onCaretMove"
/>
```
@endverbatim

```php
public array $suggestions = [];

public function onCaretMove(string $text, int $start, int $end): void
{
    // Look backwards from the caret for an "@mention" trigger.
    $before = mb_substr($text, 0, $start, 'UTF-8');

    if (preg_match('/@(\w*)$/u', $before, $m)) {
        $this->suggestions = $this->matchingHandles($m[1]);
    } else {
        $this->suggestions = [];
    }
}
```

The handler slices the text at the caret, so typing `@ja` in the middle of a sentence surfaces suggestions for the
fragment under the cursor — something `@change` can't do, since it only carries the value.

<aside>

Every `@selectionChange` event carries the full current text and costs a full component re-render. That is
independent of the `native:model` sync mode — pairing it with `native:model.blur` or `.debounce` still ships the
field contents to PHP on the selection cadence, not the model cadence. If you only need the text, stick with
`@change`; reach for `@selectionChange` only when the caret position matters.

</aside>

A few contract details:

- `@selectionChange` is never emitted for `secure` inputs. The callback isn't even serialized when `secure` is set,
  and both renderers additionally refuse to emit — so caret telemetry can't leak password-field context.
- When PHP pushes a new `value` onto the input, the field is replaced wholesale and the caret drops at the end.
  Both platforms report that immediately as a single `(text, length, length)` event, bypassing the debounce — a
  handler that rewrites the bound model will see one follow-up event.
- Discontiguous selections (multi-range, iOS) are reported as a single span from the lowest start to the highest
  end, so the range can cover text the user did not select.
- `read-only` inputs don't report on iOS, where read-only implies disabled and the field never focuses; they do on
  Android, which keeps them focusable for copy.

<aside>

Caret and selection reporting requires `nativephp/mobile` 4.0+, which ships the `text_selection` callback kind.

</aside>

## Per-platform icons

The shared `leading-icon` / `trailing-icon` names render the same icon on both platforms. When each platform should
show its own symbol, prefix the attribute with the platform — the same convention as
[`<native:button>`](button)'s `ios-icon`:

@verbatim
```blade
<native:outlined-text-input
    label="Email"
    leading-icon="email"
    ios-leading-icon="envelope.badge"
/>
```
@endverbatim

iOS renders the `envelope.badge` SF Symbol; Android falls back to the shared `email` name. The shared attribute is
the fallback for whichever platform has no override — set only a platform-prefixed attribute and the other platform
renders no icon at all.

Bound with `:`, the icon attributes also accept the typed icon enums (`App\Icons\Ios`, `App\Icons\Android`,
`App\Icons\AndroidOutlined`) instead of strings — see [Icon › Typed icon enums](icon#typed-icon-enums):

@verbatim
```blade
@use('App\Icons\Ios')
@use('App\Icons\AndroidOutlined')

<native:outlined-text-input
    label="Search"
    :ios-leading-icon="Ios::Magnifyingglass"
    :android-leading-icon="AndroidOutlined::Search"
/>
```
@endverbatim

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
- `selectionDebounceMs(int $ms)` - Coalescing window for `@selectionChange` events (150ms when unset; `0` or less
  means the default, positive values floored at 16ms)
- `onChange(string $method)`, `onSubmit(string $method)`, `onSelectionChange(string $method)`

`BareTextInput` adds one method on top of the shared API:

- `color(string $color)` - Text color as a hex value or Tailwind token (with `dark:text-*` support)
