---
title: Security
order: 100
---

## Security

Although NativePHP tries to make it as easy as possible to make your application secure, it is your responsibility to
protect your users.

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

## Secure Storage

NativePHP provides access to your users' device's native Keystore/Keychain through the
[`SecureStorage`](/docs/mobile/1/apis/secure-storage) facade, which
allow you to store small amounts of data in a secure way.

The device's secure storage encrypts and decrypts data on the fly and that means you can safely rely on it to store
critical things like API tokens, keeping your users and your systems safe.

This data is only accessible by your app and is persisted beyond the lifetime of your app, so it will still be available
the next time your app is open.

### Why not use the Laravel `Crypt` facade?

By default, the `Crypt` facade - and by extension the `encrypt` and `decrypt` helper functions - all rely on the
`APP_KEY` value set in your `.env` file.

We _will_ use Laravel's underlying `Encryption` class, but you should avoid using these helpers directly.

In the context of distributed apps, the `APP_KEY` is shipped _with_ your app and therefore isn't secure. Anyone who
knows where to look for it will be able to find it. Then any data encrypted with it is no better off than if it was
stored in plain text.

Also, it will be the same key for every user, and this presents a considerable risk.

What you really want is a **unique key for each user**, and for that you really need to generate your encryption key
once your app is installed on your user's device.

You could do this and update the `.env` file, but it would still be stored in a way that an attacker may be able to
exploit.

A better approach is to generate a secure key the first time your app opens, place that key in Secure Storage, and
then use that key to encrypt your other data before storage:

```php
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Storage;
use Native\Mobile\Facades\SecureStorage;

function generateRandomKey()
{
    return base64_encode(
        Encrypter::generateKey(config('app.cipher'))
    );
}

$encryptionKey = SecureStorage::get('encryption_key');

if (! $encryptionKey) {
    SecureStorage::set('encryption_key', $encryptionKey = generateRandomKey());
}

$mobileEncrypter = new Encrypter($encryptionKey);

$encryptedContents = $mobileEncrypter->encrypt(
    $request->file('super_private_file')
);

Storage::put('my_secure_file.pdf', $encryptedContents);
```

And then decrypt it later:

```php
$decryptedContents = $mobileEncrypter->decrypt(
    Storage::get('my_secure_file.pdf')
);
```

### Secure Storage vs Database/Files

Secure Storage is only meant for small amounts of text data, usually no more than a few KBs. If you need to store
larger amounts of data or files, you should store this in a database or as a file.
