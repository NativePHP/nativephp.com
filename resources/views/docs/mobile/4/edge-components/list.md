---
title: List
order: 270
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
- `separator` - Render dividers between rows (optional, boolean, default: `false`)
- `plain` - Force a flat, ungrouped list. By default a list containing `<native:list-section>` children adopts the
  inset-grouped style (rounded cards — iOS `.insetGrouped`, grouped cards on Android); `plain` keeps flat rows with
  plain section headers instead (optional, boolean, default: `false`)
- `on-refresh` - Component method called on pull-to-refresh (optional, string) [iOS]
- `on-end-reached` - Component method called when the user nears the end of the list (optional, string)

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

- `leadingIcon` - Icon name rendered as a leading icon. Pair with `leadingIconIos` / `leadingIconAndroid` to
  override the [icon](icon) per platform
- `leadingAvatar` - URL of a circular avatar image
- `leadingMonogram` - 1-2 character monogram (combine with `leadingMonogramColor`)
- `leadingMonogramColor` - Hex color for monogram background
- `leadingImage` - URL of a square image with a small radius
- `leadingCheckbox` - Boolean value for a leading checkbox. Interactive when `on-leading-change` is set —
  tapping the box fires your handler with the new value (the row's own `@press` still handles taps elsewhere
  on the row); without a handler it renders as a static state glyph
- `leadingRadio` - Boolean value for a leading radio button. Interactive when `on-leading-change` is set;
  static glyph otherwise

### Trailing slot (mutually exclusive)

- `trailingIcon` - Icon name rendered as a trailing icon. Pair with `trailingIconIos` / `trailingIconAndroid` to
  override the [icon](icon) per platform
- `trailingText` - Trailing text label
- `trailingCheckbox` - Boolean value for a trailing checkbox. Interactive when `on-trailing-change` is set;
  static glyph otherwise
- `trailingSwitch` - Boolean value for a trailing switch [Android]
- `trailingIconButton` - Icon name for a tappable trailing button
- `trailing-a11y-label` - Accessibility label for the trailing icon button (recommended whenever
  `trailingIconButton` is set). See [Accessibility](../digging-deeper/accessibility)
- `trailing-menu` - Attach a tap-to-open dropdown to the row's trailing edge. When set without an explicit trailing
  slot, an `ellipsis` icon button is auto-created as the anchor. See [Menus](menus)

Independent of the mutually-exclusive slot above, a row can also show a stack of small status icons:

