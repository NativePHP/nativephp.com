---
title: Android
order: 20
---

Package and sign your app for the Google Play Store. Read the [Introduction](introduction) first, and make sure
you've cut a [release build](introduction#releasing).

## Creating Signing Credentials

NativePHP provides a convenient command to generate all the signing credentials you need for Android:

```bash
php artisan native:credentials android
```

This command will:
- Generate a new JKS keystore file
- Create all necessary signing keys
- Automatically add the credentials to your `.env` file
- Add the keystore to your `.gitignore` to keep it secure

The credentials will be saved in the `nativephp/credentials/android/` directory and automatically configured for use with the package command.

## Required Signing Credentials

To build a signed Android app, you need four pieces of information:

| Credential | Option | Environment Variable | Description |
|-----------|--------|----------------------|-------------|
| Keystore file | `--keystore` | `ANDROID_KEYSTORE_FILE` | Path to your `.keystore` file |
| Keystore password | `--keystore-password` | `ANDROID_KEYSTORE_PASSWORD` | Password for the keystore |
| Key alias | `--key-alias` | `ANDROID_KEY_ALIAS` | Name of the key within the keystore |
| Key password | `--key-password` | `ANDROID_KEY_PASSWORD` | Password for the specific key |

## Building a Release APK

An APK (Android Package) is a single binary file suitable for direct distribution or testing on specific devices:

```bash
php artisan native:package android \
  --keystore=/path/to/my-app.keystore \
  --keystore-password=mykeystorepassword \
  --key-alias=my-app-key \
  --key-password=mykeypassword
```

The build process prepares your Android project, compiles the code, and signs the APK with your certificate. When complete, the output directory opens automatically, showing your signed `app-release.apk` file.

## Building an Android App Bundle (AAB)

An AAB (Android App Bundle) is required for distribution through the Play Store. It's an optimized format that the Play Store uses to generate device-specific APKs automatically:

```bash
php artisan native:package android \
  --build-type=bundle \
  --keystore=/path/to/my-app.keystore \
  --keystore-password=mykeystorepassword \
  --key-alias=my-app-key \
  --key-password=mykeypassword
```

This creates a signed `app-release.aab` file ready for Play Store submission.

## Using Environment Variables

Instead of passing credentials as command options, you can store them in your `.env` file:

```env
ANDROID_KEYSTORE_FILE=/path/to/my-app.keystore
ANDROID_KEYSTORE_PASSWORD=mykeystorepassword
ANDROID_KEY_ALIAS=my-app-key
ANDROID_KEY_PASSWORD=mykeypassword
```

Then simply run:

```bash
php artisan native:package android --build-type=bundle
```

## Uploading to Play Store

If you have a Google Service Account with Play Console access, you can upload directly from the command:

```bash
php artisan native:package android \
  --build-type=bundle \
  --keystore=/path/to/my-app.keystore \
  --keystore-password=mykeystorepassword \
  --key-alias=my-app-key \
  --key-password=mykeypassword \
  --upload-to-play-store \
  --play-store-track=internal \
  --google-service-key=/path/to/service-account-key.json
```

The `--play-store-track` option controls where the build is released:

- `internal` - Internal testing (default, fastest review)
- `alpha` - Closed alpha testing
- `beta` - Closed beta testing
- `production` - Production release

## Testing Play Store Uploads

If you already have an AAB file and want to test uploading without rebuilding, use `--test-push`:

```bash
php artisan native:package android \
  --test-push=/path/to/app-release.aab \
  --upload-to-play-store \
  --play-store-track=internal \
  --google-service-key=/path/to/service-account-key.json
```

This skips the entire build process and only handles the upload.

## Skipping Build Preparation

For incremental builds where you haven't changed native code, you can skip the preparation phase:

```bash
php artisan native:package android \
  --build-type=bundle \
  --keystore=/path/to/my-app.keystore \
  --keystore-password=mykeystorepassword \
  --key-alias=my-app-key \
  --key-password=mykeypassword \
  --skip-prepare
```

## Auto-Incrementing Version Codes

The `native:version` command handles version management. When building AABs for the Play Store with valid Google Service credentials, NativePHP automatically checks the Play Store for the latest build number and increments it.

When building a bundle with Play Store access:

```bash
php artisan native:package android \
  --build-type=bundle \
  --keystore=/path/to/my-app.keystore \
  --keystore-password=mykeystorepassword \
  --key-alias=my-app-key \
  --key-password=mykeypassword \
  --google-service-key=/path/to/service-account-key.json
```

The command automatically queries the Play Store to find your latest published build number and increments it.

## Jumping Ahead in Version Numbers

If you need to skip version numbers or jump ahead:

```bash
php artisan native:package android \
  --build-type=bundle \
  --keystore=/path/to/my-app.keystore \
  --keystore-password=mykeystorepassword \
  --key-alias=my-app-key \
  --key-password=mykeypassword \
  --jump-by=10
```

This adds 10 to the version code that would normally be used.

## Custom Output Directories

By default, the build output opens in your system's file manager. To copy the artifact to a custom location:

```bash
php artisan native:package android \
  --build-type=bundle \
  --keystore=/path/to/my-app.keystore \
  --keystore-password=mykeystorepassword \
  --key-alias=my-app-key \
  --key-password=mykeypassword \
  --output=/path/to/custom/directory
```

## Building in Non-Interactive Environments

For CI/CD pipelines or automated builds, disable TTY mode:

```bash
php artisan native:package android \
  --build-type=bundle \
  --keystore=/path/to/my-app.keystore \
  --keystore-password=mykeystorepassword \
  --key-alias=my-app-key \
  --key-password=mykeypassword \
  --no-tty
```

## Common Issues

**Keystore-related errors:**
- Verify the keystore file exists and the path is correct
- Check that the keystore password is correct
- Confirm the key alias exists in the keystore with: `keytool -list -v -keystore /path/to/keystore`
- Verify the key password matches

**Build failures:**
- Ensure you have the latest Android SDK and build tools installed
- Check that your `nativephp/android` directory exists and is properly initialized
- If you've modified native code, don't use `--skip-prepare`

**Play Store upload failures:**
- Verify the Google Service Account has access to your app in Play Console
- Ensure the service account key file is valid and readable
- Check that your bundle ID matches your Play Console app ID

## Artifact Locations

Once complete, signed artifacts are located at:

- APK (release): `nativephp/android/app/build/outputs/apk/release/app-release.apk`
- AAB (bundle): `nativephp/android/app/build/outputs/bundle/release/app-release.aab`
