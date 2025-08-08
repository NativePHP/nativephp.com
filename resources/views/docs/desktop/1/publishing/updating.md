---
title: Updating
order: 300
---

## The Updater

NativePHP ships with a built-in auto-update tool, which allows your users to update your application without needing to
manually download and install new releases.

This leaves you to focus on building and releasing new versions of your application, without needing to worry about
distributing those updates to your users.

**macOS: Automatic updating is only supported for [signed](/docs/publishing/building#signing-and-notarizing)
applications.**

## How it works

The updater works by checking a remote URL for a new version of your application. If a new version is found, the updater
will download the new version and replace the existing application files with the new ones.

This means your application's builds need to be hosted online. NativePHP will automatically upload your application for
you. After configuring the updater, simply use the [`php artisan native:publish`](/docs/publishing/publishing) command.

The updater supports three providers:

- GitHub Releases (`github`)
- Amazon S3 (`s3`)
- DigitalOcean Spaces (`spaces`)

You can configure all settings for the updater in your `config/nativephp.php` file or via your `.env` file.

**The updater will only run when your app is running in production mode.**

## Configuration

The default updater configuration looks like this:

```php
    'updater' => [
        'enabled' => env('NATIVEPHP_UPDATER_ENABLED', true),

        'default' => env('NATIVEPHP_UPDATER_PROVIDER', 'spaces'),

        'providers' => [
            'github' => [
                'driver' => 'github',
                'repo' => env('GITHUB_REPO'),
                'owner' => env('GITHUB_OWNER'),
                'token' => env('GITHUB_TOKEN'),
                'vPrefixedTagName' => env('GITHUB_V_PREFIXED_TAG_NAME', true),
                'private' => env('GITHUB_PRIVATE', false),
                'channel' => env('GITHUB_CHANNEL', 'latest'),
                'releaseType' => env('GITHUB_RELEASE_TYPE', 'draft'),
            ],

            's3' => [
                'driver' => 's3',
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
                'region' => env('AWS_DEFAULT_REGION'),
                'bucket' => env('AWS_BUCKET'),
                'endpoint' => env('AWS_ENDPOINT'),
                'path' => env('NATIVEPHP_UPDATER_PATH', null),
            ],

            'spaces' => [
                'driver' => 'spaces',
                'key' => env('DO_SPACES_KEY_ID'),
                'secret' => env('DO_SPACES_SECRET_ACCESS_KEY'),
                'name' => env('DO_SPACES_NAME'),
                'region' => env('DO_SPACES_REGION'),
                'path' => env('NATIVEPHP_UPDATER_PATH', null),
            ],
        ],
    ],
```

How to setup your storage and generate the relevant API credentials:

- <a href="https://docs.digitalocean.com/products/spaces/how-to/manage-access/" target="_blank">DigitalOcean</a>
- Amazon S3 - See <a href="https://www.youtube.com/watch?v=FLIp6BLtwjk&ab_channel=CloudCasts" target="_blank">this video</a> by Chris Fidao or
  this <a href="https://www.twilio.com/docs/video/tutorials/storing-aws-s3#step-2" target="_blank">Step 2</a> of this article by Twilio

  If you got the error message "The bucket does not allow ACLs" you can follow this guide
  from <a href="https://www.learnaws.org/2023/08/26/aws-s3-bucket-does-not-allow-acls" target="_blank">Learn AWS</a>
  on how to setup your bucket correctly.

## Disabling the updater

If you don't want your application to check for updates, you can disable the updater by setting the
`updater.enabled` option to `false` in your `config/nativephp.php` file or via your `.env` file:

```dotenv
NATIVEPHP_UPDATER_ENABLED=false
```

## Manually checking for updates

You can manually check for updates by calling the `checkForUpdates` method on the `AutoUpdater` facade:

```php
use Native\Laravel\Facades\AutoUpdater;

AutoUpdater::checkForUpdates();
```

**Note:** If an update is available, it will be downloaded automatically. Calling `AutoUpdater::checkForUpdates() twice
will download the update two times.

## Quit and Install

You can quit the application and install the update by calling the `quitAndInstall` method on the `AutoUpdater` facade:

```php
use Native\Laravel\Facades\AutoUpdater;

AutoUpdater::quitAndInstall();
```

This will quit the application and install the update. The application will then relaunch automatically.

**Note:** Calling this method is optional — any successfully downloaded update will be applied the next time the
application starts.

## Events

### `CheckingForUpdate`

The `Native\Laravel\Events\AutoUpdater\CheckingForUpdate` event is dispatched when checking for an available update has
started.

### `UpdateAvailable`

The `Native\Laravel\Events\AutoUpdater\UpdateAvailable` event is dispatched when there is an available update. The
update is downloaded automatically.

### `UpdateNotAvailable`

The `Native\Laravel\Events\AutoUpdater\UpdateNotAvailable` event is dispatched when there is no available update.

### `DownloadProgress`

The `Native\Laravel\Events\AutoUpdater\DownloadProgress` event is dispatched when the update is being downloaded.

The event contains the following properties:

- `total`: The total size of the update in bytes.
- `delta`: The size of the update that has been downloaded since the last event.
- `transferred`: The total size of the update that has been downloaded.
- `percent`: The percentage of the update that has been downloaded (0-100).
- `bytesPerSecond`: The download speed in bytes per second.

### `UpdateDownloaded`

The `Native\Laravel\Events\AutoUpdater\UpdateDownloaded` event is dispatched when the update has been downloaded.

The event contains the following properties:

- `version`: The version of the update.
- `downloadedFile`: The local path to the downloaded update file.
- `releaseDate`: The release date of the update in ISO 8601 format.
- `releaseNotes`: The release notes of the update.
- `releaseName`: The name of the update.

### `Error`

The `Native\Laravel\Events\AutoUpdater\Error` event is dispatched when there is an error while updating.

The event contains the following properties:

- `error`: The error message.