- `trailing-badges` - An array of small status badges drawn right-aligned, so several can show at once (e.g. a
  flag and a pin). Each badge is `['icon' => ..., 'ios' => ..., 'android' => ..., 'color' => 'red-500']`, where
  `icon` is a shared [icon](icon) name, `ios` / `android` override it per platform, and `color` takes any
  [color value](../digging-deeper/theming#color-values).

### Color overrides

All color props accept the full [color grammar](../digging-deeper/theming#color-values) — hex (including
`#RRGGBBAA` alpha), Tailwind palette names, and `/N` opacity modifiers (`red-300/20`).

- `headlineColor`, `supportingColor`, `overlineColor` - Colors for the text styles
- `containerColor` - Row background color
- `leadingIconColor`, `trailingIconColor`, `trailingTextColor` - Colors for the slot content
- `leadingIconBgColor` - Background color of the leading icon's circle

### State

- `disabled` - Disable the row (optional, boolean, default: `false`)
- `tonalElevation` - Tonal elevation in dp [Android]
- `shadowElevation` - Shadow elevation in dp [Android]

### Events

- `@press` / `@longPress` - Standard press handlers on the row
- `on-swipe-delete` - Shortcut for a single destructive trailing swipe. For anything richer, use
  `trailing-actions` below.

- `on-leading-change` / `on-trailing-change` - Component method called when the leading/trailing checkbox or
  radio is toggled, receiving the new value. Without a handler the control renders as a static state glyph.

`onTrailingPress()` fires on both platforms when the trailing icon button is tapped. The trailing switch is
interactive on [Android] only.

### Swipe actions

Configure swipe actions on either edge with `leading-actions` and `trailing-actions` — each an array of action
definitions the user reveals by swiping the row. Each action is an array:

- `method` - Component method to call when the action is tapped (required)
- `label` - The action's text
- `ios` / `android` - The action [icon](icon), resolved per platform (or `icon` for a shared name)
- `tint` - Background color as a hex string
- `role` - Set to `destructive` to render in the platform's delete style (trailing only)

Swipe actions only work on rows that are direct children of `<native:list>` (or a `<native:list-section>`) —
they are attached by the list renderer, so a standalone `<native:list-item>` silently ignores them.

@verbatim
```blade
<native:list>
    @foreach ($emails as $email)
        <native:list-item
            :headline="$email->subject"
            :leading-actions="[
                ['method' => 'toggleRead('.$email->id.')', 'label' => 'Read', 'ios' => 'envelope.open', 'android' => 'mark_email_read', 'tint' => '#3B82F6'],
            ]"
            :trailing-actions="[
                ['method' => 'flag('.$email->id.')',   'label' => 'Flag',   'ios' => 'flag',  'android' => 'flag',   'tint' => '#F97316'],
                ['method' => 'delete('.$email->id.')', 'label' => 'Delete', 'ios' => 'trash', 'android' => 'delete', 'role' => 'destructive'],
            ]"
        />
    @endforeach
</native:list>
```
@endverbatim

## List Section

Group rows under a header (and optional footer) with `<native:list-section>` — a SwiftUI `Section` on iOS, a
sticky-header group on Android. Place `<native:list-item>` children inside; a section on its own renders nothing.

A list that contains sections automatically adopts the inset-grouped style (rounded cards); pass `plain` to the
list to keep flat rows with plain section headers instead.

@verbatim
```blade
<native:list>
    <native:list-section header="Fruits" footer="2 items">
        <native:list-item headline="Apple" />
        <native:list-item headline="Banana" />
    </native:list-section>
    <native:list-section header="Vegetables">
        <native:list-item headline="Carrot" />
    </native:list-section>
</native:list>
```
@endverbatim

- `header` - Section header text
- `footer` - Optional footer text below the section

```php
use Nativephp\NativeUi\Elements\ListSection;

ListSection::make('Fruits', ListItem::make('Apple'))->footer('1 item');
```

### `ListSection` methods

- `make(string $header = '', Element ...$children)` - Create a section with a header and rows
- `header(string $text)` - Set the section header text
- `footer(string $text)` - Set the optional footer text

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

The checkbox, swipe, and row press are three independent targets on one row: tapping the box fires
`on-leading-change`, swiping left fires `on-swipe-delete`, tapping anywhere else fires `@press`.

@verbatim
```blade
<native:list separator on-refresh="refreshTasks">
    @foreach($tasks as $task)
        <native:list-item
            headline="{{ $task->title }}"
            supporting="{{ $task->due }}"
            leadingCheckbox="{{ $task->done }}"
            on-leading-change="toggleTask({{ $task->id }})"
            trailingIcon="forward"
            on-swipe-delete="deleteTask({{ $task->id }})"
            @press="openTask({{ $task->id }})"
        />
    @endforeach
</native:list>
```
@endverbatim

> [!NOTE]
> Pull-to-refresh needs to own the pull gesture, so it only fires when the list is the screen's scrolling
> container — inside another scroll view (like this docs page) the outer container wins the pull. Swipe and
> checkbox work inline; run the refresh on a dedicated screen.

### Infinite scroll

`loadMore()` is a method on your component that fetches the next page and appends it to the collection the loop
renders — in a real app the loop is `@foreach ($posts as $post)` over your paginated results. The fixed `range()`
here just gives the demo enough rows to scroll before the end-reached trigger fires.

@verbatim
```blade
<native:list on-end-reached="loadMore">
    @foreach (range(1, 15) as $i)
        <native:list-item headline="Post {{ $i }}" supporting="Keep scrolling — loadMore() fires near the end" />
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

- `leadingIcon(?string $name = null, IosSymbol|string|null $ios = null, AndroidSymbol|string|null $android = null)`
- `leadingAvatar(string $url)`
- `leadingMonogram(string $initials, ?string $color = null)`
- `leadingImage(string $url)`
- `leadingCheckbox(bool $checked = false)`
- `leadingRadio(bool $selected = false)`

Trailing slot:

- `trailingIcon(?string $name = null, IosSymbol|string|null $ios = null, AndroidSymbol|string|null $android = null)`
- `trailingText(string $text)`
- `trailingCheckbox(bool $checked = false)`
- `trailingSwitch(bool $checked = false)`
- `trailingIconButton(?string $name = null, IosSymbol|string|null $ios = null, AndroidSymbol|string|null $android = null)`
- `trailingA11yLabel(string $label)` - Accessibility label for the trailing icon button

Swipe actions & badges:

- `leadingActions(array $actions)`, `trailingActions(array $actions)` - Arrays of swipe-action definitions
  (`method`, `label`, `ios`/`android`, `tint`, and `role` for trailing)
- `trailingBadges(array $badges)` - Stacked right-aligned status icons; each badge is
  `['icon' => ..., 'ios' => ..., 'android' => ..., 'color' => '#hex']`

Styling:

- `headlineColor`, `supportingColor`, `overlineColor`, `containerColor`,
  `leadingIconColor`, `leadingIconBackgroundColor`, `trailingIconColor`, `trailingTextColor` (all `(string $color)`)
- `tonalElevation(float $dp)`, `shadowElevation(float $dp)`

Callbacks:

- `onLeadingChange(string $method)`, `onTrailingChange(string $method)`,
  `onTrailingPress(string $method)`, `onSwipeDelete(string $method)`
- `disabled(bool $disabled = true)`
