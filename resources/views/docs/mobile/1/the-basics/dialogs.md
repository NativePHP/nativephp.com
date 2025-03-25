---
title: Dialogs
order: 400
---

## Native Dialogs

NativePHP allows you to trigger many native dialogs.

Dialogs are created using the `Dialog` facade.

```php
use Native\Ios\Facades\Dialog;
```

### The Share Dialog

You may open the native share dialog by using the `Dialog::share()` method.

```php
Dialog::share('Title', 'Description', 'URL');
```

### The Alert Dialog

You may open a native alert dialog by using the `Dialog::alert()` method.

```php
$buttons = [
    'Ok',
    'Cancel'
];

Dialog::alert('Title', 'Message', $buttons, fn ($selected) => {
    echo "You selected {$buttons[$selected]}";
});
```
