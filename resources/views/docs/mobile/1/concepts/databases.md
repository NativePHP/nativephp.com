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

**Backend Setup:**
```php
// Install Sanctum in your API backend
composer require laravel/sanctum

// Create login endpoint
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('mobile-app')->plainTextToken;
        
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    return response()->json(['error' => 'Invalid credentials'], 401);
});

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/data', function (Request $request) {
        // Your protected data endpoints
        return Data::where('user_id', $request->user()->id)->get();
    });
});
```

### Secure Token Storage

Use the [SecureStorage API](/docs/mobile/1/apis/secure-storage) to securely store authentication tokens on the device:

```php
use Native\Mobile\Facades\SecureStorage;
use Illuminate\Support\Facades\Http;

class ApiAuthManager extends Component
{
    public bool $isAuthenticated = false;
    public string $error = '';

    public function mount()
    {
        $this->checkStoredAuthentication();
    }

    public function login(string $email, string $password)
    {
        try {
            $response = Http::post('https://your-api.com/api/login', [
                'email' => $email,
                'password' => $password
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Store token securely in device keychain/keystore
                SecureStorage::set('api_token', $data['token']);
                SecureStorage::set('user_data', json_encode($data['user']));
                
                $this->isAuthenticated = true;
                $this->error = '';
            } else {
                $this->error = 'Invalid credentials';
            }
        } catch (Exception $e) {
            $this->error = 'Network error: ' . $e->getMessage();
        }
    }

    public function logout()
    {
        // Revoke token on server
        $token = SecureStorage::get('api_token');
        if ($token) {
            Http::withToken($token)->post('https://your-api.com/api/logout');
        }
        
        // Clear local storage
        SecureStorage::delete('api_token');
        SecureStorage::delete('user_data');
        
        $this->isAuthenticated = false;
    }

    private function checkStoredAuthentication()
    {
        $token = SecureStorage::get('api_token');
        
        if ($token) {
            // Verify token is still valid
            $response = Http::withToken($token)
                ->get('https://your-api.com/api/user');
                
            if ($response->successful()) {
                $this->isAuthenticated = true;
            } else {
                // Token expired or invalid
                SecureStorage::delete('api_token');
                SecureStorage::delete('user_data');
            }
        }
    }

    public function makeAuthenticatedRequest(string $endpoint)
    {
        $token = SecureStorage::get('api_token');
        
        if (!$token) {
            throw new Exception('No authentication token available');
        }

        $response = Http::withToken($token)
            ->get("https://your-api.com/api/{$endpoint}");

        if ($response->status() === 401) {
            // Token expired
            $this->logout();
            throw new Exception('Authentication expired');
        }

        return $response->json();
    }
}
```

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

**Token Management:**
```php
class TokenManager 
{
    public function refreshTokenIfNeeded(): bool
    {
        $token = SecureStorage::get('api_token');
        $tokenExpiry = SecureStorage::get('token_expiry');
        
        if (!$token || ($tokenExpiry && now()->timestamp > $tokenExpiry)) {
            return $this->refreshToken();
        }
        
        return true;
    }
    
    private function refreshToken(): bool
    {
        $refreshToken = SecureStorage::get('refresh_token');
        
        if (!$refreshToken) {
            return false;
        }
        
        $response = Http::post('https://your-api.com/api/refresh', [
            'refresh_token' => $refreshToken
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            SecureStorage::set('api_token', $data['access_token']);
            SecureStorage::set('token_expiry', $data['expires_at']);
            return true;
        }
        
        return false;
    }
}
```

This approach ensures your mobile app remains secure while providing seamless access to your backend data through a well-designed API layer.
