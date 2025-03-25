---
title: Security
order: 400
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
care. If you choose to store them anywhere (either in a [File](files) or
[Database](databases)), make sure you store them
[encrypted](../the-basics/system#encryption-decryption) and decrypt them only when needed.
