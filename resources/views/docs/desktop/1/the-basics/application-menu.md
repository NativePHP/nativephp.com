---
title: Application Menus
order: 300
---

## Configuring the Application Menu

NativePHP allows you to configure the native menu of your application, as well as context menus, MenuBar and Dock menus, using a
single, unified and expressive Menu API, available through the `Menu` facade. Use this for building all of your app's menus.

```php
use Native\Laravel\Facades\Menu;
```

The configuration of your application menu should happen in the `boot` method of your `NativeAppServiceProvider`.

### Creating the menu

To create a new application menu, you may use the `Menu::create()` method. This method creates _and registers_ your
menu in one step.

You can customize the items that appear in the menu by passing them as parameters to the `create` method:

```php
namespace App\Providers;

use Native\Laravel\Facades\Menu;
use Native\Laravel\Facades\Window;

class NativeAppServiceProvider
{
    public function boot(): void
    {
        Menu::create(
            Menu::app(), // Only on macOS
            Menu::file(),
            Menu::edit(),
            Menu::view(),
            Menu::window(),
        );

        Window::open();
    }
}
```

### The Default menu

You may use the `Menu::default()` method to create the default application menu. This menu contains all the items that
you would expect in a typical application menu (File, Edit, View, Window):

```php
// Instead of...
Menu::create(
    Menu::app(),
    Menu::file(),
    Menu::edit(),
    Menu::view(),
    Menu::window(),
);

// You can just write...
Menu::default();
```

### Recreating the menu

It's sometimes desirable to update the main application menu with a brand new configuration in response to changes in your
application, making it contextually sensitive, perhaps based on which window currently has focus.

You can update your application menu at any time simply by calling `Menu::create()` again with your desired menu structure.
This might be in an event listener, a controller action or even a Livewire action method.

## Predefined menus

NativePHP comes with a few predefined menus that you can use out of the box. These are a convenience and, for the most
part, the only thing that can be changed about them is their label.

You may change this by passing a string as the first parameter to each method, for example:

```php
Menu::edit('My Edit Menu')
```

**The default menus enable a number of common keyboard shortcuts ("hotkeys"), such as those typically used for cut,
copy, and paste. If you decide to build custom versions of these menus, you will need to explicitly define these
shortcuts yourself.**

**On macOS, the first item in your application menu will _always_ use the name of your application as its label,
overriding any custom label you set, regardless of which type of submenu you place first.**


### The App menu

You may use the `Menu::app()` method to create the default application menu. This menu contains all the items that
you would expect in an application menu (e.g. About, Services, Quit, etc.).

```php
Menu::create(
    Menu::app(),
);
```

**The app menu is only available for macOS. It is typically used as the first menu in your application's menu.**

### The File menu

You may use the `Menu::file()` method to create the default file menu. This menu contains items and functionality that
you would expect in a file menu (e.g. Close/Quit).

```php
Menu::create(
    Menu::app(),
    Menu::file(),
);
```

The file menu uses "File" as its label by default.

### The Edit menu

You may use the `Menu::edit()` method to create the default edit menu. This menu contains all the items and
functionality that you would expect in an edit menu (e.g. Undo, Redo, Cut, Copy, Paste, etc.).

```php
Menu::create(
    Menu::app(),
    Menu::edit(),
);
```

The edit menu uses "Edit" as its label by default.

### The View menu

You may use the `Menu::view()` method to create the default view menu. This menu contains all the default items and
functionality that you would expect in a view menu (e.g. Toggle Fullscreen, Toggle Developer Tools, etc.).

```php
Menu::create(
    Menu::app(),
    Menu::view(),
);
```

The view menu uses "View" as its label by default.

### The Window menu

You may use the `Menu::window()` method to create the default window menu. This menu contains all the default items and
functionality that you would expect in a window menu (e.g. Minimize, Zoom, etc.).

```php
Menu::create(
    Menu::app(),
    Menu::window()
);
```

The window menu uses "Window" as its label by default.

## Custom Submenus

You may use the `Menu::make()` method to build a custom menu. Rather than registering this menu as the main application
menu, the `make()` method returns an instance of the `Native\Laravel\Menu\Menu` object, which you can pass into places
where `Menu` instances are accepted.

`Menu` instances are also a `MenuItem`, so they can be nested within other menus to create submenus:

