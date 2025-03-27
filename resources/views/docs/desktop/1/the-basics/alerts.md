---
title: Alerts
order: 410
---

## Native Alerts

NativePHP allows you to show native alerts to the user. They can be used to display messages, ask for confirmation, or
report an error.

Alerts are created using the `Alert` facade.

```php
use Native\Laravel\Facades\Alert;
```

### Showing Alerts

To show an alert, you may use the `Alert` class and its `show()` method.

```php
Alert::new()
    ->show('This is a simple alert');
```

## Configuring Alerts

### Alert Title

You may set the title of the alert using the `title()` method.

```php
Alert::new()
    ->title('Pizza Order')
    ->show('Your pizza has been ordered');
```

### Alert Buttons

You may configure the buttons of the alert using the `buttons()` method.
This method takes an array of button labels.

The return value of the `show()` method is the index of the button that the user clicked.
Example: If the user clicks the "Yes" button, the `show()` method will return `0`. If the user clicks the "Maybe"
button, the `show()` method will return `2`.

If no buttons are defined, the alert will only have an "OK" button.

```php
Alert::new()
    ->buttons(['Yes', 'No', 'Maybe'])
    ->show('Do you like pizza?');
```

### Alert Detail

You may set the detail of the alert using the `detail()` method.
The detail is displayed below the message and provides additional information about the alert.

```php
Alert::new()
    ->detail('Fun facts: Pizza was first made in Naples in 1889')
    ->show('Do you like pizza?');
```

### Alert Type

You may set the type of the alert using the `type()` method.
The type can be one of the following values: `none`, `info`, `warning`, `error`, `question`. On Windows, `question`
displays the same icon as `info`. On macOS, both `warning` and `error` display the same warning icon.

```php
Alert::new()
    ->type('error')
    ->show('An error occurred');
```

### Alert Default Button

You may set the default button of the alert using the `defaultId()` method.
The default button is preselected when the alert appears.

The default button can be set to the index of the button in the `buttons()` array.

```php
Alert::new()
    ->defaultId(0)
    ->buttons(['Yes', 'No', 'Maybe'])
    ->show('Do you like pizza?');
```

### Alert Cancel Button

You may set the cancel button of the alert using the `cancelId()` method.
The cancel button is the button that is selected when the user presses the "Escape" key.

The cancel button can be set to the index of the button in the `buttons()` array.

By default, this is assigned to the first button labeled 'Cancel' or 'No'. If no such buttons exist and this option is
not set, the return value will be `0`.

```php
Alert::new()
    ->cancelId(1)
    ->buttons(['Yes', 'No', 'Maybe'])
    ->show('Do you like pizza?');
```

### Error Alerts

You may use the `error()` method to display an error alert.

The `error()` method takes two required parameters: the title of the error alert and the message of the error alert.

```php
Alert::new()
    ->error('An error occurred', 'The pizza oven is broken');
```
