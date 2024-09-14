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
```

The screen bounds are the desktop area that the screen covers. The `x` and `y` values are the top-left corner of the screen relative to the primary display, and the `width` and `height` values are the width and height of the screen.

You can use this to find out which screen a particular window is located on:

```php
function getCurrentScreen()
{
    $screens = Screen::displays();
    $window = Window::current();

    foreach ($screens as $screen) {
        $bounds = $screen['bounds'];
        if ($window->x >= $bounds['x'] && $window->x <= $bounds['x'] + $bounds['width'] && $window->y >= $bounds['y'] && $window->y <= $bounds['y'] + $bounds['height']) {
            return $screen;
        }
    }

    return null;
}
```


## Cursor position

The `cursorPosition` method gives you the coordinates of the current absolute position of the mouse cursor.

The position of the cursor is relative to the top-left corner of the primary display. So if your external display is on the right of your laptop screen, the `x` value will be negative.
```php
$position = Screen::cursorPosition();

$position->x
$position->y
```
