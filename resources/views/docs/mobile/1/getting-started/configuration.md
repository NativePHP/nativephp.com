---
title: Configuration
order: 200
---

## Overview

NativePHP for Mobile is designed so that most configuration happens **inside your Laravel application**, without requiring you to manually open Xcode or Android Studio.

After installation, NativePHP sets up the necessary native scaffolding, but your primary interaction remains inside Laravel itself.

This page explains the key configuration points you can control directly through Laravel.

## The `nativephp.php` Config File

The nativephp.php config file is where you can configure the native project for your application. 

NativePHP uses sensible defaults and makes several assumptions based on default installations for tools required to build and run apps from your computer. 

You can override these defaults by editing the `nativephp.php` config file in your Laravel project or changing environment variables.

```dotenv
NATIVEPHP_APP_VERSION 
NATIVEPHP_APP_VERSION_CODE 
NATIVEPHP_APP_ID 
NATIVEPHP_DEEPLINK_SCHEME 
NATIVEPHP_DEEPLINK_HOST 
NATIVEPHP_APP_AUTHOR 
NATIVEPHP_GRADLE_PATH 
NATIVEPHP_ANDROID_SDK_LOCATION
```

## Cleanup `env` keys

The `cleanup_env_keys` array in the config file allows you to specify keys that should be removed from the `.env` file before bundling. 
This is useful for removing sensitive information like API keys or other secrets.

## Cleanup `exclude_files`

The `cleanup_exclude_files` array in the config file allows you to specify files and folders that should be removed before bundling. 
This is useful for removing files like logs or other temporary files.

## Permissions
In general, the app stores don't want apps to request permissions that they don't need. 
To enable some permissions your app needs, you simply need to change their values in the permissions section.

```dotenv
biometric
camera
nfc
location
```
