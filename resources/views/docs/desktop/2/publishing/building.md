---
title: Building
order: 100
---

## Building Your App

Building your app is the process of compiling your application into a production-ready state. When building, NativePHP
attempts to sign and notarize your application. Once signed, your app is ready to be distributed.

## Securing

Before you prepare a distributable build, please make sure you've been through the
[Security guide](/docs/digging-deeper/security).

## Building

The build process compiles your app for one platform at a time. It compiles your application along with the
Electron runtime into a single executable.

Once built, you can distribute your app however you prefer, but NativePHP also provides a [publish command](publishing)
that will automatically upload your build artifacts to your chosen [provider](/docs/publishing/updating) - this allows
your app to provide automatic updates.

You should build your application for each platform you intend to support and test it on each platform _before_
publishing to make sure that everything works as expected.

### Running commands before and after builds
Many applications rely on a tool such as [Vite](https://vitejs.dev/) or [Webpack](https://webpack.js.org/) to compile their CSS and JS assets before a production build.

To facilitate this, NativePHP provides two hooks that you can use to run commands before and after the build process.

To utilise these hooks, add the following to your `config/nativephp.php` file:

```php
'prebuild' => [
    'npm run build', // Run a command before the build
    'php artisan optimize', // Run another command before the build
],
'postbuild' => [
    'npm run release', // Run a command after the build
],
```

These commands will be run in the root of your project directory and you can specify as many as required.

## Versioning

For every build you create, you should change the version of your application in your app's `config/nativephp.php` file.

This can be any format you choose, but you may find that a simple incrementing build number is the easiest to manage.

**Migrations will only run on the user's machine if the version reference is _different_ to the currently-installed version.**

You may choose to have a different version number that uses a different scheme (e.g. SemVer) that you use for user-facing
releases.

## Running a build

```shell
php artisan native:build
```

This will build for the platform and architecture where you are running the build.

### Cross-compilation

You can also specify a platform to build for by passing the `os` argument, so for example you could build for Windows
whilst on a Mac:

```shell
php artisan native:build win
```

Possible options are: `mac`, `win`, `linux`.

**Cross-compilation is not supported on all platforms.**

#### Cross-compilation on Linux

Compiling Windows binaries is possible with [wine](https://www.winehq.org/).
NSIS requires 32-bit wine when building x64 applications.

```bash
# Example installation of wine for Debian based distributions (Ubuntu)
dpkg --add-architecture i386
apt-get -y update
apt-get -y install wine32
```

## Code signing

Both macOS and Windows require your app to be signed before it can be distributed to your users.

NativePHP makes this as easy for you as it can, but each platform does have slightly different requirements.

### Windows

NativePHP supports two methods for Windows code signing: traditional certificate-based signing and Azure Trusted Signing.

#### Azure Trusted Signing (Recommended)

Azure Trusted Signing is a cloud-based code signing service that eliminates the need to manage local certificates. 

When building your application, you can identify which signing method is being used:
- **Azure Trusted Signing**: The build output will show "Signing with Azure Trusted Signing (beta)"
- **Traditional Certificate**: The build output will show "Signing with signtool.exe"

To use Azure Trusted Signing, add the following environment variables to your `.env` file:

```dotenv
# Azure AD authentication
AZURE_TENANT_ID=your-tenant-id
AZURE_CLIENT_ID=your-client-id
AZURE_CLIENT_SECRET=your-client-secret

# Azure Trusted Signing configuration
# This is the CommonName (CN) value - your full name or company name
# as entered in the Identity Validation Request form
NATIVEPHP_AZURE_PUBLISHER_NAME=your-publisher-name

# The endpoint URL for the Azure region where your certificate is stored
NATIVEPHP_AZURE_ENDPOINT=https://eus.codesigning.azure.net/

# The name of your certificate profile (NOT the Trusted Signing Account)
NATIVEPHP_AZURE_CERTIFICATE_PROFILE_NAME=your-certificate-profile

# Your Trusted Signing Account name (NOT the app registration display name)
# This is the account name shown in Azure Trusted Signing, not your login name
NATIVEPHP_AZURE_CODE_SIGNING_ACCOUNT_NAME=your-code-signing-account
```

These credentials will be automatically stripped from your built application for security.

#### Traditional Certificate Signing

For traditional certificate-based signing, [see the Electron documentation](https://www.electronforge.io/guides/code-signing/code-signing-windows) for more details.

### macOS

[See the Electron documentation](https://www.electronforge.io/guides/code-signing/code-signing-macos) for more details.

To prepare for signing and notarizing, please provide the following environment variables when running
`php artisan native:build`:

```dotenv
NATIVEPHP_APPLE_ID=developer@abcwidgets.com
NATIVEPHP_APPLE_ID_PASS=app-specific-password
NATIVEPHP_APPLE_TEAM_ID=8XCUU22SN2
```

These can be added to your `.env` file as they will be stripped out when your app is built.

Without proper notarization your app will only run on the development machine. Other Macs will show a "app is damaged and can't be opened" warning.
This is a security feature in macOS that prevents running unsigned or improperly notarized applications. Make sure to complete the notarization process to avoid this issue.

## First run

When your application runs for the first time, a number of things occur.

NativePHP will:

1. Create the `appdata` folder - where this is created depends which platform you're developing on. It is named
   according to your `nativephp.app_id` [config](/docs/getting-started/configuration) value (which is based on the
   `NATIVEPHP_APP_ID` env variable).
2. Creating the `{appdata}/database/database.sqlite` SQLite database - your user's copy of your app's database.
3. Migrate this database.

If you wish to seed the user's database, you should run this somewhere that runs
[every time your app boots](/docs/the-basics/app-lifecycle#codeApplicationBootedcode).

Check if the database was already seeded and, if not, run the appropriate `db:seed` command. For example:

```php
use App\Models\Config;
use Illuminate\Support\Facades\Artisan;

if (Config::where('seeded', true)->count() === 1) {
    Artisan::call('db:seed');
}
```

## Subsequent runs

Each time a user opens your app, NativePHP will check to see if the [app version](#versioning) has changed and attempt
to migrate the user's copy of your database in their `appdata` folder.

This is why you should change the version identifier for each release.