```php
Menu::create(
    Menu::app(),
    Menu::make(
        Menu::link('https://nativephp.com', 'Documentation'),
    )->label('My Submenu')
);
```

## Menu Items

![Menu items](/img/docs/custom-menus.png)

NativePHP provides a range of menu items that you can use in your menus, all accessible from the `Menu` facade:

```php
Menu::make(
    Menu::checkbox(string $label, bool $checked = false, ?string $hotkey = null),
    Menu::label(string $label, ?string $hotkey = null),
    Menu::link(string $url, ?string $label = null, ?string $hotkey = null),
    Menu::radio(string $label, bool $checked = false, ?string $hotkey = null),
    Menu::route(string $route, ?string $label = null, ?string $hotkey = null),
);
```

Each is a subclass of the `Native\Laravel\Menu\Items\MenuItem` class which provides many useful methods to help you
build the perfect menu:

```php
$item = Menu::route('welcome')
    ->label('Home')
    ->id('my-item')
    ->icon(public_path('path/to/icon.png'))
    ->visible(false)
    ->tooltip('Hover text FTW!') // macOS only
    ->hotkey('Cmd+F')
    ->disabled();
```

Other methods are available depending on the type of menu item.

### Handling clicks

Almost all menu items will fire an event when clicked or by pressing their hotkey combo. You may decide which event is
fired by chaining the `event()` method to the menu item:

```php
Menu::label('Click me!')
    ->event(MyCustomMenuItemEvent::class)
```

Your custom event class should extend the default `Native\Laravel\Events\Menu\MenuItemClicked` class.

If you do not provide a custom event to fire, the default event will be used. By default, this event is
[broadcast](/docs/digging-deeper/broadcasting) across your app so you can listen for it either in your Laravel back-end,
via Javascript in your windows, or both.

The click event receives details of the menu item that was clicked, as well as an array of combo keys that may have
been pressed at the time the item was clicked.

### Hotkeys

Hotkeys can be defined for all menu items, either via the `hotkey` parameter of the respective `Menu` facade method or
by using the `hotkey()` chainable method:

```php
Menu::label('Quick search', hotkey: 'Ctrl+K');

// Or

Menu::label('Quick search')->hotkey('Ctrl+K');
```

