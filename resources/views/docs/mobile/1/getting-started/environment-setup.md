---
title: Environment Setup
order: 100
---

## Requirements

1. PHP 8.3+
2. Laravel 11+
3. [A NativePHP for Mobile license](https://nativephp.com/mobile)

If you don't already have PHP installed on your machine, the most painless way to get PHP up and running on Mac and
Windows is with [Laravel Herd](https://herd.laravel.com). It's fast and free!

## iOS Requirements

<aside>

#### Heads up!

You cannot build iOS apps on Windows or Linux. This is a limitation imposed by Apple. But we've got you covered with
[Bifrost](https://bifrost.nativephp.com)!

</aside>

1. macOS (required - iOS development is only possible on a Mac)
2. [Xcode 16.0 or later](https://apps.apple.com/app/xcode/id497799835)
3. Xcode Command Line Tools
4. Homebrew & CocoaPods
5. _Optional_ iOS device for testing

### Setting up iOS Development Environment

1. **Install Xcode**
   - Download from the [Mac App Store](https://apps.apple.com/app/xcode/id497799835)
   - Minimum version: Xcode 16.0

2. **Install Xcode Command Line Tools**
   ```shell
   xcode-select --install
   ```
   Verify installation:
   ```shell
   xcode-select -p
   ```

3. **Install Homebrew** (if not already installed)
   ```shell
   /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
   ```

4. **Install CocoaPods**
   ```shell
   brew install cocoapods
   ```
   Verify installation:
   ```shell
   pod --version
   ```

### Apple Developer Account
You **do not** need to enroll in the [Apple Developer Program](https://developer.apple.com/programs/enroll/) ($99/year)
to develop and test your apps on a Simulator. However, you will need to enroll when you want to:
- Test your apps on real devices
- Distribute your apps via the App Store

## Android Requirements

1. [Android Studio 2024.2.1 or later](https://developer.android.com/studio)
2. Android SDK with API 33 or higher
3. **Windows only**: You must have [7zip](https://www.7-zip.org/) installed.

<aside>

#### Note

You might need to install the Java Development Kit (JDK) separately. Recent versions of Android Studio no longer install it automatically.
If you encounter Gradle errors, check the [Gradle JDK Compatibility Matrix](https://docs.gradle.org/current/userguide/compatibility.html).
The latest JDK version may not be supported yet.

To check the installed Gradle version, examine the ``nativephp/android/.gradle`` folder after running ``php artisan native:install``.

</aside> 

### Setting up Android Studio and SDK

1. **Download and Install Android Studio**
   - Download from the [Android Studio download page](https://developer.android.com/studio)
   - Minimum version required: Android Studio 2024.2.1

2. **Install Android SDK**
   - Open Android Studio
   - Navigate to **Tools → SDK Manager**
   - In the **SDK Platforms** tab, install at least one Android SDK platform for API 33 or higher
     - Latest stable version: Android 15 (API 35)
     - You only need to install one API version to get started
   - In the **SDK Tools** tab, ensure **Android SDK Build-Tools** and **Android SDK Platform-Tools** are installed

That's it! Android Studio handles all the necessary configuration automatically.

### Preparing for NativePHP

1. Check that you can run `java -version` and `adb devices` from the terminal.
2. The following environment variables set:

#### On macOS
```shell
# This isn't required if JAVA_HOME is already set in your environment variables (check using `printenv | grep JAVA_HOME`)
export JAVA_HOME=$(/usr/libexec/java_home -v 17) 

export ANDROID_HOME=$HOME/Library/Android/sdk
export PATH=$PATH:$JAVA_HOME/bin:$ANDROID_HOME/emulator:$ANDROID_HOME/tools:$ANDROID_HOME/tools/bin:$ANDROID_HOME/platform-tools
```

#### On Windows
The example below assumes default installation paths for the Android SDK and JDK:

```shell
set ANDROID_HOME=C:\Users\yourname\AppData\Local\Android\Sdk
set PATH=%PATH%;%JAVA_HOME%\bin;%ANDROID_HOME%\platform-tools

# This isn't required if JAVA_HOME is already set in the Windows Env Variables
set JAVA_HOME=C:\Program Files\Microsoft\jdk-17.0.8.7-hotspot
```

### "No AVDs found" error
If you encounter this error, it means no Virtual Devices are configured in Android Studio.
To resolve it, open Android Studio, navigate to Virtual Devices, and create at least one device.

## Testing on Real Devices

You don't _need_ a physical iOS/Android device to compile and test your application, as NativePHP for Mobile supports
the iOS Simulator and Android emulators. However, we highly recommend that you test your application on a real device
before submitting to the Apple App Store and Google Play Store.

### On iOS
If you want to run your app on a real iOS device, you need to make sure it is in
[Developer Mode](https://developer.apple.com/documentation/xcode/enabling-developer-mode-on-a-device)
and that it's been added to your Apple Developer account as
[a registered device](https://developer.apple.com/account/resources/devices/list).

### On Android
On Android you need to [enable developer options](https://developer.android.com/studio/debug/dev-options#enable)
and have USB debugging (ADB) enabled.
