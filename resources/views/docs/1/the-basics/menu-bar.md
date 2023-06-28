---
title: Menu Bar
order: 200
---

## Working with the Menu Bar

NativePHP allows you to create a native application menu bar for your application.
This can be used as an addition to your existing application that already uses windows, or as a standalone (menu-bar only) application.

When the user clicks on the menu bar icon, the menu bar window will open and show the given URL or route.

The configuration of your MenuBar should happen in the `boot` method of your `NativeAppServiceProvider`.

### Creating a stand-alone Menu Bar application

To create a menu bar for your application, you may use the `MenuBar` facade. 
When creating the menu bar, NativePHP will automatically open the root URL of your application.
By default, adding a menu bar will automatically hide the dock icon of your application.

```php
namespace App\Providers;

use Native\Laravel\Facades\Window;

class NativeAppServiceProvider
{
    public function boot(): void
    {
        MenuBar::create();
    }
}
```

### Creating a Menu Bar for an application that already uses windows

You may also create a menu bar for an application that already uses windows. Usually you will want to show the 
dock icon of your application in this case.
To do so, you may use the `MenuBar::create()` method, but this time call the `showDockIcon()` method.

```php
namespace App\Providers;

use Native\Laravel\Facades\Window;

class NativeAppServiceProvider
{
    public function boot(): void
    {
        MenuBar::create()
            ->showDockIcon();
    }
}
```

### Opening the Menu Bar

You may use the `MenuBar::show()` method to manually open the menu bar window.

```php
MenuBar::show();
```

### Hiding the Menu Bar

You may use the `MenuBar::hide()` method to manually close the menu bar window.

```php
MenuBar::show();
```

### Menu Bar Labels

By default, the menu bar will only show the configured menu bar icon.
Additionally, you may add a label to the menu bar that will be shown next to the icon.

This label can be changed at any time by using the `label()` method.

```php
MenuBar::label('Status: Online');
```

You may also use the `label()` method while creating the menu bar to set the initial label.

```php
MenuBar::create()
    ->label('Status: Online');
```

To remove the label, you may pass an empty string to the `label()` method.

```php
MenuBar::label('');
```

## Configuring the Menu Bar

### Menu Bar URL

By default, the `MenuBar::create()` method will configure your menu bar to show the root URL of your application when clicked.
If you would like to open a different URL, you may use the `route()` method to specify the route name to open.

```php
MenuBar::create()
    ->route('home');
```

You may also pass an absolute URL to the `url()` method:

```php
MenuBar::create()
    ->url('https://google.com');
```

### Menu Bar Icon

The default menu bar icon is the NativePHP logo. You may change this icon by using the `icon()` method.
This method accepts an absolute path to an image file.

When providing an icon, you should make sure that the image is a PNG file with a transparent background.
The recommended size for the icon is **22x22 pixels**, as well as **44x44 pixels** for retina displays.

The file name for the retina display icon should be the same as the regular icon, but with `@2x` appended to the file name.

Example:

```text
menuBarIcon.png
menuBarIcon@2x.png
```

On macOS, it is recommended to use a so-called "Template Image".  
This is an image that is rendered as a white image with a transparent background.

NativePHP can automatically convert your image to a template image. To do so, you may name your image file with `Template` appended to the file name.

Example:

```text
menuBarIconTemplate.png
menuBarIconTemplate@2x.png
```

You do not need to manually append `@2x` to the file name, as NativePHP will automatically detect the retina display icon and use it when available.

```php
MenuBar::create()
    ->icon(storage_path('app/menuBarIconTemplate.png'));
```

### Menu Bar Window Sizes

The default size of the menu bar window is **400x400 pixels**.
You may use the `width()` and `height()` methods to specify the size of the window that will be opened when the user clicks on the menu bar icon.

```php 
MenuBar::create()
    ->width(800)
    ->height(600);
```

### Menu Bar on Top

When developing a menu bar application, you may want to make sure that the menu bar window is always open and on top of all other windows.
This makes it easier to develop your application, as you do not have to click on the menu bar icon every time you want to see the window.

To do so, you may use the `alwaysOnTop()` method on the `MenuBar`.

```php
MenuBar::create()
    ->alwaysOnTop();
```

## Menu Bar Context Menu

You may add a context menu to your menu bar icon. This context menu will be shown when the user right-clicks on the menu bar icon.

### Adding a Context Menu

To add a context menu, you may use the `contextMenu()` method on the `MenuBar`. 
This method accepts a `Native\Laravel\Menu\Menu` instance.

To learn more about the menu builder, please refer to the [Menu Builder](/docs/1/the-basics/menu-builder) documentation.

```php
MenuBar::create()
    ->withContextMenu(
        Menu::new()
            ->label('My Application')
            ->separator()
            ->link('https://nativephp.com', 'Learn more…')
            ->separator()
            ->quit()
    );
```

## Menu Bar Events

NativePHP provides a simple way to listen for menu bar events.
All events get dispatched as regular Laravel events, so you may use your `EventServiceProvider` to register listeners.

Sometimes you may want to listen and react to window events in real-time, which is why NativePHP also broadcasts all
window events to the `nativephp` broadcast channel.

To learn more about NativePHP's broadcasting capabilities, please refer to the [Broadcasting](/docs/broadcasting) section.

### Menu Bar Opened

The `Native\Laravel\Events\MenuBar\MenuBarShown` event will be dispatched when the user clicks on the menu bar icon and the menu bar window opens, or when
the menu bar gets shown by using the `MenuBar::show()` method.

### Menu Bar Closed

The `Native\Laravel\Events\MenuBar\MenuBarHidden` event will be dispatched when the user clicks out of the menu bar window and the menu bar window closes, or when
the menu bar gets hidden by using the `MenuBar::hide()` method.

### Menu Bar Context Menu Opened

The `Native\Laravel\Events\MenuBar\MenuBarContextMenuOpened` event will be dispatched when the user right-clicks on the menu bar icon and the context menu opens.
