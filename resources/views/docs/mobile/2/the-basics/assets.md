---
title: Assets
order: 500
---

## Compiling CSS and JavaScript

If you are using React, Vue or another JavaScript library, or Tailwind CSS, tools that requires your frontend to be
built by build tooling like Vite, you will need to run your build process _before_ compiling the native application.

For example, if you're using Vite with NPM to build a React application that is using Tailwind, to ensure that your
latest styles and JavaScript are included, always run `npm run build` before running `php artisan native:run`.

## Other files

NativePHP will include all files from the root of your Laravel application. So you can store any files that you wish to
make available to your application wherever makes the most sense for you.

<aside class="relative z-0 mt-5 overflow-hidden rounded-2xl bg-pink-50 px-5 ring-1 ring-black/5 dark:bg-pink-600/10">

#### Accessing arbitrary files

All files must be accessed using _relative_ paths from the root of your app. Use Laravel's
[Path helpers](https://laravel.com/docs/12.x/helpers#paths) to access files in the appropriate location.

Note that the `storage_path()` helper points to a location _outside_ of your Laravel application's root.

</aside>
