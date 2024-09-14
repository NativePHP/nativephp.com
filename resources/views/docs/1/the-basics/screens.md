---
title: Screens
order: 850
---
# Screens

The `Screen` facade lets you get information about the screens currently connected to the computer.

```php
use Native\Laravel\Facades\Screen;
```

## Displays

The `displays` method gives you an array of information about all the displays actually used.

The Display object represents a physical display connected to the system. A fake display may exist on a headless system, or a display may correspond to a remote, virtual display. If you use an external display with your laptop screen closed, the internal screen of your laptop will not be part of the array.

See [Display object](https://www.electronjs.org/docs/latest/api/structures/display) documentation.

```php
$screens = Screen::displays();

[
  0 => [
    'bounds' => [
      'x' => 0,
      'y' => 0,
      'width' => 2560,
      'height' => 1440,
    ],
    'detected' => true,
    'id' => 2026675401,
    'internal' => false,
    'label' => 'U3277WB',
    'size' => [
      'width' => 2560,
      'height' => 1440,
    ],
    'workArea' => [
      'x' => 0,
      'y' => 25,
      'width' => 2560,
      'height' => 1345,
    ],
    // ... 
  ],
  // ... 
]
```

The screen bounds are the desktop area that the screen covers. The `x` and `y` values are the top-left corner of the screen relative to the primary display, and the `width` and `height` values are the width and height of the screen.


## Cursor position

The `cursorPosition` method gives you the coordinates of the current absolute position of the mouse cursor.

```php
$position = Screen::cursorPosition();

(object) [
   'x' => 627,
   'y' => 168,
]
```
The position of the cursor is relative to the top-left corner of the primary display. These values can be
negative as well as positive.

For example, a secondary display may be oriented by your system to the right of your primary display.
If your mouse cursor is on the secondary display when calling `Screen::cursorPosition()`, the `x` value will be a negative integer.
