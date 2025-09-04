---
title: Introduction
order: 1
---

## Welcome to the revolution!

NativePHP for Mobile is the first library of its kind that lets you run full PHP applications natively on mobile
devices — no web server required.

By embedding a statically compiled PHP runtime alongside Laravel, and bridging directly into each platform’s native
APIs, NativePHP brings the power of modern PHP to truly native mobile apps. Build performant, offline-capable
experiences using the tools you already know.

**It's never been this easy to build beautiful, local-first apps for iOS and Android.**

## What makes NativePHP for Mobile special?

- 📱 **Native performance**  
    Your app runs natively through an embedded PHP runtime optimized for each platform.  
- 🔥 **True mobile APIs**  
    Access camera, biometrics, push notifications, and more. One cohesive library that does it all.
- ⚡ **Laravel powered**  
    Leverage the entire Laravel ecosystem and your existing skillset.
- 🚫 **No web server required**  
    Your app runs entirely on-device and can operate completely offline-first.
- 🔄 **Cross platform**  
    Build apps for both iOS and Android from a single codebase.

## Old tools, new tricks

With NativePHP for Mobile, you don’t need to learn Swift, Kotlin, or anything new.
No new languages. No unfamiliar build tools. No fighting with Gradle or Xcode.

Just PHP.

Developers around the world are using the skills they already have to build and ship real mobile apps — faster than
ever. In just a few hours, you can go from code to app store submission.

## How does it work?

On the simplest level:

1. A statically-compiled version of PHP is bundled with your code into a Swift/Kotlin shell application.
2. NativePHP's custom Swift/Kotlin bridges manage the PHP environment, running your PHP code directly.
3. A custom PHP extension is compiled into PHP, that exposes PHP interfaces to native functions.
4. Your app renders in a native web view, so you can continue developing your UI the way you're used to.

You simply interact with an easy-to-use set of functions from PHP and everything just works!

## Batteries included

NativePHP for Mobile is way more than just a web view wrapper for your server-based application. Your application lives
_on device_ and is shipped with each installation.

Thanks to our custom PHP extension, you can interact with many native APIs today, with more coming all the time,
including:

- 📷 Camera & Gallery access
- 🔐 Biometric authentication (Face ID, Touch ID, Fingerprint)
- 🔔 Push notifications via APNs (for iOS) or Firebase (both)
- 💬 Native dialogs & toasts
- 🔗 Deep links & universal links
- 📳 Haptic feedback & vibration
- 🔦 Flashlight control
- 📤 Native sharing
- 🔒 Secure storage (Keychain/Keystore)
- 📍 Location services

You have the full power of PHP and Laravel at your fingertips... literally! And you're not sandboxed into the web view;
this goes way beyond what's possible with PWAs and WASM without any of the complexity... we've got full-cream PHP at
the ready!

**What are you waiting for!? [Let's go!](quick-start)**
