---
title: Environment Setup
order: 100
---

## Requirements

1. PHP 8.3+
2. Laravel 11+
3. [A NativePHP for Mobile license](https://nativephp.com/mobile)

If you don't already have PHP installed on your machine, the most painless way to get PHP up and running on Mac and
Windows is with <a href="https://herd.laravel.com" target="_blank">Laravel Herd</a>. It's fast and free!

## iOS Requirements

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

#### Heads up!

You cannot build iOS apps on Windows or Linux. This is a limitation imposed by Apple.

</aside>

1. macOS (required - iOS development is only possible on a Mac)
2. <a href="https://apps.apple.com/app/xcode/id497799835" target="_blank">Xcode 16.0 or later</a>
3. Xcode Command Line Tools
4. Homebrew & CocoaPods
5. _Optional_ iOS device for testing

### Setting up iOS Development Environment

1. **Install Xcode**
   - Download from the <a href="https://apps.apple.com/app/xcode/id497799835" target="_blank">Mac App Store</a>
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
You **do not** need to enroll in the <a href="https://developer.apple.com/programs/enroll/" target="_blank">Apple Developer Program</a> ($99/year)
to develop and test your apps on a Simulator. However, you will need to enroll when you want to:
- Test your apps on real devices
- Distribute your apps via the App Store

## Android Requirements

1. <a href="https://developer.android.com/studio" target="_blank">Android Studio 2024.2.1 or later</a>
2. Android SDK with API 23 or higher
3. **Windows only**: You must have <a href="https://www.7-zip.org/" target="_blank">7zip</a> installed.

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

#### Note

You do not need to separately install the Java Development Kit (JDK). Android Studio will automatically install the
proper JDK for you.

</aside> 

### Setting up Android Studio and SDK

1. **Download and Install Android Studio**
   - Download from the <a href="https://developer.android.com/studio" target="_blank">Android Studio download page</a>
   - Minimum version required: Android Studio 2024.2.1

2. **Install Android SDK**
   - Open Android Studio
   - Navigate to **Tools → SDK Manager**
   - In the **SDK Platforms** tab, install at least one Android SDK platform for API 23 or higher
     - Latest stable version: Android 15 (API 35)
     - You only need to install one API version to get started
   - In the **SDK Tools** tab, ensure **Android SDK Build-Tools** and **Android SDK Platform-Tools** are installed

That's it! Android Studio handles all the necessary configuration automatically.

### Preparing for NativePHP

1. Check that you can run `java -v` and `adb devices` from the terminal.
2. The following environment variables set:

#### On macOS
```shell
export JAVA_HOME=$(/usr/libexec/java_home -v 17) // This isn't required if JAVA_HOME is already set in your environment variables (check using `printenv | grep JAVA_HOME`)
export ANDROID_HOME=$HOME/Library/Android/sdk
export PATH=$PATH:$JAVA_HOME/bin:$ANDROID_HOME/emulator:$ANDROID_HOME/tools:$ANDROID_HOME/tools/bin:$ANDROID_HOME/platform-tools
```

#### On Windows
```shell
set ANDROID_HOME=C:\Users\yourname\AppData\Local\Android\Sdk
set PATH=%PATH%;%JAVA_HOME%\bin;%ANDROID_HOME%\platform-tools

# This isn't required if JAVA_HOME is already set in the Windows Env Variables
set JAVA_HOME=C:\Program Files\Microsoft\jdk-17.0.8.7-hotspot
```

## Testing on Real Devices

You don't _need_ a physical iOS/Android device to compile and test your application, as NativePHP for Mobile supports
the iOS Simulator and Android emulators. However, we highly recommend that you test your application on a real device
before submitting to the Apple App Store and Google Play Store.

### On iOS
If you want to run your app on a real iOS device, you need to make sure it is in
<a href="https://developer.apple.com/documentation/xcode/enabling-developer-mode-on-a-device" target="_blank">Developer Mode</a>
and that it's been added to your Apple Developer account as
<a href="https://developer.apple.com/account/resources/devices/list" target="_blank">a registered device</a>.

### On Android
On Android you need to <a href="https://developer.android.com/studio/debug/dev-options#enable" target="_blank">enable developer options</a>
and have USB debugging (ADB) enabled.
