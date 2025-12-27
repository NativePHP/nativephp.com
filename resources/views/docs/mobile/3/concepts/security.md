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
[`SecureStorage`](/docs/apis/secure-storage) facade, which
allow you to store small amounts of data in a secure way.

The device's secure storage encrypts and decrypts data on the fly and that means you can safely rely on it to store
critical things like API tokens, keeping your users and your systems safe.

This data is only accessible by your app and is persisted beyond the lifetime of your app, so it will still be available
the next time your app is opened.


<aside>

Secure Storage is only meant for small amounts of text data, usually no more than a few KBs. If you need to store
larger amounts of data or files, you should store this in a database or as a file.

</aside>

### When to use the Laravel `Crypt` facade

When a user first opens your app, NativePHP generates a **unique `APP_KEY` just for their device** and stores it in the
device's secure storage. This means each instance of your application has its own encryption key that is securely
stored on the device.

NativePHP securely reads the `APP_KEY` from secure storage and makes it available to Laravel. So you can safely use the
`Crypt` facade to encrypt and decrypt data!

<aside>

Make sure you do not leak the `APP_KEY` or decrypted data inadvertently through error tracking or debug logging tools.

</aside>

This is great for encrypting larger amounts of data that wouldn't easily fit in secure storage. You can encrypt values
and store them in the file system or in the SQLite database, knowing that they are safe at rest:

```php
use Illuminate\Support\Facades\Crypt;

$encryptedContents = Crypt::encryptString(
    $request->file('super_private_file')
);

Storage::put('my_secure_file', $encryptedContents);
```

And then decrypt it later:

```php
$decryptedContents = Crypt::decryptString(
    Storage::get('my_secure_file')
);
```

<aside>

Data encrypted with the `Crypt` facade should stay on the user's device with your app. Placing it encrypted anywhere
else risks the chance that it will be unrecoverable. If the user loses their device or deletes your app,
they will lose the encryption key and the data will be encrypted forever.

If you wish to share data, decrypt it first, transmit securely (e.g. over HTTPS) and re-encrypt it with a different key
that is safely managed elsewhere.

</aside>

