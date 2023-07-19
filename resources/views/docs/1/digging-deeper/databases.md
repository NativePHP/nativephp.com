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

[SQLite](https://sqlite.org/) is a feature-rich, portable, lightweight, file-based database. It's perfect for native
applications that need persistent storage of complex data structures with the speed and tooling of SQL.

Its small footprint and minimal dependencies make it ideal for cross-platform, native applications. Your users
don't need to install anything else besides your app, and it doesn't add hundreds of MBs to your bundle,
keeping download & install size small.

### Configuration

You do not need to do anything special to configure your application to use SQLite. NativePHP will automatically:
- Switch to using SQLite when building your application
- Create a database file for you in the `storage` directory on the user's system
- Configure your application to use that database file
- Run your migrations each time your app starts if needed

## Migrations

When writing migrations, you need to consider any special recommendations for working with SQLite. For example,
SQLite disables foreign key constraints by default. If your application relies upon foreign key constraints,
[you need to enable SQLite support for them](https://laravel.com/docs/10.x/database#configuration) before
writing your migrations.

**It's important to test your migrations before releasing updates!** You don't want to accidentally delete your user's
data when they update your app.

## When not to use a database

If you're only storing small amounts of very simple metadata or working files, you may not need a database at all.
Consider [storing files](/docs/digging-deeper/files) instead. These could be JSON, CSV, plain text or any other format
that makes sense for your application.

Consider also using file storage for very critical metadata about the state of your application on a user's device.
If you rely on the same database you store the user's data to store this information, if the database becomes
corrupted for any reason, your application may not be able to start at all.

If you store this information in a file, you can at least instruct your users to delete the file and restart the
application lowering the risk of deleting their data.
