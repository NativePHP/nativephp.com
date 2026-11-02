---
title: Security
order: 100
---

## Security

Although NativePHP tries to make it as easy as possible to make your application secure, it is your responsibility to
protect your users.

### Your code ships with your app

When you build your app for release, your Laravel application — PHP source, views, and assets — is bundled into the
app package (APK/AAB on Android, IPA on iOS) and extracted on the device the first time your app runs. The device
needs this code to run your app, so anyone who unpacks your app package can read it.

This is true of every mobile app, whatever it's built with: JavaScript ships the same way in React Native and Ionic
apps, and even compiled Kotlin and Swift can be decompiled. Android's
[minification and obfuscation options](../getting-started/configuration#android-build-configuration) apply R8/ProGuard to
the app's Kotlin/Java code only; your PHP is bundled as an asset and ships as written.

Encrypting the bundle wouldn't change this: the decryption key would have to ship inside the same package, where it
can be extracted just as easily. Rather than trying to hide client-side code, build so that reading it doesn't matter:

- Treat everything you bundle as public — your PHP source, views, and the parts of your `.env` file that ship with
  the app.
- Keep secrets and sensitive business logic on a server, behind authenticated APIs.
- Strip sensitive keys from your bundled `.env` using the `cleanup_env_keys` option in `config/nativephp.php` — see
  [Configuration](../getting-started/configuration).

The rest of this page covers how to handle the data that genuinely needs protecting: secrets, tokens, and your users'
data on the device.

### Secrets and .env

As your application is being installed on systems outside your/your organisation's control, it is important to think
of the environment that it's in as _potentially_ hostile, which is to say that any secrets, passwords or keys
could fall into the hands of someone who might try to abuse them.

This means you should, where possible, use unique keys for each installation, preferring to generate these at first-run
or on every run rather than sharing the same key for every user across many installations.

Especially if your application is communicating with any private APIs over the network, we highly recommend that your
application and any API use a robust and secure authentication protocol, such as OAuth2, that enables you to create and
distribute unique and expiring tokens (an expiration date less than 48 hours in the future is recommended) with a high
level of entropy, as this makes them hard to guess and hard to abuse.

**Always use HTTPS.**

If your application allows users to connect _their own_ API keys for a service, you should treat these keys with great
care. If you choose to store them anywhere (either in a file or
[Database](databases)), make sure you store them
[encrypted](../the-basics/system#encryption-decryption) and decrypt them only when needed.

## Encrypting data on the device

When a user first opens your app, NativePHP generates a **unique `APP_KEY` just for their device** and stores it in the
device's secure storage: the Keychain on iOS and the Keystore on Android. NativePHP reads the key from there each time
your app starts and makes it available to Laravel.

This means Laravel's encrypter works out of the box. You can use the `Crypt` facade and
[encrypted Eloquent casts](https://laravel.com/docs/eloquent-mutators#encrypted-casting) just as you would on a server,
and store the results in your [database](databases) or on the file system:

```php
use Illuminate\Support\Facades\Crypt;

$user->update([
    'api_token' => Crypt::encryptString($token),
]);

$token = Crypt::decryptString($user->api_token);
```

You don't need a plugin for this.

### What it protects against

On-device encryption has one job: if someone gets hold of your app's files or its database, the sensitive values in
them are unreadable without the key, and the key is not stored alongside them.

That makes it a good fit for data that is sensitive but easy to replace:

- API tokens and refresh tokens, ideally short-lived
- Session data and cached responses from your API
- Anything else you can fetch or generate again

### The key never leaves the device

Each device has its own key, and it stays there. It is not included in device backups and it does not move when your
user sets up a new phone. Their app data usually does: the database and files are backed up and restored, but the key
that decrypts them is not.

> [!CAUTION]
> If the only copy of some data is encrypted on the device, your user can lose it for good. A new phone or a restore
> from backup can leave encrypted data behind with no key to decrypt it, and deleting your app removes it altogether.

So don't use on-device encryption for long-lived data your users expect to keep as they change devices and upgrade
their OS. Keep that data on your server, or somewhere else that isn't tied to one device, and treat what's on the
device as a copy.

Your app should also expect to find values it can no longer decrypt, for example after a restore. Catch the
`DecryptException`, discard the value and fetch it again:

```php
use Illuminate\Contracts\Encryption\DecryptException;

try {
    $token = Crypt::decryptString($user->api_token);
} catch (DecryptException) {
    // The key has changed. Ask the user to sign in again.
}
```

### Don't send encrypted values anywhere else

Only the device that encrypted a value can decrypt it. Sending the encrypted value to your back-end or to a third
party is pointless unless you also send the key, and you should never do that.

If you need to share data, decrypt it on the device, send it over HTTPS, and let the receiving service protect it with
its own key.

> [!WARNING]
> Make sure you do not leak the `APP_KEY` or decrypted data inadvertently through error tracking or debug logging tools.

### Secure Storage

The [`SecureStorage`](../plugins/core/secure-storage) plugin takes a different approach. Instead of encrypting a value
and leaving you to store it, it hands the value to the device's Keychain or Keystore-backed storage directly, so it
never touches your database or files.

It is meant for a small number of short text values, usually no more than a few KBs. It is device-bound in the same way:
values do not move to a new device.

For most apps Laravel's encrypter is all you need. Reach for Secure Storage if you would rather keep a secret out of
your database and files altogether.
