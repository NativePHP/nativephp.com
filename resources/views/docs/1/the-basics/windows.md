---
title: Windows
order: 100
---

## Working with Windows

NativePHP allows you to open native application Windows. 
While this usually happens in your `NativeAppServiceProvider`, you are free to open a window anywhere in your application.

### Opening Windows

To open a window, you may use the `Window` facade.

```php
namespace App\Providers;

use Native\Laravel\Facades\Window;

class NativeAppServiceProvider
{
    public function boot(): void
    {
        Window::open()
            ->width(800)
            ->height(800);
    }
}
```

When opening a window, NativePHP will automatically open the root URL of your application.
You may pass a unique identifier to the `open()` method to distinguish between multiple windows.

The default ID, if none is specified, is `main`.

You can use the ID to reference the window in other methods, such as `Window::close()` or `Window::resize()`.

### Closing Windows

To close a window, you may use the `Window::close()` method.

You may pass a unique identifier to the `close()` method to specify which window to close.

If you do not specify a window ID, NativePHP will try to detect the window ID automatically based on the current route.

```php
Window::close();
Window::close('settings');
```

### Resizing Windows

You may use the `Window::resize()` method to resize a window. This method accepts a width and height as its first and second arguments, respectively.

You may pass a unique identifier to the `resize()` method to specify which window to resize.

If you do not specify a window ID, NativePHP will try to detect the window ID automatically based on the current route.

```php
Window::resize(400, 300);

Window::resize(400, 300, 'settings');
```

### Minimizing and Maximizing

There are convenience methods that allow you to minimize and maximize windows.

#### Minimize a Window

To maximize a window, you may use the `Window::minimize()` method.

You may pass the window ID to the `minimize()` method to specify which window to minimize.

If you do not specify a window ID, NativePHP will try to detect the window ID automatically based on the current route.

```php
Window::open('secondary');

// Later...

Window::minimize('secondary');
```

#### Maximize a Window

To maximize a window, you may use the `Window::maximize()` method.

You may pass the window ID to the `maximize()` method to specify which window to maximize.

If you do not specify a window ID, NativePHP will try to detect the window ID automatically based on the current route.

```php
Window::open('secondary');

// Later...

Window::maximize('secondary');
```

Of course, you may also wish to open windows in a minimized or maximized state. You can achieve this simply by chaining the
`minimized()` and `maximized()` methods to your `Window::open()` call:

```php
Window::open()
    ->maximized();
```

### Retrieving the Current Window

You may use the `Window::current()` method to retrieve the currently focused window.
This method returns an object with the following properties:

- `id`: The ID of the window.
- `title`: The title of the window.
- `width`: The width of the window.
- `height`: The height of the window.
- `x`: The x position of the window.
- `y`: The y position of the window.
- `alwaysOnTop`: Whether the window is always on top.

```php
$currentWindow = Window::current();
```

## Managing Multiple Windows

If you would like to open multiple windows, you may use the `Window::open()` method multiple times.
In order to distinguish between the individual windows, you may pass a unique identifier to the `open()` method.

If you do not specify an ID, NativePHP will automatically use `main` as the ID.

This ID can be used to reference the window in other methods, such as `Window::close()` or `Window::resize()`.

```php
Window::open('home')
    ->width(800)
    ->height(800);

Window::open('settings')
    ->route('settings')
    ->width(800)
    ->height(800);
```

## Configuring Windows
### Window URLs

By default, all calls to `Window::open()` will open up the root URL of your application.
If you would like to open a different URL, you may use the `route()` method to specify the route name to open.

```php
Window::open()
    ->route('home');
```

You may also pass an absolute URL to the `url()` method:

```php
Window::open()
    ->url('https://google.com');
```

### Window Titles

By default, all calls to `Window::open()` will use the application name as the window title.
If you would like to use a different title, you may use the `title()` method to specify the window title to use.

```php
Window::open()
    ->title('My Window');
```

### Window Sizes

You may use the `width()` and `height()` methods to specify the size of the window.

```php
Window::open()
    ->width(800)
    ->height(800);
```

If you want to constrain the window to a specific size, you may make use of the `minWidth()`, `minHeight()`,
`maxWidth()`, and `maxHeight()` methods.

```php
Window::open()
    ->minWidth(400)
    ->minHeight(400)
    ->maxWidth(800)
    ->maxHeight(800);
```

### Window Position

To specify the position of the window, you may use the `position($x, $y)` method.

```php
Window::open()
    ->position(100, 100);
```

### Remembering Window State

The users of your application may resize or move the window and expect it to be in the same position and size the next
time they open it. NativePHP provides a simple way to manage the state of your window. You may use the `rememberState()`
method to instruct NativePHP to remember the state of the window.

```php
Window::open()
    ->rememberState();
```

Please note that NativePHP only allows you to remember the state of one window at a time.

