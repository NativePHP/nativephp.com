---
title: List
order: 280
---

## Overview

A virtualized list container. On iOS, renders as a SwiftUI `List` with native pull-to-refresh and trailing
swipe-to-delete. On Android, renders as a `LazyColumn` / `LazyRow`.

Pair with [`<native:list-item>`](#list-item) for Material3 list rows, or use any EDGE element as a child.

@verbatim
```blade
<native:list separator on-refresh="refresh" on-end-reached="loadMore">
    @foreach($contacts as $contact)
        <native:list-item
            headline="{{ $contact->name }}"
            supporting="{{ $contact->email }}"
            leadingMonogram="{{ $contact->initials }}"
            trailingIcon="forward"
            @press="openContact({{ $contact->id }})"
        />
    @endforeach
</native:list>
```
@endverbatim

## Props

- `horizontal` - Lay out children horizontally instead of vertically (optional, boolean, default: `false`)
- `shows-indicators` - Show scroll indicators (optional, boolean, default: `false`) [iOS]
- `separator` - Render dividers between rows (optional, boolean, default: `false`) [iOS]
- `on-refresh` - Livewire method called on pull-to-refresh (optional, string) [iOS]
- `on-end-reached` - Livewire method called when the user nears the end of the list (optional, string)

## Children

Accepts any EDGE elements as children. `<native:list-item>` is the canonical child for Material3-style rows.

## List Item

A pre-styled Material3 row with a headline, optional supporting + overline text, and configurable leading + trailing
content slots.

@verbatim
```blade
<native:list-item
    headline="Inbox"
    supporting="42 unread"
    leadingIcon="email"
    trailingIcon="forward"
    @press="openInbox"
/>
```
@endverbatim

### Text props

- `headline` - Primary text (required, string)
- `supporting` - Secondary text rendered below the headline (optional, string)
- `overline` - Small caption rendered above the headline (optional, string)

### Leading slot (mutually exclusive)

- `leadingIcon` - Icon name rendered as a leading icon
- `leadingAvatar` - URL of a circular avatar image
- `leadingMonogram` - 1-2 character monogram (combine with `leadingMonogramColor`)
- `leadingMonogramColor` - Hex color for monogram background
- `leadingImage` - URL of a square image with a small radius
- `leadingCheckbox` - Boolean value for a leading checkbox
- `leadingRadio` - Boolean value for a leading radio button

### Trailing slot (mutually exclusive)

- `trailingIcon` - Icon name rendered as a trailing icon
- `trailingText` - Trailing text label
- `trailingCheckbox` - Boolean value for a trailing checkbox
- `trailingSwitch` - Boolean value for a trailing switch [Android]
- `trailingIconButton` - Icon name for a tappable trailing button

### Color overrides

- `headlineColor`, `supportingColor`, `overlineColor` - Hex colors for the text styles
- `containerColor` - Row background color
- `leadingIconColor`, `trailingIconColor`, `trailingTextColor` - Colors for the slot content

### State

- `disabled` - Disable the row (optional, boolean, default: `false`)
- `tonalElevation` - Tonal elevation in dp [Android]
- `shadowElevation` - Shadow elevation in dp [Android]

### Events

- `@press` / `@longPress` - Standard press handlers on the row
- `on-swipe-delete` - Livewire method invoked when the user swipes the row to delete [iOS]

## Examples

### Settings menu

@verbatim
```blade
<native:list separator>
    <native:list-item headline="Profile"       leadingIcon="person"        trailingIcon="forward" @press="openProfile" />
    <native:list-item headline="Notifications" leadingIcon="notifications" trailingIcon="forward" @press="openNotifications" />
    <native:list-item headline="Privacy"       leadingIcon="lock"          trailingIcon="forward" @press="openPrivacy" />
    <native:list-item headline="Help"          leadingIcon="help"          trailingIcon="forward" @press="openHelp" />
</native:list>
```
@endverbatim

### Swipe-to-delete with pull-to-refresh

@verbatim
```blade
<native:list separator on-refresh="refreshTasks">
    @foreach($tasks as $task)
        <native:list-item
            headline="{{ $task->title }}"
            supporting="{{ $task->due }}"
            leadingCheckbox="{{ $task->done }}"
            trailingIcon="forward"
            on-swipe-delete="deleteTask({{ $task->id }})"
            @press="openTask({{ $task->id }})"
        />
    @endforeach
</native:list>
```
@endverbatim

### Infinite scroll

@verbatim
```blade
<native:list on-end-reached="loadMore">
    @foreach($posts as $post)
        <native:list-item headline="{{ $post->title }}" supporting="{{ $post->excerpt }}" />
    @endforeach
</native:list>
```
@endverbatim

## Element

```php
use Nativephp\NativeUi\Elements\NativeList;
use Nativephp\NativeUi\Elements\ListItem;

NativeList::make(
    ListItem::make('Profile')->leadingIcon('person')->trailingIcon('forward'),
    ListItem::make('Settings')->leadingIcon('settings')->trailingIcon('forward'),
)
    ->separator()
    ->onRefresh('refresh')
    ->onEndReached('loadMore');
```

### `NativeList` methods

- `make(Element ...$children)` - Create a list with children
- `horizontal(bool $value = true)` - Horizontal layout
- `showsIndicators(bool $value = true)` - Show scroll indicators
- `separator(bool $value = true)` - Render dividers between rows
- `onRefresh(string $method)` - Pull-to-refresh handler
- `onEndReached(string $method)` - End-reached handler

### `ListItem` methods

Text:

- `make(string $headline = '')`, `supporting(string $text)`, `overline(string $text)`

Leading slot:

- `leadingIcon(string $icon)`
- `leadingAvatar(string $url)`
- `leadingMonogram(string $initials, ?string $color = null)`
- `leadingImage(string $url)`
- `leadingCheckbox(bool $checked = false)`
- `leadingRadio(bool $selected = false)`

Trailing slot:

- `trailingIcon(string $icon)`
- `trailingText(string $text)`
- `trailingCheckbox(bool $checked = false)`
- `trailingSwitch(bool $checked = false)`
- `trailingIconButton(string $icon)`

Styling:

- `headlineColor`, `supportingColor`, `overlineColor`, `containerColor`,
  `leadingIconColor`, `trailingIconColor`, `trailingTextColor` (all `(string $color)`)
- `tonalElevation(float $dp)`, `shadowElevation(float $dp)`

Callbacks:

- `onLeadingChange(string $method)`, `onTrailingChange(string $method)`,
  `onTrailingPress(string $method)`, `onSwipeDelete(string $method)`
- `disabled(bool $disabled = true)`
