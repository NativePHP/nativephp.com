---
title: Settings
order: 450
---

## Storing Settings

NativePHP offers an easy method to store and retrieve settings in your application. This is helpful for saving application-wide
settings that persist even after closing and reopening the application.

Settings are managed using the `Settings` facade and are stored in a file named `config.json` in the
[`appdata`](/docs/1/getting-started/debugging#start-from-scratch) directory of your application.

```php
use Native\Laravel\Facades\Settings;
```

### Setting a value
It's as simple as calling the `set` method. The key must be a string.
```php
Settings::set('key', 'value');
```

### Getting a value
To retrieve a setting, use the `get` method.
```php
$value = Settings::get('key');
```

You may also provide a default value to return if the setting does not exist.
```php
$value = Settings::get('key', 'default');
```
If the setting does not exist, `default` will be returned.

### Forgetting a value
If you want to remove a setting altogether, use the `forget` method.
```php
Settings::forget('key');
```

### Clearing all settings
To remove all settings, use the `clear` method.
```php
Settings::clear();
```
This will remove all settings from the `config.json` file.

## Events

### `SettingChanged`
The `Native\Laravel\Events\Notifications\SettingChanged` event is dispatched when a setting is changed.

Example usage:
```php
Event::listen(SettingChanged::class, function (SettingChanged $event) {
    $key = $event->key; // Key of the setting that was changed
    $value = $event->value; // New value of the setting
});
```

This event can also be listened with Livewire to refresh your settings page:
```php
use Livewire\Component;
use Native\Laravel\Events\Notifications\SettingChanged;

class Settings extends Component
{
    protected $listeners = [
        'native:'.SettingChanged::class => '$refresh',
    ];
}
```

