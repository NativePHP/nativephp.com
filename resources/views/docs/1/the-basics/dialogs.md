---
title: Dialogs
order: 400
---

## Native Dialogs

NativePHP allows you to open native file dialogs. They can be used to give the user the ability to select a file or folder, or to save a file.

### Opening File Dialogs

To open a file dialog, you may use the `Dialog` class and its `open()` method.

The return value of the `open()` method is the path to the file or folder that the user selected.
This could be null, a file path (string), or an array of file paths, depending on the type of dialog you open.

```php
use Native\Laravel\Dialog;

Dialog::new()
    ->title('Select a file')
    ->open();
```

