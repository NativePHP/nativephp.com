---
title: SecureStorage
order: 700
---

## Overview

The SecureStorage API provides secure storage using the device's native keychain (iOS) or keystore (Android) for sensitive data like tokens, passwords, and user credentials.

```php
use Native\Mobile\Facades\SecureStorage;
```

## Methods

### `set()`

Stores a secure value in the native keychain or keystore.

**Parameters:**
- `string $key` - The key to store the value under
- `string|null $value` - The value to store securely

**Returns:** `bool` - `true` if successfully stored, `false` otherwise

```php
$success = SecureStorage::set('api_token', 'abc123xyz');

if ($success) {
    // Token stored securely
} else {
    // Storage failed
}
```

### `get()`

Retrieves a secure value from the native keychain or keystore.

**Parameters:**
- `string $key` - The key to retrieve the value for

**Returns:** `string|null` - The stored value or `null` if not found

```php
$token = SecureStorage::get('api_token');

if ($token) {
    // Use the retrieved token
    $this->authenticateWithToken($token);
} else {
    // Token not found, user needs to login
    $this->redirectToLogin();
}
```

### `delete()`

Deletes a secure value from the native keychain or keystore.

**Parameters:**
- `string $key` - The key to delete the value for

**Returns:** `bool` - `true` if successfully deleted, `false` otherwise

```php
$deleted = SecureStorage::delete('api_token');

if ($deleted) {
    // Token removed successfully
} else {
    // Deletion failed or key didn't exist
}
```

## Example Usage

```php
use Livewire\Component;
use Native\Mobile\Facades\SecureStorage;

class AuthManager extends Component
{
    public bool $isLoggedIn = false;
    public string $error = '';

    public function mount()
    {
        // Check if user has stored credentials
        $this->checkStoredAuth();
    }

    public function login(string $username, string $password)
    {
        try {
            // Authenticate with your API
            $response = Http::post('/api/login', [
                'username' => $username,
                'password' => $password
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Store tokens securely
                SecureStorage::set('access_token', $data['access_token']);
                SecureStorage::set('refresh_token', $data['refresh_token']);
                SecureStorage::set('user_id', (string) $data['user']['id']);
                
                $this->isLoggedIn = true;
            } else {
                $this->error = 'Login failed';
            }
        } catch (Exception $e) {
            $this->error = 'Network error: ' . $e->getMessage();
        }
    }

    public function logout()
    {
        // Clear stored credentials
        SecureStorage::delete('access_token');
        SecureStorage::delete('refresh_token');
        SecureStorage::delete('user_id');
        
        $this->isLoggedIn = false;
    }

    private function checkStoredAuth()
    {
        $accessToken = SecureStorage::get('access_token');
        
        if ($accessToken) {
            // Verify token is still valid
            $response = Http::withToken($accessToken)
                ->get('/api/user');
                
            if ($response->successful()) {
                $this->isLoggedIn = true;
            } else {
                // Token expired, try refresh
                $this->refreshToken();
            }
        }
    }

    private function refreshToken()
    {
        $refreshToken = SecureStorage::get('refresh_token');
        
        if ($refreshToken) {
            $response = Http::post('/api/refresh', [
                'refresh_token' => $refreshToken
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                SecureStorage::set('access_token', $data['access_token']);
                $this->isLoggedIn = true;
            } else {
                // Refresh failed, clear everything
                $this->logout();
            }
        }
    }

    public function render()
    {
        return view('livewire.auth-manager');
    }
}
```

## Platform Implementation

### iOS - Keychain Services
- Uses the iOS Keychain Services API
- Data is encrypted and tied to your app's bundle ID
- Survives app deletion and reinstallation if iCloud Keychain is enabled
- Protected by device passcode/biometrics

### Android - Keystore
- Uses Android Keystore system
- Hardware-backed encryption when available
- Data is automatically deleted when app is uninstalled
- Protected by device lock screen

## Security Features

- **Encryption:** All data is automatically encrypted
- **App Isolation:** Data is only accessible by your app
- **System Protection:** Protected by device authentication
- **Tamper Resistance:** Hardware-backed security when available

## Best Practices

### What to Store
- API tokens and refresh tokens
- User credentials (if necessary)
- Encryption keys
- Sensitive user preferences
- Two-factor authentication secrets

### What NOT to Store
- Large amounts of data (use encrypted database instead)
- Non-sensitive configuration
- Temporary data
- Cached content

### Implementation Tips

```php
class SecureSettings
{
    public function storeUserCredentials(string $userId, string $token)
    {
        // Use prefixed keys for organization
        SecureStorage::set("user_{$userId}_token", $token);
        SecureStorage::set("user_{$userId}_last_login", now()->toISOString());
    }

    public function getUserToken(string $userId): ?string
    {
        return SecureStorage::get("user_{$userId}_token");
    }

    public function clearUserData(string $userId)
    {
        // Clean up all user-related secure data
        SecureStorage::delete("user_{$userId}_token");
        SecureStorage::delete("user_{$userId}_last_login");
        SecureStorage::delete("user_{$userId}_preferences");
    }

    public function rotateToken(string $userId, string $newToken)
    {
        // Atomic token rotation
        $oldToken = $this->getUserToken($userId);
        
        if (SecureStorage::set("user_{$userId}_token", $newToken)) {
            // New token stored successfully
            Log::info("Token rotated for user {$userId}");
        } else {
            // Rotation failed, keep old token
            Log::error("Token rotation failed for user {$userId}");
        }
    }
}
```

## Error Handling

```php
public function storeSecurely(string $key, string $value)
{
    $attempts = 0;
    $maxAttempts = 3;
    
    while ($attempts < $maxAttempts) {
        if (SecureStorage::set($key, $value)) {
            return true;
        }
        
        $attempts++;
        usleep(100000); // Wait 100ms before retry
    }
    
    Log::error("Failed to store secure value after {$maxAttempts} attempts", [
        'key' => $key
    ]);
    
    return false;
}
```

