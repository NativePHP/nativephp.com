---
title: Application Menu
order: 300
---

## Configuring the Application Menu

NativePHP allows you to configure the native menu of your application, as well as context menus or dock menus.
You can use the `Menu` facade which provides you with a single re-usable abstraction for building all of these menus.

The configuration of your application menu should happen in the `boot` method of your `NativeAppServiceProvider`.

### Creating a menu

To create a new menu, you may use the `Menu::new()` method. This method returns the menu builder, which allows you to add additional items or submenus.
Once you are done configuring the menu, you may call the `register()` method to register the menu with the native application.

```php
namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Menu\Menu;

class NativeAppServiceProvider
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Menu::new()
            ->appMenu()
            ->submenu('NativePHP', Menu::new()
                ->link('https://nativephp.com', 'Documentation')
            )
            ->register();

        Window::open();
    }
}
```

## Predefined menus

NativePHP comes with a few predefined menus that you can use out of the box. 


### The default application menu

You may use the `appMenu()` method to create the default application menu. This menu contains all the default items
that you would expect in an application menu (e.g. About, Services, Quit, etc.).

```php
Menu::new()
    ->appMenu()
    ->register();
```

The `appMenu` will use the name of your application as its title.

### The default edit menu

You may use the `editMenu()` method to create the default edit menu. This menu contains all the default items and their
functionality that you would expect in an edit menu (e.g. Undo, Redo, Cut, Copy, Paste, etc.).

> The `editMenu()` enables a number of common keyboard shortcuts (like cut, copy, paste). Without it, you will need
> to define these yourself.

```php
Menu::new()
    ->editMenu()
    ->register();
```

The edit menu uses "Edit" as its title by default. You may change this by passing a string to the `editMenu()` method.

```php
Menu::new()
    ->editMenu('My Edit Menu')
    ->register();
```

### The default view menu

You may use the `viewMenu()` method to create the default view menu. This menu contains all the default items and their
functionality that you would expect in a view menu (e.g. Toggle Fullscreen, Toggle Developer Tools, etc.).

```php
Menu::new()
    ->viewMenu()
    ->register();
```

The view menu uses "View" as its title by default. You may change this by passing a string to the `viewMenu()` method.

```php
Menu::new()
    ->viewMenu('My View Menu')
    ->register();
```

### The default window menu

You may use the `windowMenu()` method to create the default window menu. This menu contains all the default items and their functionality that you would expect in a window menu (e.g. Minimize, Zoom, etc.).

```php
Menu::new()
    ->windowMenu()
    ->register();
```

The window menu uses "Window" as its title by default. You may change this by passing a string to the `windowMenu()` method.

```php
Menu::new()
    ->windowMenu('My Window Menu')
    ->register();
```

### Combining the default menus

If you want to use multiple predefined menus, simply chain the methods together.

```php
Menu::new()
    ->appMenu()
    ->editMenu()
    ->viewMenu()
    ->windowMenu()
    ->register();
```

## Custom Submenus

You may use the `submenu()` method to create a submenu. This method accepts a title and a menu builder as its arguments.

```php
Menu::new()
    ->appMenu()
    ->submenu('My Submenu', Menu::new()
        ->link('https://nativephp.com', 'Documentation')
    )
    ->register();
```

## Available Submenu Items

### Labels

NativePHP allows you to add labels to your menus. You may use the `label()` method to add a label to your menu.
Clicking on a label will trigger the `Native\Laravel\Events\Menu\MenuItemClicked` event will be
[broadcast](/docs/digging-deeper/broadcasting).

```php
Menu::new()
    ->appMenu()
    ->submenu('My Submenu', Menu::new()
        ->label('My Label')
    )
    ->register();
```

### Links

You may add a link to your menu by using the `link()` method. This method accepts a URL and a title as its arguments.
When the user clicks on the link, the URL will be opened in the default browser and the
`Native\Laravel\Events\Menu\MenuItemClicked` event will be dispatched.

The payload of the event will contain the following data:

- `id`: The internal ID of the menu item.
- `label`: The label of the menu item.

```php
Menu::new()
    ->submenu('My Submenu', Menu::new()
        ->link('https://nativephp.com', 'Learn more')
    )
    ->register();
```

### Separators

You may add separators to your menu by using the `separator()` method.
A separator is a horizontal line that separates menu items.

```php
Menu::new()
    ->submenu('My Submenu', Menu::new()
        ->link('https://nativephp.com', 'Learn more')
        ->separator()
        ->link('https://nativephp.com', 'Documentation')
    )
    ->register();
```

### Event-based menu items

Event menu items automatically trigger the specified event when clicked.

```php
Menu::new()
    ->submenu('My Submenu', Menu::new()
        ->event(App\Events\MyEvent::class, 'Trigger my event')
    )
    ->register();
```


You may register listeners for your custom events in your `EventServiceProvider` class.

```php
/**
 * The event listener mappings for the application.
 *
 * @var array
 */
protected $listen = [
    App\Events\MyEvent::class => [
        'App\Listeners\MyMenuItemWasClicked',
    ],
    // ...
];
```

### Checkbox menu items

You may add a checkbox item to your menu by using the `checkbox()` method.
The `checkbox()` method accepts a label and a boolean value for the initial state of the checkbox as its arguments.

When the user clicks on the checkbox item, the checkbox will be toggled 
and the `Native\Laravel\Events\Menu\MenuItemClicked` event will be dispatched.

The payload of the event will contain the following data:

- `id`: The internal ID of the menu item.
- `checked`: Whether the checkbox is checked or not.
- `label`: The label of the menu item.

```php
Menu::new()
    ->submenu('My Submenu', Menu::new()
        ->checkbox('My Checkbox', true)
    )
    ->register();
```

### Quit

You may add a quit item to your menu by using the `quit()` method.
When the user clicks on the quit item, the application will quit.

```php
Menu::new()
    ->submenu('My Submenu', Menu::new()
        ->quit()
    )
    ->register();
```


## Hotkeys

NativePHP allows you to register hotkeys along with your menu items.
The `checkbox()`, `event()` and `link()` methods accept a hotkey as their last argument.

The hotkey must be a string that contains the modifiers and the key separated by a `+` sign.

For example, if you want to register a hotkey that triggers the `MyEvent` event when the user presses `Cmd+Shift+D`, you may do the following:

```php
Menu::new()
    ->submenu('My Submenu', Menu::new()
        ->event(App\Events\MyEvent::class, 'Trigger my event', 'CmdOrCtrl+Shift+D')
    )
    ->register();
```

You can find a list of available hotkey modifiers in the [global hotkey documentation section](/docs/the-basics/global-hotkeys#available-modifiers) 
