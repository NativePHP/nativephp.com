---
title: Databases
order: 200
---

# Working with Databases

Almost every application needs a database, especially if your app is working with complex user data or communicating
with an API. A database is an efficient and reliable way to persist structured data across multiple versions of
your application.

When building a _server-side_ application, you are free to choose the database engine you prefer. But in the context of
a self-contained native application, your choices are limited to:
- what you can reasonably bundle with your app; or
- what you can expect the user's system to have installed.

**To keep the footprint of your application small, NativePHP currently only supports SQLite out of the box.**

You can interact with SQLite via PDO or an ORM, such as Eloquent, in exactly the way you're used to.

## SQLite

<a href="https://sqlite.org/" target="_blank">SQLite</a> is a feature-rich, portable, lightweight, file-based database. It's perfect for native
applications that need persistent storage of complex data structures with the speed and tooling of SQL.

Its small footprint and minimal dependencies make it ideal for cross-platform, native applications. Your users
don't need to install anything else besides your app, and it doesn't add hundreds of MBs to your bundle,
keeping download & install size small.

### Configuration

You do not need to do anything special to configure your application to use SQLite. NativePHP will automatically:
- Switch to using SQLite when building your application.
- Create a database file for you in the `appdata` directory on the user's system.
- Configure your application to use that database file.
- Run your migrations each time your app starts, as needed.

## Development

In [development](/docs/getting-started/development), your application uses a database called `nativephp.sqlite`
which is created in the build directory.

NativePHP forces your application to use this database when it is running within the Electron/Tauri environment so that
it doesn't modify any other SQLite databases you may already be using.

## Migrations

When writing migrations, you need to consider any special recommendations for working with SQLite.

For example, prior to Laravel 11, SQLite foreign key constraints are turned off by default. If your application relies
upon foreign key constraints, <a href="https://laravel.com/docs/database#configuration" target="_blank">you need to enable SQLite support for them</a> before running your migrations.

**It's important to test your migrations on prod builds before releasing updates!** You don't want to accidentally
delete your user's data when they update your app.

### In production

In production builds of your app, NativePHP will check to see if the app version has changed and attempt to migrate
the user's copy of your database in their `appdata` folder.

During development, you will need to migrate your development database manually:

```shell
php artisan native:migrate
```

This command uses the exact same signature as the Laravel `migrate` command, so everything you're used to there can be
used here.

You can do this whether the application is running or not, but depending on how your app behaves, it may be better to
do it _before_ running your app.

### Refreshing your app database

You can completely refresh your app database using the `native:migrate:fresh` command:

```shell
php artisan native:migrate:fresh
```

**This is a destructive action that will delete all data in your database.**

## Seeding

When developing, it's especially useful to seed your database with sample data. If you've set up
<a href="https://laravel.com/docs/seeding" target="_blank">Database Seeders</a>, you can run these using the `native:db:seed` command:

```shell
php artisan native:db:seed
```

## When not to use a database

If you're only storing small amounts of very simple metadata or working files, you may not need a database at all.
Consider [storing files](/docs/digging-deeper/files) instead. These could be JSON, CSV, plain text or any other format
that makes sense for your application.

Consider also using file storage for very critical metadata about the state of your application on a user's device.
If you rely on the same database you store the user's data to store this information, if the database becomes
corrupted for any reason, your application may not be able to start at all.

If you store this information in a file, you can at least instruct your users to delete the file and restart the
application lowering the risk of deleting their data.
