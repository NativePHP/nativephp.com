---
title: Deployment
order: 300
---

Deploying mobile apps is a complicated process — and it's different for each platform! Generally speaking you need to:

1. **Releasing**: Create a _release build_ for each platform.
2. **Testing**: Test this build on real devices.
3. **Packaging**: Sign and distribute this build to the stores.
4. **Submitting for Review**: Go through each store's submission process to have your app reviewed.
5. **Publishing**: Releasing the new version to the stores and your users.

It's initially more time-consuming when creating a brand new app in the stores, as you need to get the listing set up
in each store and create your signing credentials.

If you've never done it before, allow a couple of hours so you can focus on getting things right and understand
everything you need.

Don't rush through the app store processes! There are compliance items that if handled incorrectly will either prevent
you from publishing your app, being unable to release it in the territories you want to make it available to, or simply
having it get rejected immediately when you submit it for review if you don't get those right.

It's typically easier once you've released the first version of your app and after you've done 2 or 3 apps, you'll fly
through the process!

<aside>

#### Need help?

This page is here to help you _configure and use NativePHP_ to prepare your app for release; it is not a guide around
the stores. You should consult the [App Store Connect Help](https://developer.apple.com/help/app-store-connect/) and
[Play Console Help](https://support.google.com/googleplay/android-developer/?hl=en-GB#topic=3450769) documentation for
detailed and up-to-date guidance on how to prepare your app submissions and listings.

If you want more hands-on support, we happily work with our [Partners](/partners) to support them releasing their apps.

</aside>

## Releasing

To prepare your app for release, you should set the version number to a new version number that you have not used
before and increment the build number:

```dotenv
NATIVEPHP_APP_VERSION=1.2.3
NATIVEPHP_APP_VERSION_CODE=48
```

### Versioning

You have complete freedom in how you version your applications. You may use semantic versioning, codenames,
date-based versions, or any scheme that works for your project, team or business.

Remember that your app versions are usually public-facing (e.g. in store listings and on-device settings and update
screens) and can be useful for customers to reference if they need to contact you for help and support.

The build number is managed via the `NATIVEPHP_APP_VERSION` key in your `.env`.

### Build numbers

Both the Google Play Store and Apple App Store require your app's build number to increase for each release you submit.

The build number is managed via the `NATIVEPHP_APP_VERSION_CODE` key in your `.env`.

### Run a `release` build

Then run a release build:

```shell
php artisan native:run --build=release
```

This builds your application with various optimizations that reduce its overall size and improve its performance, such
as removing debugging code and unnecessary features (i.e. Composer dev dependencies).

**You should test this build on a real device.** Once you're happy that everything is working as intended you can then
submit it to the stores for approval and distribution.

- [Google Play Store submission guidelines](https://support.google.com/googleplay/android-developer/answer/9859152?hl=en-GB#zippy=%2Cmaximum-size-limit)
- [Apple App Store submission guidelines](https://developer.apple.com/ios/submit/)

## Packaging Your App

The `native:package` command creates signed, production-ready apps for distribution to the App Store and Play Store.
This command handles all the complexity of code signing, building release artifacts, and preparing files for submission.

<aside>

#### There's an Easier Way

App packaging and distribution is a technically challenging task, even for experienced developers. Managing certificates, provisioning profiles, keystores, and coordinating deployments across teams can be frustrating and time-consuming.

That's why we built [Bifrost](https://bifrost.nativephp.com) - a platform that handles all of this complexity for you:

- **Set credentials once per app** - No more managing certificates and profiles locally
- **Team collaboration** - Share apps with your team and manage access easily
- **Auto-deploy** - Push updates automatically without manual builds
- **Over-the-air updates** - Deploy changes to users instantly without app store approval
- **Low monthly cost** - Simple, affordable pricing

While the package command gives you complete control, Bifrost saves you countless hours of setup and maintenance.

</aside>

## Before You Begin

Before you can package your app for distribution, ensure:

1. Your app is fully developed and tested on both platforms
2. You have a valid bundle ID and app ID configured in your `nativephp.php` config
3. For Android: You have a signing keystore with a valid key alias
4. For iOS: You have the necessary signing certificates and provisioning profiles from Apple Developer
5. All configuration is complete (see the [configuration guide](/docs/mobile/2/getting-started/configuration))

## Android Packaging

### Creating Android Signing Credentials

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

### Required Signing Credentials

To build a signed Android app, you need four pieces of information:

| Credential | Option | Environment Variable | Description |
|-----------|--------|----------------------|-------------|
| Keystore file | `--keystore` | `ANDROID_KEYSTORE_FILE` | Path to your `.keystore` file |
| Keystore password | `--keystore-password` | `ANDROID_KEYSTORE_PASSWORD` | Password for the keystore |
| Key alias | `--key-alias` | `ANDROID_KEY_ALIAS` | Name of the key within the keystore |
| Key password | `--key-password` | `ANDROID_KEY_PASSWORD` | Password for the specific key |

### Building a Release APK

An APK (Android Package) is a single binary file suitable for direct distribution or testing on specific devices:

```bash
php artisan native:package android \
  --keystore=/path/to/my-app.keystore \
  --keystore-password=mykeystorepassword \
  --key-alias=my-app-key \
  --key-password=mykeypassword
```

The build process prepares your Android project, compiles the code, and signs the APK with your certificate. When complete, the output directory opens automatically, showing your signed `app-release.apk` file.

### Building an Android App Bundle (AAB)

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

### Using Environment Variables

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

### Uploading to Play Store

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

### Testing Play Store Uploads

If you already have an AAB file and want to test uploading without rebuilding, use `--test-push`:

```bash
php artisan native:package android \
  --test-push=/path/to/app-release.aab \
  --upload-to-play-store \
  --play-store-track=internal \
  --google-service-key=/path/to/service-account-key.json
```

This skips the entire build process and only handles the upload.

### Skipping Build Preparation

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

## iOS Packaging

### Required Signing Credentials

iOS apps require several credentials from Apple Developer:

| Credential | Option | Environment Variable | Description |
|-----------|--------|----------------------|-------------|
| API Key file | `--api-key-path` | `APP_STORE_API_KEY_PATH` | Path to `.p8` file from App Store Connect |
| API Key ID | `--api-key-id` | `APP_STORE_API_KEY_ID` | Key ID from App Store Connect |
| API Issuer ID | `--api-issuer-id` | `APP_STORE_API_ISSUER_ID` | Issuer ID from App Store Connect |
| Certificate | `--certificate-path` | `IOS_DISTRIBUTION_CERTIFICATE_PATH` | Distribution certificate (`.p12` or `.cer`) |
| Certificate password | `--certificate-password` | `IOS_DISTRIBUTION_CERTIFICATE_PASSWORD` | Password for the certificate |
| Provisioning profile | `--provisioning-profile-path` | `IOS_DISTRIBUTION_PROVISIONING_PROFILE_PATH` | Profile file (`.mobileprovision`) |
| Team ID | `--team-id` | `IOS_TEAM_ID` | Apple Developer Team ID |

### Setting Up App Store Connect API

To upload to the App Store directly from the command line, you'll need an API key:

1. Log in to [App Store Connect](https://appstoreconnect.apple.com)
2. Navigate to Users & Access → Keys
3. Click the "+" button to create a new key with "Developer" access
4. Download the `.p8` file immediately (you can't download it again later)
5. Note the Key ID and Issuer ID displayed on the page

### Export Methods

The `--export-method` option controls how your app is packaged:

- `app-store` - For App Store distribution (default)
- `ad-hoc` - For distribution to specific registered devices
- `enterprise` - For enterprise distribution (requires enterprise program)
- `development` - For development and testing

### Building for App Store

To build a production app ready for App Store submission:

```bash
php artisan native:package ios \
  --export-method=app-store \
  --api-key-path=/path/to/api-key.p8 \
  --api-key-id=ABC123DEF \
  --api-issuer-id=01234567-89ab-cdef-0123-456789abcdef \
  --certificate-path=/path/to/distribution.p12 \
  --certificate-password=certificatepassword \
  --provisioning-profile-path=/path/to/profile.mobileprovision \
  --team-id=ABC1234567
```

### Building for Ad-Hoc Distribution

For distributing to specific devices without going through the App Store:

```bash
php artisan native:package ios \
  --export-method=ad-hoc \
  --certificate-path=/path/to/distribution.p12 \
  --certificate-password=certificatepassword \
  --provisioning-profile-path=/path/to/ad-hoc-profile.mobileprovision
```

### Building for Development

For testing on your own device:

```bash
php artisan native:package ios \
  --export-method=development \
  --certificate-path=/path/to/development.p12 \
  --certificate-password=certificatepassword \
  --provisioning-profile-path=/path/to/development-profile.mobileprovision
```

### Using Environment Variables

Store your iOS credentials in `.env`:

```env
APP_STORE_API_KEY_PATH=/path/to/api-key.p8
APP_STORE_API_KEY_ID=ABC123DEF
APP_STORE_API_ISSUER_ID=01234567-89ab-cdef-0123-456789abcdef
IOS_DISTRIBUTION_CERTIFICATE_PATH=/path/to/distribution.p12
IOS_DISTRIBUTION_CERTIFICATE_PASSWORD=certificatepassword
IOS_DISTRIBUTION_PROVISIONING_PROFILE_PATH=/path/to/profile.mobileprovision
IOS_TEAM_ID=ABC1234567
```

Then build with:

```bash
php artisan native:package ios --export-method=app-store
```

### Uploading to App Store Connect

To automatically upload after building:

```bash
php artisan native:package ios \
  --export-method=app-store \
  --api-key-path=/path/to/api-key.p8 \
  --api-key-id=ABC123DEF \
  --api-issuer-id=01234567-89ab-cdef-0123-456789abcdef \
  --certificate-path=/path/to/distribution.p12 \
  --certificate-password=certificatepassword \
  --provisioning-profile-path=/path/to/profile.mobileprovision \
  --team-id=ABC1234567 \
  --upload-to-app-store
```

The upload uses the App Store Connect API, so API credentials are required only when using `--upload-to-app-store`.

### Validating Provisioning Profiles

Before building, you can validate your provisioning profile to check push notification support and entitlements:

```bash
php artisan native:package ios \
  --validate-profile \
  --provisioning-profile-path=/path/to/profile.mobileprovision
```

This extracts and displays:

- Profile name
- All entitlements configured in the profile
- Push notification support status
- Associated domains
- APS environment matching

### Testing App Store Uploads

To test uploading an existing IPA without rebuilding:

```bash
php artisan native:package ios \
  --test-upload \
  --api-key-path=/path/to/api-key.p8 \
  --api-key-id=ABC123DEF \
  --api-issuer-id=01234567-89ab-cdef-0123-456789abcdef
```

### Clearing Xcode Caches

If you encounter build issues, clear Xcode and Swift Package Manager caches:

```bash
php artisan native:package ios \
  --export-method=app-store \
  --clean-caches
```

### Forcing a Clean Rebuild

To force a complete rebuild and create a new archive:

```bash
php artisan native:package ios \
  --export-method=app-store \
  --rebuild
```

### Validating Without Exporting

To validate the archive without creating an IPA:

```bash
php artisan native:package ios \
  --validate-only
```

## Version Management

### Build Numbers and Version Codes

The `native:version` command handles version management. When building AABs for the Play Store with valid Google Service credentials, NativePHP automatically checks the Play Store for the latest build number and increments it.

### Auto-Incrementing from Play Store

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

### Jumping Ahead in Version Numbers

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

## Tips & Troubleshooting

### Common Android Issues

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

### Custom Output Directories

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

### Building in Non-Interactive Environments

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

### Artifact Locations

Once complete, signed artifacts are located at:

**Android:**
- APK (release): `nativephp/android/app/build/outputs/apk/release/app-release.apk`
- AAB (bundle): `nativephp/android/app/build/outputs/bundle/release/app-release.aab`

**iOS:**
- IPA: Generated in Xcode's build output directory
