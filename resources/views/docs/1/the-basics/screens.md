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

If you use an external display with your laptop screen closed, the internal screen of your laptop will not be part of the array.

See [Display object](https://www.electronjs.org/docs/latest/api/structures/display) documentation.

```php
$screens = Screen::displays();
```

## Cursor position

The `cursorPosition` method gives you the coordinates of the current absolute position of the mouse cursor.

```php
$position = Screen::cursorPosition();

$position->x
$position->y
```
