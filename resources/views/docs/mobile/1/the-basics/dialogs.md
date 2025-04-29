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
Dialog::alert('Title', 'Message');
```

### The Toast Dialog

You may open a native toast dialog by using the `Dialog::toast()` method. There is not a toast dialog on iOS, 
on iOS we will simply show an Alert Dialog with just an `OK` button.

```php
Dialog::toast('Message');
```
