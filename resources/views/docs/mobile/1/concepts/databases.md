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

**It's important to test your migrations on prod builds before releasing updates!** You don't want to accidentally
delete your user's data when they update your app.

## Seeding Data with Migrations

Migrations are the perfect mechanism for seeding data in mobile applications. They provide the natural behavior you want for data seeding:

- **Run once**: Each migration runs exactly once per device
- **Tracked**: Laravel tracks which migrations have been executed
- **Versioned**: New app versions can include new data seeding migrations
- **Reversible**: You can create migrations to remove or update seed data

### Creating Seed Migrations

Create dedicated migrations for seeding data:

```bash
php artisan make:migration seed_default_categories
php artisan make:migration seed_app_settings
php artisan make:migration seed_initial_user_data
```

### Example: Seeding Default Categories

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

    public function down()
    {
        // Rollback/fresh migrations never run
    }
};
```

### Best Practices for Seed Migrations

1. **Use specific timestamps**: Name migrations with dates to ensure proper ordering
2. **Check before inserting**: Prevent duplicate data with updateOrCreate() or firstOrCreate()
3. **Handle conflicts gracefully**: Check if data already exists before seeding
4. **Use realistic data**: Seed with data that matches your production environment
5. **Test thoroughly**: Verify seed migrations work on fresh installs and updates

This approach ensures your mobile app has the initial data it needs while maintaining consistency across different app versions and user devices.

## Things to note

- As your app is installed on a separate device, you do not have remote access to the database.
- If a user deletes your application from their device, any databases are also deleted.

## Database Security & Remote Data

### Why No MySQL/PostgreSQL Support?

NativePHP for Mobile intentionally does not include MySQL, PostgreSQL, or other remote database drivers. This is a deliberate security decision to prevent developers from accidentally embedding production database credentials directly in mobile applications.

**Key security concerns:**
- Mobile apps are distributed to user devices and can be reverse-engineered
- Database credentials embedded in apps are accessible to anyone with the app binary
- Direct database connections bypass important security layers like rate limiting and access controls
- Network connectivity issues make direct database connections unreliable on mobile

### The API-First Approach

Instead of direct database connections, we strongly recommend building a secure API backend that your mobile app communicates with. This provides multiple security and architectural benefits:

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

### Recommended Architecture

```php
// ❌ NEVER DO THIS - Direct database in mobile app
DB::connection('production')->table('users')->get(); // Credentials exposed!

// ✅ DO THIS - Secure API communication
$response = Http::withToken($this->getStoredToken())
    ->get('https://your-api.com/api/users');
```

### Laravel Sanctum Integration

[Laravel Sanctum](https://laravel.com/docs/sanctum) is the perfect solution for API authentication between your mobile app and Laravel backend. It provides secure, token-based authentication without the complexity of OAuth.


### Best Practices

**For Mobile Apps:**
- Always store API tokens in SecureStorage
- Implement proper error handling for network requests
- Cache data locally using SQLite for offline functionality
- Use HTTPS for all API communications
- Implement retry logic with exponential backoff

**For API Backends:**
- Use Laravel Sanctum or similar for token-based authentication
- Implement rate limiting to prevent abuse
- Validate and sanitize all input data
- Use HTTPS with proper SSL certificates
- Log all authentication attempts and API access

This approach ensures your mobile app remains secure while providing seamless access to your backend data through a well-designed API layer.