You can find a list of available hotkey modifiers in the
[global hotkey documentation section](/docs/the-basics/global-hotkeys#available-modifiers).

Unlike global hotkeys, hotkeys registered to menu items will only be fired when one of your application's windows are
focused or the relevant context menu is open.

When a menu item is fired from a hotkey combo press, the event's `$combo` parameter will have its `triggeredByAccelerator`
value set to `true`.

### Label items

The simplest menu item is just a label. You may use the `Menu::label()` method to add a label item to your menu:

```php
Menu::make(
    Menu::label('Support'),
);
```

These are great when you want your app to do something in response to the user clicking the menu item or pressing
a hotkey combo.

### Link items

Link items allow you to define navigational elements within your menus. These can either navigate users to another URL
within your application or to an external page hosted on the internet.

You may add a link to your menu by using the `Menu::link()` method:

```php
Menu::link('/login', 'Login');
```

This will navigate the currently-focused window to the URL provided.

You may use the `Menu::route()` method as a convenience to map to a
[named route](https://laravel.com/docs/routing#named-routes):

```php
Menu::route('login', 'Login');
```

When combined with the `openInBrowser()` method, Link items are great for creating links to external websites that you
would like to open in the user's default web browser:

```php
Menu::link('https://nativephp.com/', 'Documentation')
    ->openInBrowser();
```

**You should never open untrusted external websites within your application's windows. If you're not very careful, you
may introduce serious vulnerabilities onto your user's device.**

### Checkbox and Radio items

In some cases, your app may not require a preferences panel, and a few interactive menu items may suffice to allow
your user to configure some settings. Or you may wish to make certain commonly-used settings more readily accessible.

Checkbox and Radio items enable you to create menu items for just these purposes. They operate in a very similar way
to checkboxes and radio buttons in a web form. Their default state is 'unchecked'.

You may use the `Menu::checkbox()` and `Menu::radio()` methods to create such items, passing the initial state of the
item to the `checked` parameter or using the `checked()` chainable method:

```php
Menu::checkbox('Word wrap', checked: true);

// Or

Menu::checkbox('Word wrap')->checked();
```

When Checkbox and Radio items are triggered, the click event data will indicate whether or not the item is currently
checked via the `$item['checked']` value.

#### Radio groups

Unlike radio buttons in HTML forms, Radio menu items are not grouped by their name; they are grouped logically with
all other radio items in the same menu.

However, you _can_ have separate groups of radio buttons within the _same_ menu if you separate them with a
[separator](#separators):

```php
Menu::make(
    Menu::radio('Option 1'),
    Menu::radio('Option 2'),
    Menu::separator(),
    Menu::radio('Option 1'),
    Menu::radio('Option 2'),
);
```

These two radio groups will operate independently of each other.

## Special Menu Items

NativePHP also ships with a number of "special" menu items that provide specific behavior for you to use in your menus.

These items usually have default labels and hotkeys associated with them and provide the basic, default functionality
commonly associated with them in any web browser. Therefore, they do not fire any click events.

You may only override their labels.

### Separators
You may add separators to your menu by using the `Menu::separator()` method.

A separator is a horizontal line that visually separates menu items.

```php
Menu::make(
    Menu::link('https://nativephp.com', 'Learn more'),
    Menu::separator(),
    Menu::link('https://nativephp.com/docs/', 'Documentation'),
);
```

As already noted, they also aid in logically grouping radio items.

### Undo and Redo
If you have chosen not to include the [default Edit menu](#the-edit-menu) in your application menu,
you may add the default undo and redo functionality to your app by using the `Menu::undo()` and
`Menu::redo()` methods.

```php
Menu::make()
    Menu::undo(),
    Menu::redo(),
);
```

**These standard actions work well with text input from the user provided via standard `input` or `textarea` elements,
but for more complex undo/redo workflows, you may wish to implement your own logic. In which case, you should not use
these items.**

### Cut, Copy, and Paste
If you have chosen not to include the [default Edit menu](#the-edit-menu) in your application menu,
you may add the default cut, copy and paste functionality to your app by using the `Menu::cut()`, `Menu::copy()` and
`Menu::paste()` methods.

```php
Menu::make()
    Menu::cut(),
    Menu::copy(),
    Menu::paste(),
);
```

**These standard actions work well with text input from the user provided via standard `input` or `textarea` elements,
but for more complex cut, copy and paste workflows, you may wish to implement your own logic. In which case, you should
not use these items.**

### Fullscreen
You may add a fullscreen item to your menu by using the `Menu::fullscreen()` method.

When the user clicks on the fullscreen item, the application will attempt to enter fullscreen mode. This will only work
if your currently-focused window is [fullscreen-able](/docs/the-basics/windows#full-screen-windows).

```php
Menu::make()
    Menu::fullscreen('Supersize me!'),
);
```

### Minimize
You may add a minimize item to your menu by using the `Menu::minimize()` method.

When the user clicks on the minimize item, the currently-focused window will be minimized.

```php
Menu::make()
    Menu::minimize(),
);
```

### Quit
You may add a quit item to your menu by using the `Menu::quit()` method.

When the user clicks on the quit item, the application will attempt to quit.

```php
Menu::make()
    Menu::quit(),
);
```

## Context Menu

You may need to add custom context menu to the elements in the views of your application and override the default one.

You can use the `Native` JavaScript object provvided by NativePHP's preload script.

This object exposes the `contextMenu()` method which takes an array of objects that matches the 
[MenuItem](https://www.electronjs.org/docs/latest/api/menu-item) constructor's `options` argument.

```js
Native.contextMenu([
    {
        label: 'Edit',
        accelerator: 'e',
        click(menuItem, window, event) {
            // Code to execute when the menu item is clicked
        },
    },
    // Other options
]);
```

You can use the `contextmenu` event to capture the user's action and show your menu:

```js
const element = document.getElementById('your-element');

element.addEventListener('contextmenu', (event) => {
    event.preventDefault();

    Native.contextMenu([
        {
            label: 'Duplicate',
            accelerator: 'd',
            click() {
                duplicateEntry(element.dataset.id);
            },
        },
        {
            label: 'Edit',
            accelerator: 'e',
            click() {
                showEditForm(element.dataset.id);
            },
        },
        {
            label: 'Delete',
            click() {
                if (confirm('Are you sure you want to delete this entry?')) {
                    deleteEntry(element.dataset.id);
                }
            },
        },
    ]);
});
```
