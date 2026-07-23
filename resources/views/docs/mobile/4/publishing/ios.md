---
title: iOS
order: 30
---

Package and sign your app for the Apple App Store. Read the [Introduction](introduction) first, and make sure
you've cut a [release build](introduction#releasing).

## Required Signing Credentials

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

## Setting Up App Store Connect API

To upload to the App Store directly from the command line, you'll need an API key:

1. Log in to [App Store Connect](https://appstoreconnect.apple.com)
2. Navigate to Users & Access → Keys
3. Click the "+" button to create a new key with "Developer" access
4. Download the `.p8` file immediately (you can't download it again later)
5. Note the Key ID and Issuer ID displayed on the page

## Export Methods

The `--export-method` option controls how your app is packaged:

- `app-store` - For App Store distribution (default)
- `ad-hoc` - For distribution to specific registered devices
- `enterprise` - For enterprise distribution (requires enterprise program)
- `development` - For development and testing

## Building for App Store

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

## Building for Ad-Hoc Distribution

For distributing to specific devices without going through the App Store:

```bash
php artisan native:package ios \
  --export-method=ad-hoc \
  --certificate-path=/path/to/distribution.p12 \
  --certificate-password=certificatepassword \
  --provisioning-profile-path=/path/to/ad-hoc-profile.mobileprovision
```

## Building for Development

For testing on your own device:

```bash
php artisan native:package ios \
  --export-method=development \
  --certificate-path=/path/to/development.p12 \
  --certificate-password=certificatepassword \
  --provisioning-profile-path=/path/to/development-profile.mobileprovision
```

## Using Environment Variables

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

## Uploading to App Store Connect

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

## Validating Provisioning Profiles

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

## Testing App Store Uploads

To test uploading an existing IPA without rebuilding:

```bash
php artisan native:package ios \
  --test-upload \
  --api-key-path=/path/to/api-key.p8 \
  --api-key-id=ABC123DEF \
  --api-issuer-id=01234567-89ab-cdef-0123-456789abcdef
```

## Clearing Xcode Caches

If you encounter build issues, clear Xcode and Swift Package Manager caches:

```bash
php artisan native:package ios \
  --export-method=app-store \
  --clean-caches
```

## Forcing a Clean Rebuild

To force a complete rebuild and create a new archive:

```bash
php artisan native:package ios \
  --export-method=app-store \
  --rebuild
```

## Validating Without Exporting

To validate the archive without creating an IPA:

```bash
php artisan native:package ios \
  --validate-only
```

## Version Numbers

`native:release` is a convenient way to bump your app's version number — pass `major`, `minor`, or `patch`:

```bash
php artisan native:release patch
```

Build numbers (`NATIVEPHP_APP_VERSION_CODE`) are incremented automatically based on your `.env`, so you don't have to
manage them by hand.

## Artifact Locations

Once complete, your signed IPA is generated in Xcode's build output directory.
