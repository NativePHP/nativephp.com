---
title: Files
order: 300
---

## Files & Paths

Working with files in NativePHP is just like working with files in a regular Laravel application.
To achieve this, NativePHP rewrites the `Application::$storagePath()` (and thus `app()->storagePath()` and the `storage_path()` helper)
to the <a href="https://www.electronjs.org/docs/latest/api/app#appgetpathname" target="_blank">Electron `app.getPath('appData')` path</a>,
which is different for each operating system.

This means that you can continue to use Laravel's `Storage` facade to store and retrieve files on your user's file
system just as you would on your server.

If you use the default Storage configuration for the `local` filesystem, your `local` disk will also point to this
`appdata` directory, followed by `storage/app`.

![](/img/appdata.png)

Here you may see some folders you recognise, namely `database` and `storage`. The other folders are managed by Electron.
The `storage` folder is exactly the same `storage` directory you are used to seeing in your Laravel application. It
stores various caches and also application logs.

You should use this `Application::storagePath()` when storing files on your user's computer that need to remain
available even when your application is updated or removed from the system, e.g. your application's configuration,
settings and any user data that the user doesn't need direct access to.

It's also the location where your SQLite database will be stored.

### Storing files elsewhere

NativePHP doesn't interfere with any of your _existing_ filesystem configuration, so you may continue to configure
<a href="https://laravel.com/docs/filesystem" target="_blank">Filesystem</a> as you normally would, however you should be aware that it does
_add_ some new default filesystems for your convenience.

Consider that your users want to store their files in locations other than the obscure `appdata` directories on their
preferred OS. To that end, NativePHP provides a variety of convenient `filesystems` which are configured at runtime to
point to the respective, platform-specific directories for the current user.

[warning]
If your application also defines any of these filesystems, NativePHP will override their configuration with its own.
[/warning]

You can use these filesystem simply using the `Storage` facade like this:

```php
Storage::disk('user_home')->get('file.txt');
Storage::disk('desktop')->get('file.txt');
Storage::disk('documents')->get('file.txt');
Storage::disk('downloads')->get('file.txt');
Storage::disk('music')->get('file.txt');
Storage::disk('pictures')->get('file.txt');
Storage::disk('videos')->get('file.txt');
Storage::disk('recent')->get('file.txt');
```

Note that the PHP process which runs your application operates with the same privileges as the logged-in user, this
means your application is able to read and write files wherever your user is authorised to.

Generally, you should only read and write files to the user's `home` directory or your app's `appdata` directory. Be
aware that some operating systems now actively prompt the user to grant permissions to apps when they first attempt to
access directories in the user's home directory.

See [Security](/docs/digging-deeper/security) for more considerations.

[aside]
You can also continue to use cloud storage providers if you wish.

However, be mindful that an application installed on a user's device is even more likely to experience network
disruption than one operating on a server in the cloud, as your users may be without an internet connection at any
time.

You should prepare more carefully for such scenarios when interacting with any APIs that require network connectivity
by checking for a connection _before_ making a request and/or handling exceptions gracefully should a request fail.

This will help maintain a smooth user experience
[/aside]

NativePHP uses the `local` disk by default. If you would like to use a different disk, you may configure this in your
`config/filesystems.php` file.

Remember, you can set the filesystem disk your application uses by default in your `config/filesystems.php` file or by
adding a `FILESYSTEM_DISK` variable to your `.env` file.

### Symlinks

In traditional web servers, symlinking directories is a common practice - for example, it's a convenient way to expose parts of your filesystem through the public directory.

However, in a NativePHP app, this approach is less applicable. The entire application is already accessible to the end user, and symlinks can cause unexpected issues during the packaging process due to differences in how Unix-like and Windows systems handle symbolic links.

We recommend avoiding symlinks within your NativePHP app. Instead, consider either:

-   Placing files directly in their intended locations
-   Using Laravel's Storage facade to mount directories outside your application
