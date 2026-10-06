---
title: Splash Screens
order: 400
---

NativePHP makes it easy to add custom splash screens to your iOS and Android apps.

## Supply your Splash Screens

Place the relevant files in the locations specified:

- `public/splash.png` - for the Light Mode splash screen
- `public/splash-dark.png` - for the Dark Mode splash screen

### Requirements
- Format: PNG (WebP is also accepted on Android, see below)
- Minimum Size/Ratio: 1080 × 1920 pixels
- GD PHP extension must be enabled, ensure it has enough memory (~2GB should be enough) 

## Android Image Format

<x-docs.version-badge since="4.6" />

When building for Android, NativePHP resizes your splash screens (and [app icon](app-icon)) into a bitmap for every
screen density. By default these are written as PNG files.

Full-screen splash images are large, and multiplied across five densities and two themes they can add tens of
megabytes to your app bundle. Google Play Console flags this under "Optimize your app's images".

To ship smaller files, set the `image_format` option under the `android` key in `config/nativephp.php` to `webp`:

```php
'android' => [
    'image_format' => env('NATIVEPHP_ANDROID_IMAGE_FORMAT', 'png'),
],
```

```dotenv
NATIVEPHP_ANDROID_IMAGE_FORMAT=webp
```

- `png` - Default. Compressed PNG files.
- `webp` - Lossless WebP files. Same visual output, usually 80-90% smaller than PNG.

When switching formats, the files from the previous format are removed on the next build, so you won't get
duplicate resource errors.

You may also supply `public/splash.webp` and `public/splash-dark.webp` as sources for Android. When both a `.webp`
and a `.png` exist, the `.webp` is used.

<aside>

WebP output requires your PHP GD extension to be compiled with WebP support. If it isn't, NativePHP falls back to
PNG output instead of failing the build. You can check with `php -r "var_dump(function_exists('imagewebp'));"`.

iOS always uses the PNG files, so keep `public/splash.png` and `public/splash-dark.png` if you build for iOS.

</aside>
