---
title: Environment Files
order: 400
---

# Environment Files

When NativePHP bundles your application, it will copy your entire application directory into the bundle, including your
`.env` file.

**This means that your `.env` file will be accessible to anyone who has access to your application bundle.**

So you should be careful to not include any sensitive information in your `.env` file, such as API keys or passwords.
This is quite unlike a traditional web application deployed to a server you control.

If you need to perform any sensitive operations, such as accessing an API or database, you should do so using a
separate API that you create specifically for your application. You can then call _this_ API from your application and
have it perform the sensitive operations on your behalf.

See [Security](/digging-deeper/security) for more tips.

## Removing sensitive data from your environment files

There are certain environment variables that NativePHP uses internally, for example to configure your application's
updater, or Apple's notarization service.

These environment variables are automatically removed from your `.env` file when your application is bundled, so you
don't need to worry about them being exposed.

If you want to remove other environment variables from your `.env` file, you can do so by adding them to the
`cleanup_env_keys` configuration option in your `nativephp.php` config file:

```php
    /**
     * A list of environment keys that should be removed from the
     * .env file when the application is bundled for production.
     * You may use wildcards to match multiple keys.
     */
    'cleanup_env_keys' => [
        'AWS_*',
        'DO_SPACES_*',
        '*_SECRET',
        'NATIVEPHP_UPDATER_PATH',
        'NATIVEPHP_APPLE_ID',
        'NATIVEPHP_APPLE_ID_PASS',
        'NATIVEPHP_APPLE_TEAM_ID',
    ],
```
