---
title: Emoji Picker
order: 221
---

## Overview

The platform's own emoji chooser, shown while `visible` is true, so people get search, recently used emoji and skin
tones without your app drawing a grid of its own. It takes no space in the layout, so it can sit anywhere on a screen.

- **iOS** has no public emoji picker view, so the element raises the system emoji keyboard on a hidden field, with a
  Done bar above it. It's the same keyboard Messages opens for an emoji Tapback.
- **Android** shows Jetpack's `EmojiPickerView` (androidx.emoji2) in a Material 3 bottom sheet: categories, recently
  used, and skin-tone, gender and direction variants on a long press.

@verbatim
```blade
@php
    $picking = false;
    $emoji = '🙂';
@endphp

<native:column class="w-full gap-3 items-start">
    <native:text class="text-4xl">{{ $emoji }}</native:text>
    <native:button label="Choose an emoji" @press="$picking = true" />

    <native:emoji-picker :visible="$picking" @change="pick" @dismiss="$picking = false" />
</native:column>
```
@endverbatim

```php
public bool $picking = false;

public string $emoji = '🙂';

public function pick(string $emoji): void
{
    $this->emoji = $emoji;
    $this->picking = false;
}
```

## Props

- `visible` - Whether the picker is shown (required, boolean)
- `done-label` - The button on the bar above the iOS emoji keyboard that closes it (optional, default: `"Done"`).
  Android closes its sheet with a swipe down or back, so it has no button
- `a11y-label` - Accessibility label (optional)
- `a11y-hint` - Accessibility hint (optional)

## Events

- `@change` - Component method called with each emoji picked, as a string
- `@dismiss` - Component method called when the person closes the picker: Done or a tap elsewhere on iOS, a swipe
  down, a tap on the scrim or back on Android. Setting `visible` to false from PHP never calls it

## Closing it

The picker stays up until you set `visible` to false. Close it in your `@change` handler for a single pick, or leave
it open to collect several, for instance to add emoji to a draft:

@verbatim
```blade
<native:emoji-picker :visible="$addingEmoji" @change="addToDraft" @dismiss="$addingEmoji = false" />
```
@endverbatim

```php
public function addToDraft(string $emoji): void
{
    $this->draft .= $emoji;
}
```

<aside>

Always handle `@dismiss` and set `visible` back to false there. If you don't, your component still thinks the picker
is open after the person has closed it, and setting `visible` to true again won't reopen it.

</aside>

## Checking what was picked

The emoji is whatever the platform hands over. It can carry a skin-tone modifier (U+1F3FB to U+1F3FF) or a variation
selector (U+FE0F), be a ZWJ sequence, or be newer than the list your server accepts. If a server only takes certain
emoji, check the pick before you send it: strip skin tones, match with and without U+FE0F, and tell the person when an
emoji can't be used rather than sending it.

On iOS, if someone has removed the emoji keyboard in Settings, they get their default keyboard instead, and anything
they type that isn't an emoji is ignored.

## Testing

`@change` rides on the same event as `<native:select>`, so tests pick with `select()` and close with `dismissSheet()`,
both by method name or by the element's `ref`:

```php
Native::test(Reactions::class)
    ->set('picking', true)
    ->select('pick', '🎉')
    ->assertSet('emoji', '🎉')
    ->assertSet('picking', false);
```

## Element

```php
use Native\Mobile\UI\Elements\EmojiPicker;

EmojiPicker::make()
    ->visible($picking)
    ->doneLabel('Close')
    ->onChange('pick')
    ->onDismiss('closePicker');
```

- `make()` - Create an emoji picker
- `visible(bool $value = true)` - Toggle visibility
- `doneLabel(string $label)` - The iOS Done bar's button
- `a11yLabel(string $value)` - Accessibility label
- `a11yHint(string $value)` - Accessibility hint
- `onChange(string $method)` - Component method invoked with each emoji picked
- `onDismiss(string $method)` - Component method invoked when the person closes it
