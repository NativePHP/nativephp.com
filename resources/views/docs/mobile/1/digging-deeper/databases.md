---
title: Databases
order: 200
---

# Working with Databases

You'll almost certainly want your application to persist structured data. For this, NativePHP supports
[SQLite](https://sqlite.org/), which works on both iOS and Android devices.

You can interact with SQLite from PHP in whichever way you're used to.

### Configuration

You do not need to do anything special to configure your application to use SQLite. NativePHP will automatically:
- Switch to using SQLite when building your application.
- Create the database for you in the app container.
- Run your migrations each time your app starts, as needed.

## Migrations

When writing migrations, you need to consider any special recommendations for working with SQLite.

For example, prior to Laravel 11, SQLite foreign key constraints are turned off by default. If your application relies
upon foreign key constraints, [you need to enable SQLite support for them](https://laravel.com/docs/database#configuration) before running your migrations.

**It's important to test your migrations on prod builds before releasing updates!** You don't want to accidentally
delete your user's data when they update your app.

## Things to note

- As your app is installed on a separate device, you do not have remote access to the database.
- If a user deletes your application from their device, any databases are also deleted.
