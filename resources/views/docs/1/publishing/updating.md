---
title: Updating
order: 300
---
# The Updater
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
- [DigitalOcean](https://docs.digitalocean.com/products/spaces/how-to/manage-access/)
- Amazon S3 - See [this video](https://www.youtube.com/watch?v=FLIp6BLtwjk&ab_channel=CloudCasts) by Chris Fidao or 
    this [Step 2](https://www.twilio.com/docs/video/tutorials/storing-aws-s3#step-2) of this article by Twilio

  If you got the error message "The bucket does not allow ACLs" you can follow this guide from [Learn AWS](https://www.learnaws.org/2023/08/26/aws-s3-bucket-does-not-allow-acls)
      on how to setup your bucket correctly.

## Disabling the updater

If you don't want your application to check for updates, you can disable the updater by setting the
`updater.enabled` option to `false` in your `config/nativephp.php` file or via your `.env` file:

```dotenv
NATIVEPHP_UPDATER_ENABLED=false
```