### Resizable Windows

By default, all windows created with the `Window` facade are resizable. 
If you would like to disable resizing, you may use the `resizable()` method and pass `false` as the first argument.

```php
Window::open()
    ->resizable(false);
```

### Focusable Windows

By default, all windows created with the `Window` facade are focusable by clicking on them.
You may use the `focusable()` method to disable focusing.

```php
Window::open()
    ->focusable(false);
```

### Movable Windows

By default, all windows created with the `Window` facade are movable.

You may use the `movable()` method to disable moving.

```php
Window::open()
    ->movable(false);
```

### Minimizable, Maximizable, and Closable Windows

By default, all windows created with the `Window` facade are minimizable, maximizable, and closable.

You may use the `minimizable()`, `maximizable()`, and `closable()` methods to disable these features.

```php
Window::open()
    ->minimizable(false)
    ->maximizable(false)
    ->closable(false);
```

### Full Screen Windows

By default, all windows created with the `Window` facade are fullscreen-able, meaning that they can enter Full Screen Mode.

You may use the `fullscreenable()` method to disable this feature.

```php
Window::open()->fullscreenable(false);
```

If you wish, you may open a window in full screen mode using the `fullscreen()` method.

```php
Window::open()->fullscreen();
```

### Window Shadow

By default, all windows created with the `Window` facade have a shadow. You may use the `hasShadow()` method to disable the shadow.

```php
Window::open()
    ->hasShadow(false);
```

### Windows on Top

In some cases, you may want to make a window always on top of other windows.
When opening a window, you may use the `alwaysOnTop()` method to make the window always on top.

```php
Window::open()
    ->alwaysOnTop();
```

If you would like to toggle the always on top state of a window, you may use the `alwaysOnTop()` method on the `Window` facade 
directly and pass the window ID as the second argument.

If you do not specify a window ID, NativePHP will try to detect the window ID automatically based on the current route.

```php
Window::alwaysOnTop(true, 'settings');
```

### Window Background Color

By default, all windows created with the `Window` facade have a white background color. 
This color is visible when resizing the window, right before the content is rendered.

You may use the `backgroundColor()` method to change the background color of the window.
This method accepts a hex color code as its first argument.  
You may also pass a hex color code with an alpha channel to make the background color semi-transparent. 

```php
Window::open()
    ->backgroundColor('#00000050'); // Semi-transparent black
```

### Hiding the menu

By default on Windows and Linux the application menu will be visible.
This method will hide the menu and have it reveal when the user presses ALT.

```php
Window::open()
    ->hideMenu();
```

## Window Title Styles

### Default Title Style

By default, all windows created with the `Window` facade show their title in the center of the title bar.

### Hidden Title Style

You may use the `titleBarHidden()` method to hide the title bar of a window.

```php
Window::open()
    ->titleBarHidden();
```

When using this style, you may want to add a custom title bar to the window yourself via HTML/JS.

In order to keep the window draggable, you should add an HTML element with the following CSS attributes:

```html
<div style="height: 30px; -webkit-app-region: drag;">
    <!-- Your Custom Title Content -->
</div>
```

## Events

NativePHP provides a simple way to listen for native window events.
All events get dispatched as regular Laravel events, so you may use your `EventServiceProvider` to register listeners.

```php
protected $listen = [
    'Native\Laravel\Events\Windows\WindowShown' => [
        'App\Listeners\WindowWasShownListener',
    ],
    // ...
];
```

Sometimes you may want to listen and react to window events in real-time, which is why NativePHP also broadcasts all
window events to the `nativephp` broadcast channel. 

To learn more about NativePHP's broadcasting capabilities, please refer to the [Broadcasting](/docs/digging-deeper/broadcasting) section.

### WindowShown

The `Native\Laravel\Events\Windows\WindowShown` event will be dispatched when a window is shown to the user.
The payload of this event contains the window ID.

### WindowClosed

The `Native\Laravel\Events\Windows\WindowClosed` event will be dispatched when a window is closed.
The payload of this event contains the window ID.

### WindowFocused

The `Native\Laravel\Events\Windows\WindowFocused` event will be dispatched when a window is focused.
The payload of this event contains the window ID.

### WindowBlurred

The `Native\Laravel\Events\Windows\WindowBlurred` event will be dispatched when a window is blurred.
The payload of this event contains the window ID.

### WindowMinimized

The `Native\Laravel\Events\Windows\WindowMinimized` event will be dispatched when a window is minimized.
The payload of this event contains the window ID.

### WindowMaximized

The `Native\Laravel\Events\Windows\WindowMaximized` event will be dispatched when a window is maximized.
The payload of this event contains the window ID.

### WindowResized

The `Native\Laravel\Events\Windows\WindowResized` event will be dispatched after a window has been resized.
The payload of this event contains the window ID and the new window `$width` and `$height`.

