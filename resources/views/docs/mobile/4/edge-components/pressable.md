---
title: Pressable
order: 250
---

## Overview

A touch-sensitive container that wraps its children in a tappable area. Pressable is structurally identical to a column
but is specifically designed for handling tap and long-press gestures on a group of elements.

While any element can handle `@press` and `@longPress` events, `<native:pressable>` makes the intent explicit and
provides a clear tap target that wraps multiple children.

@verbatim
```blade
<native:pressable @press="selectItem({{ $item->id }})" class="w-full p-4 rounded-xl" bg="#FFFFFF">
    <native:text class="text-lg font-semibold">{{ $item->name }}</native:text>
    <native:text class="text-sm text-slate-400">{{ $item->description }}</native:text>
</native:pressable>
```
@endverbatim

## Props

All [shared layout and style attributes](layout) are supported. There are no pressable-specific props beyond the
standard event handlers.

## Events

- `@press` - Livewire method to call on tap
- `@longPress` - Livewire method to call on long press

## Children

Accepts any EDGE elements as children. Children are arranged vertically (like a column).

## Examples

### Tappable list item

@verbatim
```blade
@foreach($items as $item)
    <native:pressable
        @press="selectItem({{ $item->id }})"
        class="w-full px-4 py-3"
    >
        <native:row :gap="12" :align-items="1">
            <native:icon name="folder" :size="24" color="#3B82F6" />
            <native:column :flex-grow="1" :gap="2">
                <native:text class="text-base font-medium">{{ $item->name }}</native:text>
                <native:text class="text-sm text-slate-400">{{ $item->subtitle }}</native:text>
            </native:column>
            <native:icon name="forward" :size="16" color="#94A3B8" />
        </native:row>
    </native:pressable>
    @unless($loop->last)
        <native:divider />
    @endunless
@endforeach
```
@endverbatim

### Card with tap and long press

@verbatim
```blade
<native:pressable
    @press="openDetail({{ $post->id }})"
    @longPress="showOptions({{ $post->id }})"
    class="w-full p-4 rounded-2xl gap-2"
    bg="#FFFFFF"
    :elevation="2"
>
    <native:text class="text-lg font-bold">{{ $post->title }}</native:text>
    <native:text class="text-base text-slate-500">{{ $post->excerpt }}</native:text>
</native:pressable>
```
@endverbatim

### Navigation with @navigate

@verbatim
```blade
<native:pressable @navigate="/detail/{{ $item->id }}" class="w-full p-4">
    <native:text class="text-base">{{ $item->name }}</native:text>
</native:pressable>
```
@endverbatim

## Element

```php
use Native\Mobile\Edge\Elements\Pressable;

Pressable::make($child1, $child2)->onPress('handleTap');
```

- `make(Element ...$children)` - Create a pressable with children. Inherits the standard layout / style API from
  the base `Element` class
