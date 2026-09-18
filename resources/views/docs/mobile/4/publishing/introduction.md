---
title: Introduction
order: 10
---

Deploying mobile apps is a complicated process — and it's different for each platform!

<aside>

#### There's an Easier Way

Managing certificates, provisioning profiles, keystores, and coordinating deployments across teams can be frustrating
and time-consuming.

[Bifrost](https://bifrost.nativephp.com) handles all of this complexity for you.

- **Set credentials once per app** - No more managing certificates and profiles locally
- **Team collaboration** - Share apps with your team and manage access easily
- **Auto-deploy** - Push updates automatically without manual builds
- **Over-the-air updates** - Deploy changes to users instantly without app store approval
- **Low monthly cost** - Simple, affordable pricing

</aside>

You will be doing the following for both platforms:

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

If you want more [hands-on support](/consulting), we happily work with our [Partners](/partners) to support them releasing their apps.

</aside>

## Releasing

To prepare your app for release, bump the version number using the
[`native:release` command](../getting-started/commands#nativerelease):

```shell
php artisan native:release patch
```

You can pass `patch`, `minor`, or `major` depending on the type of release you're cutting. This updates
`NATIVEPHP_APP_VERSION` in your `.env` and increments the build number for you.

### Versioning

App version numbers should follow [semantic versioning](https://semver.org) (e.g. `1.2.3`). The `native:release` command
relies on this format to determine how to bump your version.

Remember that your app versions are usually public-facing (e.g. in store listings and on-device settings and update
screens) and can be useful for customers to reference if they need to contact you for help and support.

The app version is managed via the `NATIVEPHP_APP_VERSION` key in your `.env`.

### Build numbers

Both the Google Play Store and Apple App Store require your app's build number to increase for each release you submit.

The build number is managed via the `NATIVEPHP_APP_VERSION_CODE` key in your `.env`. You don't need to manage this
yourself — running `native:release` automatically increments the build number and persists it back to your `.env`.

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

## Production and testing builds

<x-docs.version-badge since="4.5" />

The `APP_ENV` in your `.env` when you package your app decides how far that build can be released. Only a build
packaged with `APP_ENV=production` can be released to the public. Any other value, such as `local` or `staging`, makes
it a testing build, and NativePHP tells both stores so:

- **Play Store:** the build declares closed testing as the largest audience it may reach. It can go to the internal and
  closed testing tracks, but Google Play won't release it on the open testing or production tracks.
- **App Store:** the build is marked for TestFlight internal testing only. Your internal testers can install it, but it
  can't be sent to external testers or submitted for App Store review.

This is written into the build itself, so a testing build can't be promoted to a public release later.

New Laravel apps start with `APP_ENV=local`, so set it before you package a release:

```env
APP_ENV=production
```

Your `.env` is bundled into the app, so it runs in the same environment on the device.

<aside>

#### Share builds with testers safely

Package with `APP_ENV=staging` when you want to hand a build to testers. It can reach your internal TestFlight testers
and the Play Store's internal and closed testing tracks, and it can never be released to the public by mistake.

</aside>

## Packaging Your App

The `native:package` command creates signed, production-ready apps for distribution to the App Store and Play Store.
This command handles all the complexity of code signing, building release artifacts, and preparing files for submission.

Packaging is platform-specific — follow the [Android](android) or [iOS](ios) guide for step-by-step instructions.

## Before You Begin

Before you can package your app for distribution, ensure:

1. Your app is fully developed and tested on both platforms
2. You have a valid bundle ID and app ID configured in your `nativephp.php` config
3. For Android: You have a signing keystore with a valid key alias
4. For iOS: You have the necessary signing certificates and provisioning profiles from Apple Developer
5. All configuration is complete (see the [configuration guide](../getting-started/configuration))
6. For a public release, your `.env` has `APP_ENV=production` (see [Production and testing builds](#production-and-testing-builds))
