---
title: Development
order: 300
---

# Development

```shell
php artisan native:serve
```

NativePHP isn't prescriptive about how you develop your application. You can build it in the way you're most comfortable
and familiar with, just as if you were building a traditional web application.

The only difference comes in the feedback cycle. Instead of switching to and refreshing your browser, you'll need to
be serving your application using `php artisan native:serve` and refreshing (and in some cases restarting) your
application to see changes.

This is known as 'running a dev build'.

## What does the `native:serve` command do?

The `native:serve` command runs the Electron/Tauri 'debug build' commands, which build your application with various
debug options set to help make debugging easier, such as allowing you to show the Dev Tools in the embedded web view.

It also keeps the connection to the terminal open so you can see and inspect useful output from your app, such as logs,
in real time.

These builds are unsigned and not meant for distribution. They do not go through various optimizations typically done
when [building your application for production](/docs/publishing) and so they expose more about the inner workings of
the code than you would typically want to share with your users.

A major part of the build process, even for debug builds, involves _copying_ your application code into the runtime's
build environment. This means that changes you make to your application code _will not_ be reflected in your running
application until you restart it.

You can stop the `native:serve` command by pressing `Ctrl-C` on your keyboard in the terminal window it's running in.

Alternatively, you can use hot reloading.

### App Icon

The `native:serve` and `native:build` commands look for the following icon files when building your application:

- `public/icon.png` - your main icon, used on the Desktop, Dock and app switcher.
- `public/IconTemplate.png` - used in the Menu Bar on non-retina displays.
- `public/IconTemplate@2x.png` - used in the Menu Bar on retina displays.

If any of these files exist, they will be moved into the relevant location to be used as your application's icons.
You simply need to follow the naming convention.

Your main icon should be at least 512x512 pixels.

## Hot Reloading

Hot reloading is an awesome feature for automatically seeing changes to your application during development. NativePHP
supports hot reloading of certain files within its core and your application, but it does _not_ watch all of your
source code for changes. It is left to you to determine how you want to approach this.

If you're using Vite, hot reloading will just work inside your app as long as you've booted your Vite dev server and
[included the Vite script tag](https://laravel.com/docs/vite#loading-your-scripts-and-styles) in your views
(ideally in your app's main layout file).

You can do this easily in Blade using the `@@vite` directive.

Then, in a separate terminal session to your `php artisan native:serve`, from the root folder of your application, run:

```shell
npm run dev
```

Now changes you make to files in your source code will cause a hot reload in your running application.

Which files trigger reloads will depend on your Vite configuration.

## First run

When your application runs for the first time, a number of things occur.

NativePHP will:

1. Create the `appdata` folder - where this is created depends which platform you're developing on. It is named
  according to your `nativephp.app_id` config value (which is based on the `NATIVEPHP_APP_ID` env variable).
2. Create a SQLite database
3. Migrate the database

This is identical to what will happen when a new user runs a production build of your app for the first time on their
device.

**If you change your `app_id`, a new `appdata` folder may need to be created and all of these steps will need to run
again. No previous files will be deleted.**

## Subsequent runs

Every time your application boots, NativePHP checks that these files exist and creates them if they don't. It also tries
to run your migrations. This is done with the `--force` flag so that it is fully automated even in production.
