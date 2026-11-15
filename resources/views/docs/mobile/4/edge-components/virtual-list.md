---
title: Virtual List
order: 455
---

## Overview

Renders only the visible slice of a large collection. Where [`<native:list>`](list) builds every row, a virtual
list emits just the rows inside the current window — so a 10,000-item list paints instantly and stays smooth,
because PHP only ever builds ~80 rows at a time. The native side fires a window-change callback as the user
scrolls, and the next render emits the new slice.

@verbatim
```blade
<native:virtual-list
    :count="$total"
    :from="$virtualWindowFrom"
    :to="$virtualWindowTo"
    item="native.rows.contact"
    on-window-change="setVirtualWindow" />
```
@endverbatim

## Props

- `count` - Total number of items in the full collection (required, int). Used for sizing and scroll extent.
- `from` - Absolute index of the first row to emit (optional, int, default: `0`).
- `to` - Absolute index of the last row to emit, inclusive (optional, int, default: `from + 29`). With both `from`
  and `to` omitted, the list emits rows 0–29 on first paint.
- `item` - Name of a Blade view rendered once per index in the window. It receives `['index' => $i]` (required,
  string).
- `on-window-change` - Component method called as the visible range moves, with `(int $from, int $to)`.
- `estimated-row-height` - Estimated row height in dp, used to size the scroll extent before rows measure
  (optional, float, default: `56`).
- `overscan` - Extra rows built beyond the visible window to smooth fast scrolling (optional, int, default: `30`).
- `auto-scroll-to` <x-docs.version-badge package="mobile-ui" since="0.9" /> - Absolute index of the row to open the
  list at. Negative values turn it off. See [Opening at a row](#opening-at-a-row) (optional, int).

`<native:virtual-list />` is self-closing — the `item` view is the row template, not slot content.

## Windowing with the trait

Use the `HasVirtualListWindow` trait on your component. It supplies the `$virtualWindowFrom` / `$virtualWindowTo`
state (defaulting to a `0–79` first-paint window) and the `setVirtualWindow()` handler you point `on-window-change`
at:

```php
use Native\Mobile\Edge\Concerns\HasVirtualListWindow;

class ContactsScreen extends NativeComponent
{
    use HasVirtualListWindow;

    public function render(): View
    {
        return view('native.contacts', [
            'total' => Contact::count(),
        ]);
    }
}
```

The row view renders one item by absolute index:

@verbatim
```blade static
{{-- resources/views/native/rows/contact.blade.php --}}
@php $contact = \App\Models\Contact::skip($index)->first(); @endphp

<native:list-item headline="{{ $contact->name }}" supporting="{{ $contact->email }}" />
```
@endverbatim

## Opening at a row

<x-docs.version-badge since="4.7" />

A virtual list draws only the rows inside its window, so to open it at a row, the window has to hold that row. Call
`scrollVirtualListTo()` from the trait, for example in `mount()`, and pass `$virtualScrollTo` to the list's
`auto-scroll-to`:

```php
public function mount(int $contact): void
{
    $this->scrollVirtualListTo($contact);
}
```

@verbatim
```blade static
<native:virtual-list
    :count="$total"
    :from="$virtualWindowFrom"
    :to="$virtualWindowTo"
    :auto-scroll-to="$virtualScrollTo"
    item="native.rows.contact"
    on-window-change="setVirtualWindow" />
```
@endverbatim

`scrollVirtualListTo(int $index)` moves the window to start a quarter of its size above the row, keeps its size, and
sets `$virtualScrollTo` to the row. `$virtualScrollTo` is `null` until you call it. The row is in the same render
that scrolls to it, so the list opens on the row itself rather than a placeholder.

`auto-scroll-to` behaves as it does on [`<native:list>`](list#opening-at-a-row): the list scrolls when it's created
and again when the index changes, and a re-render that keeps the same index leaves the list where the user scrolled
it.

<aside>

One virtual list per screen for now. For small collections, plain [`<native:list>`](list) is simpler — reach for
virtualization when the row count is large enough that building them all would stutter.

</aside>
