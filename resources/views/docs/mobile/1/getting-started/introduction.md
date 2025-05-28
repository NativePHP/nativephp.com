---
title: Introduction
order: 001
---

## Welcome to the revolution!

NativePHP for Mobile is a first of its kind library that allows PHP developers to run PHP applications natively on
all sorts of mobile devices _without a web server_.

We've combined the statically compiling PHP as an embeddable C library with the flexibility of Laravel and the rich
native APIs of each support platform, unlocking the power and convenience of Laravel for building performant, native
_mobile_ applications using PHP.

**It's never been this easy to build beautiful, local-first apps for iOS and Android.**

## Old tools, new tricks

With NativePHP for Mobile, you don't have to learn any new languages or ecosystems. Stop fighting with other package
managers and build tools. Stay in the comfort of PHP and Composer!

PHP developers all over the world are building incredible mobile experiences with the skills they already possess.
In just a few hours you can build an app and have it submitted to the app stores for review.

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

Thanks to our custom PHP extension, you can interact with many native APIs today, with more coming each week, including:

- Camera & Microphone
- Biometric ID
- Vibration
- Accelerometer, GPS and other sensors
- Push notifications, native alerts and other native UI elements

You have the full power of PHP and Laravel at your fingertips... literally! And you're not sandboxed into the web view;
this goes way beyond what's possible with PWAs and WASM without any of the complexity... we've got full-cream PHP ready
to go!

**What are you waiting for!? [Let's go!](installation)**
