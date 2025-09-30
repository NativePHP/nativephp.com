---
title: Global Hotkeys
order: 600
---

## Global Hotkeys 

In your NativePHP application, you may define multiple global hotkeys.
Unlike hotkeys that you may define in your application via JavaScript, these hotkeys are globally registered.
This means that your application may be aware of these hotkeys being triggered even when it is running in the
background and not focused.

As these global hotkeys are usually used in your entire application, a common approach to registering them is inside
the `NativeAppServiceProvider` class.

```php
namespace App\Providers;

use Native\Laravel\Facades\GlobalShortcut;

class NativeAppServiceProvider
{
    public function boot(): void
    {
        GlobalShortcut::key('CmdOrCtrl+Shift+A')
            ->event(\App\Events\MyShortcutEvent::class)
            ->register();
            
        // Additional code, such as registering a menu, opening windows, etc.
    }
}
```

## Registering Hotkeys

You may register a global shortcut using the `GlobalShortcut` facade.
Using the `key` method, you may specify the hotkey to listen for. The hotkey must be a string that contains the
modifiers and the key separated by a `+` sign.

For example, if you want to register a hotkey that triggers the `MyEvent` event when the user presses `Cmd+Shift+D`,
you may do the following:

```php
GlobalShortcut::key('Cmd+Shift+D')
    ->event(\App\Events\MyEvent::class)
    ->register();
```

You can find a list of all available modifiers [here](#available-modifiers).

## Removing registered hotkeys

Sometimes you may want to remove an already registered global hotkey.
To do this, specify the hotkey that you used to register and call the `unregister` method on the `GlobalShortcut` facade.
You do not need to provide an event class in this case, as every hotkey can only be registered once.

For example, in order to remove the `Cmd+Shift+D` global hotkey, you may do the following:

```php
GlobalShortcut::key('Cmd+Shift+D')
    ->unregister();
```

### Available modifiers
* `Command` or `Cmd`
* `Control` or `Ctrl`
* `CommandOrControl` or `CmdOrCtrl`
* `Alt`
* `Option`
* `AltGr`
* `Shift`
* `Super`
* `Meta`

### Available key codes

* `0` to `9`
* `A` to `Z`
* `F1` to `F24`
* `Backspace`
* `Delete`
* `Insert`
* `Return` or `Enter`
* `Up`, `Down`, `Left` and `Right`
* `Home` and `End`
* `PageUp` and `PageDown`
* `Escape` or `Esc`
* `VolumeUp`, `VolumeDown` and `VolumeMute`
* `MediaNextTrack`, `MediaPreviousTrack`, `MediaStop` and `MediaPlayPause`
* `PrintScreen`
* `Numlock`
* `Scrolllock`
* `Space`
* `Plus`
