---
title: Dialogs
order: 400
---

## Native Dialogs

NativePHP allows you to open native file dialogs. They can be used to give the user the ability to select a file or folder, or to save a file.

Dialogs are created using the `Dialog` facade.
```php
use Native\Laravel\Dialog;
```

### Opening File Dialogs

To open a file dialog, you may use the `Dialog` class and its `open()` method.

The return value of the `open()` method is the path to the file or folder that the user selected.
This could be null, a file path (string), or an array of file paths, depending on the type of dialog you open.

```php
Dialog::new()
    ->title('Select a file')
    ->open();
```

### Opening Save Dialogs

The `open()` dialog allows users to select existing files or folders, but not to create new files or folders.
For this, you may use the `save()` method.
This method will return the path to the file that the user wants to save.

Please note that the `save()` method will not actually save the file for you, it will only return the path to the file that the user wants to save.

```php
Dialog::new()
    ->title('Save a file')
    ->save();
```

## Configuring File Dialogs

### Dialog Title

You may set the title of the dialog using the `title()` method.

```php
Dialog::new()
    ->title('Select a file')
    ->open();
```

### Dialog Button Label

You may configure the label of the dialog button using the `button()` method.
This is the button that the user clicks to confirm their selection.

```php
Dialog::new()
    ->button('Select')
    ->open();
```

### Dialog Default Path

You may configure the default path of the dialog using the `defaultPath()` method.
This is the path that the dialog will open in by default, if it exists.

```php
Dialog::new()
    ->defaultPath('/Users/username/Desktop')
    ->open();
```

### Dialog File Filters

By default, the file dialog will allow the user to select any file.
You may constrain the file types that the user can select using the `filter()` method.
One dialog can have multiple filters.

The first argument of the `filter()` method is the name of the filter, and the second argument is an array of file extensions.

```php
Dialog::new()
    ->filter('Images', ['jpg', 'png', 'gif'])
    ->filter('Documents', ['pdf', 'docx'])
    ->open();
```

### Allowing Multiple Selections

By default, the file dialog will only allow the user to select one file.
You may change this behavior using the `multiple()` method.
This will result in the `open()` method returning an array of file paths, instead of a single file path string.

```php
$files = Dialog::new()
    ->multiple()
    ->open();
```

### Showing Hidden Files

By default, the file dialog will not show hidden files (files that start with a dot).
You may change this behavior using the `showHiddenFiles()` method.

```php
Dialog::new()
    ->withHiddenFiles()
    ->open();
```

### Resolving Symbolic Links

By default, the file dialog will always resolve symbolic links. 
This means that if you select a symbolic link, the dialog will return the path to the file or folder that the symbolic link points to.

You may change this behavior using the `dontResolveSymlinks()` method.

```php
Dialog::new()
    ->dontResolveSymlinks()
    ->open();
```

### Opening Dialogs as Sheets

By default, all NativePHP dialogs will open as separate windows that can be moved around independently.

If you would like to open a dialog as a "sheet" (a dialog that is attached to a window), you may use the `asSheet()` method.
The first argument of the `asSheet()` method is the ID of the window to attach the dialog to.
If you do not specify a window ID, NativePHP will use the ID of the currently focused window.

```php
Dialog::new()
    ->asSheet()
    ->open();
```

### Opening Folders

By default, the dialog opens a file or group of files.

If you would like to open a folder instead, you may use the `folders()` method.

```php
Dialog::new()
    ->folders()
    ->open();
```
