---
title: Mobile
order: 1
---

# NativePHP for Mobile

**Build native iOS and Android apps with PHP and Laravel**

NativePHP for Mobile revolutionizes mobile development by allowing PHP developers to create native mobile applications using the languages and frameworks they already know and love. No need to learn Swift, Kotlin, or React Native - just pure PHP and Laravel.

## What Makes NativePHP Mobile Special?

📱**Native Performance** - Your app runs natively on device with embedded PHP runtime  
🔥**True Mobile APIs** - Access camera, biometrics, push notifications, and more  
⚡ **Laravel Powered** - Use your existing Laravel skills and ecosystem  
🚫**No Web Server** - Your app runs entirely on-device  
🔄**Cross Platform** - Single codebase for iOS and Android  

## Quick Start

Get your first mobile app running in minutes:

```bash
# Install NativePHP Mobile
composer require nativephp/mobile

# Configure your app
php artisan native:install

# Run your app
php artisan native:run
```

## Current Features (v1.1)

**Available now:**
- 📷 Camera & Gallery access
- 🔐 Biometric authentication (Face ID, Touch ID, Fingerprint)
- 🔔 Push notifications via Firebase
- 💬 Native dialogs & toasts
- 🔗 Deep links & universal links
- 📱 NFC support
- 📳 Haptic feedback & vibration
- 🔦 Flashlight control
- 📤 Native sharing
- 🔒 Secure storage (Keychain/Keystore)
- 📍 Location services

[See the complete roadmap 🗺️](/docs/mobile/1/getting-started/roadmap)

## Documentation Sections

### [Getting Started](/docs/mobile/1/getting-started)
Everything you need to start building mobile apps with PHP:
- [Introduction](/docs/mobile/1/getting-started/introduction) - Learn how NativePHP Mobile works
- [Installation](/docs/mobile/1/getting-started/installation) - Set up your development environment
- [Environment Setup](/docs/mobile/1/getting-started/environment-setup) - Configure iOS and Android tools
- [Configuration](/docs/mobile/1/getting-started/configuration) - App settings and permissions
- [Development](/docs/mobile/1/getting-started/development) - Development workflow
- [Roadmap](/docs/mobile/1/getting-started/roadmap) - Current and planned features

### [The Basics](/docs/mobile/1/the-basics)
Core concepts and fundamental knowledge:
- [Native Functions](/docs/mobile/1/the-basics/native-functions) - Understanding sync vs async APIs
- [Asynchronous Methods](/docs/mobile/1/the-basics/asynchronous-methods) - Event-driven mobile development
- [ICU Support](/docs/mobile/1/the-basics/icu-support) - International components for Unicode

### [Concepts](/docs/mobile/1/concepts)
Important concepts for mobile app development:
- [CI/CD](/docs/mobile/1/concepts/ci-cd) - Continuous integration and deployment
- [Deep Links](/docs/mobile/1/concepts/deep-links) - Universal links, app links, and NFC
- [Push Notifications](/docs/mobile/1/concepts/push-notifications) - Firebase Cloud Messaging setup
- [Splash Screen/Icons](/docs/mobile/1/concepts/splash-screen-icons) - App branding and assets
- [Versioning](/docs/mobile/1/concepts/versioning) - App version management

### [APIs](/docs/mobile/1/apis)
Complete API reference for all native features:
- [Biometrics](/docs/mobile/1/apis/biometrics) - Face ID, Touch ID, fingerprint authentication
- [Camera](/docs/mobile/1/apis/camera) - Photo capture and gallery access
- [Dialog](/docs/mobile/1/apis/dialog) - Alerts, toasts, and sharing
- [Geolocation](/docs/mobile/1/apis/geolocation) - GPS and location services
- [Haptics](/docs/mobile/1/apis/haptics) - Vibration and tactile feedback
- [PushNotifications](/docs/mobile/1/apis/push-notifications) - FCM token management
- [SecureStorage](/docs/mobile/1/apis/secure-storage) - Keychain and keystore operations
- [System](/docs/mobile/1/apis/system) - Flashlight and legacy methods

### [Digging Deeper](/docs/mobile/1/digging-deeper)
Advanced topics for production apps:
- [Databases](/docs/mobile/1/digging-deeper/databases) - SQLite and data management
- [Security](/docs/mobile/1/digging-deeper/security) - Best practices and secure storage

## Need Help?

- **License Required** - [Purchase your license](https://nativephp.com/mobile) to get started
- **Community** - Join our Discord for support and discussions
- **Examples** - Check out the Kitchen Sink demo app (coming soon to app stores)

Ready to build your first mobile app with PHP? [Let's get started! 🚀](/docs/mobile/1/getting-started/introduction)
