---
title: Security
order: 400
---
# Security

When building desktop applications it's essential to take your application's security to the next level, both to
protect your application and infrastructure, but also to protect your users, their system and their data. This is a
complex and wide-reaching topic. Please take time to thoroughly understand everything discussed in this chapter.

Remember that we can't cover everything here either, so please use good judgement when implementing features of your
application that allows users to manipulate data on their filesystem or other sources.

## Protecting your application and infrastructure

A major consideration for NativePHP is how it can protect _your_ application.

### Secrets and .env
As your application is being installed on systems outside of your/your organisation's control, it is important to think
of the environment that it's in as _potentially_ hostile, which is to say that any secrets, passwords or keys
could fall into the hands of someone who might try to abuse them.

This means you should, where possible, use unique keys for each installation, preferring to generate these at first-run
or on every run rather than sharing the same key for every user across many installations.

Especially if your application is communicating with any private APIs over the network, we highly recommend that your
application and any API use a robust and secure authentication protocol, such as OAuth2, that enables you to create and
distribute unique and expiring tokens (an expiration date less than 48 hours in the future is recommended) with a high
level of entropy, as this makes them hard to guess and hard to abuse.

If your application allows users to connect _their own_ API keys for a service, you should treat these keys with great
care. If you choose to store them anywhere (either in a [File](/docs/digging-deeper/files) or
[Database](/docs/digging-deeper/databases)), make sure you store them encrypted and decrypt them only when in use.

### Files and privileges

Your application runs in a privileged state thanks to the PHP runtime being executed as the user who is currently
operating the system. This is convenient, but it also comes with risks. Your application has access to everything that
the user is authorized to access on the system.

You should limit where you are reading and writing files to the locations your user expects. These are the `appdata`
folder for the combination of your application and this user and the user's `home` directory (and the other user
subdirectories).

All of these can be done simply by using the provided Storage filesystems detailed in
[Files](/docs/digging-deeper/files).

### The web servers

NativePHP works by spinning up web servers on each side of the runtime environment: one on the PHP side to execute your
application and another on the Electron side, to interact with Electron's native environment hooks for the operating
system. It then bridges the gap between the two by making _authenticated and encrypted_ HTTP calls between the two
using a pre-shared, dynamic key that is regenerated every time your application starts.

This prevents any third-party software from snooping/sniffing the connection and 'tinkering' with either your
application or the Electron environment. This means that your application's front-end will only be accessible through
your Electron application shell and the Electron APIs will only respond to your application.

**You MUST NOT bypass this security measure!** If you do, your application will be open to attack from very basic HTTP
calls, which it is trivial for any installed application to make, or even for your user to be coerced into making via a
web browser (e.g. from a phishing attack).

By default, Laravel's built-in CSRF and CORS protections will go some way to preventing many of these kinds of attacks
but you should do all you can to prevent unwanted attack vectors from being made available.

## Protecting your users and their data

Equally important is how your app protects users. NativePHP is a complex combination of powerful software and so there
are a number of risks associated with its use.

### When sending data over the network

**Always use HTTPS to communicate with web services.** This ensures that any data sent between your user's device and
the service is encrypted in transit.

### The PHP executable

Currently, the bundled PHP executable can be used by any user or application that knows where to find it and has
privileges to execute binaries in that location.

This is a potential attack vector that your users ought to be aware of when they are installing other applications. If
a user installs an application that they don't trust, it may attempt to use the PHP binary bundled with your application
to execute arbitrary code on your user's device. This is known as a Remote Code Execution attack (or RCE).

While this may not directly affect your application (unless it's the target of such an attack), you can still help users
to secure their device by reminding them of their responsibility to only install trusted software from reputable
vendors.

There's very little that can be done to mitigate this kind of attack in practice, just the same as any application you
install now on your device could use any other application installed.
