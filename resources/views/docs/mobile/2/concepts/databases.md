---
title: Databases
order: 200
---

## Working with Databases

You'll almost certainly want your application to persist structured data. For this, NativePHP supports
[SQLite](https://sqlite.org/), which works on both iOS and Android devices.

You can interact with SQLite from PHP in whichever way you're used to.

## Configuration

You do not need to do anything special to configure your application to use SQLite. NativePHP will automatically:
- Switch to using SQLite when building your application.
- Create the database for you in the app container.
- Run your migrations each time your app starts, as needed.

## Migrations

When writing migrations, you need to consider any special recommendations for working with SQLite.

For example, prior to Laravel 11, SQLite foreign key constraints are turned off by default. If your application relies
upon foreign key constraints, [you need to enable SQLite support for them](https://laravel.com/docs/database#configuration) before running your migrations.

**It's important to test your migrations on [prod builds](/docs/mobile/1/getting-started/development#releasing)
before releasing updates!** You don't want to accidentally delete your user's data when they update your app.

## Seeding data with migrations

Migrations are the perfect mechanism for seeding data in mobile applications. They provide the natural behavior you
want for data seeding:

- **Run once**: Each migration runs exactly once per installation.
- **Tracked**: Laravel tracks which migrations have been executed.
- **Versioned**: New app versions can include new data seeding migrations.
- **Reversible**: You can create migrations to remove or update seed data.

### Creating seed migrations

Create dedicated migrations for seeding data:

```shell
php artisan make:migration seed_app_settings
```

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('categories')->insert([
            ['name' => 'Work', 'color' => '#3B82F6'],
            ['name' => 'Personal', 'color' => '#10B981'],
        ]);
    }
};
```

### Test thoroughly

This is the most important step when releasing new versions of your app, especially with new migrations.

Your migrations should work both for users who are installing your app for the first time (or re-installing) _and_
users who have updated your app to a new release.

Make sure you test your migrations under the different scenarios that your users' databases are likely to be in.

## Things to note

- As your app is installed on a separate device, you do not have remote access to the database.
- If a user deletes your application from their device, any databases are also deleted.

## Can I get MySQL/Postgres/other support?

No.

SQLite being the only supported database driver is a deliberate security decision to prevent developers from
accidentally embedding production database credentials directly in mobile applications. Why?

- Mobile apps are distributed to user devices and can be reverse-engineered.
- Database credentials embedded in apps may be accessible to anyone with the app binary.
- Direct database connections bypass important security layers like rate limiting and access controls.
- Network connectivity issues make direct database connections unreliable from mobile devices and can be troublesome
    for your database to handle.

## API-first

If a key part of your application relies on syncing data between a central database and your client apps, we strongly
recommend that you do so via a secure API backend that your mobile app can communicate with.

This provides multiple security and architectural benefits:

**Security Benefits:**
- Database credentials never leave your server
- Implement proper authentication and authorization
- Rate limiting and request validation
- Audit logs for all data access
- Ability to revoke access instantly

**Technical Benefits:**
- Better error handling and offline support
- Easier to scale and maintain
- Version your API for backward compatibility
- Transform data specifically for mobile consumption

### Securing your API

For the same reasons that you shouldn't share database credentials in your `.env` file or elsewhere in your app code,
you shouldn't store API keys or tokens either.

If anything, you should provide a client key that **only** allows client apps to request tokens. Once you have
authenticated your user, you can pass an access token back to your mobile app and use this for communicating with your
API.

Store these tokens on your users' devices securely with the [`SecureStorage`](/docs/mobile/1/apis/secure-storage) API.

It's a good practice to ensure these tokens have high entropy so that they are very hard to guess and a short lifespan.
Generating tokens is cheap; leaking personal customer data can get _very_ expensive!

Use industry-standard tools like OAuth-2.0-based providers, Laravel Passport, or Laravel Sanctum.

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-gradient-to-tl from-transparent to-violet-100/75 px-5 ring-1 ring-black/5 dark:from-slate-900/30 dark:to-indigo-900/35">

[Laravel Sanctum](https://laravel.com/docs/sanctum) is an ideal solution for API authentication between your mobile app and Laravel backend.
It provides secure, token-based authentication without the complexity of OAuth.

</aside>

#### Considerations

In your mobile apps:

- Always store API tokens using `SecureStorage`
- Use HTTPS for all API communications
- Cache data locally using SQLite for offline functionality
- Check for connectivity before making API calls

And on the API side:

- Use token-based authentication
- Implement rate limiting to prevent abuse
- Validate and sanitize all input data
- Use HTTPS with proper SSL certificates
- Log all authentication attempts and API access
