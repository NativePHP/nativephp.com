---
title: Data Binding
order: 20
---

## Overview

`native:model` two-way binds a native input to a public property on your component — the native equivalent of
Livewire's `wire:model`. Type into a field, toggle a switch, drag a slider, and the bound property updates on the
PHP side; change the property in PHP and the control reflects it on the next render.

@verbatim
```blade
<native:text-input native:model="name" placeholder="Your name" />
```
@endverbatim

```php
class ProfileScreen extends NativeComponent
{
    public string $name = '';
}
```

That's the whole contract: no `@press`, no manual `onChange` handler. The value lives in `$name`, and every
render sees the current value.

## Supported controls

Any input-style EDGE component binds with `native:model`:

- [`<native:text-input>`](../edge-components/text-input) — string
- [`<native:toggle>`](../edge-components/toggle) / [`<native:checkbox>`](../edge-components/checkbox) — boolean
- [`<native:slider>`](../edge-components/slider) — float
- [`<native:radio-group>`](../edge-components/radio-group) / [`<native:select>`](../edge-components/select) — string
- [`<native:chip>`](../edge-components/chip) / [`<native:button-group>`](../edge-components/button-group) — selection
- [`<native:tab-row>`](../edge-components/tab-row) — int (active index)

The bound value is coerced to match the control: a toggle syncs a `bool`, a slider a `float`, a tab row an `int`,
text and pickers a `string`. Declare your property with the matching type.

## Sync modifiers

By default the property updates on **every** change (each keystroke). Modifiers change *when* the sync fires —
useful for text inputs where syncing on every character is wasteful:

| Modifier | When it syncs |
| --- | --- |
| `native:model` (or `.live`) | On every change — the default. |
| `native:model.blur` | Only when the field loses focus. |
| `native:model.lazy` | Alias for `.blur`. |
| `native:model.debounce.300ms` | After the user stops changing it for the given delay. |

@verbatim
```blade
{{-- Sync only when the user leaves the field --}}
<native:text-input native:model.blur="email" />

{{-- Sync 500ms after typing stops --}}
<native:text-input native:model.debounce.500ms="search" />
```
@endverbatim

<aside>

There is no `.number` or `.defer` modifier — the value is always coerced by control type, and binding is never
deferred. The full set is `live` / `blur` / `lazy` / `debounce.<n>ms`.

</aside>

## Reacting to changes

When a bound property changes from the UI, the framework fires the matching
[`updated{Property}()`](lifecycle-hooks#updated-property) hook, passing the new value — the place to run
validation or trigger side effects:

```php
public string $search = '';
public array $results = [];

public function updatedSearch(string $value): void
{
    $this->results = Product::search($value)->get()->all();
}
```

A change also invalidates every [`#[Computed]`](lifecycle-hooks#related) value, so anything derived from the
property recomputes on the next frame. Together these let you keep components declarative: bind inputs to
properties, derive the rest with computed methods, and let the UI re-render itself.

## Without binding

`native:model` is sugar. If you'd rather handle the event yourself — to transform the value, or to bind to
something other than a public property — set the value and change handler directly:

@verbatim
```blade
<native:text-input :value="$name" @change="rename" />
```
@endverbatim

```php
public function rename(string $value): void
{
    $this->name = trim($value);
}
```
