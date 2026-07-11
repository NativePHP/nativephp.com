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

`<native:virtual-list />` is self-closing — the `item` view is the row template, not slot content.

## Windowing with the trait

Use the `HasVirtualListWindow` trait on your component. It supplies the `$virtualWindowFrom` / `$virtualWindowTo`
state (defaulting to a `0–79` first-paint window) and the `setVirtualWindow()` handler you point `on-window-change`
at:

```php
use Native\Mobile\Edge\Traits\HasVirtualListWindow;

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
```blade
{{-- resources/views/native/rows/contact.blade.php --}}
@php $contact = \App\Models\Contact::skip($index)->first(); @endphp

<native:list-item headline="{{ $contact->name }}" supporting="{{ $contact->email }}" />
```
@endverbatim

<aside>

One virtual list per screen for now. For small collections, plain [`<native:list>`](list) is simpler — reach for
virtualization when the row count is large enough that building them all would stutter.

</aside>
