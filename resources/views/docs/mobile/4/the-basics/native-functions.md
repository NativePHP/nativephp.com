---
title: Native Functions
order: 100
---

Our custom PHP extension enables tight integration with each platform, providing a consistent and performant abstraction
that lets you focus on building your app. Build for both platforms while you develop on one.

Native device functions are called directly from your PHP code, giving you access to platform-specific features while
maintaining the productivity and familiarity of Laravel development.

These functions are called from your PHP code using an ever-growing set of [Plugins](../plugins/). Plugins contain PHP classes that
are also wrapped in Laravel Facades for ease of access and testing, such as:

```php
Native\Mobile\Facades\Biometrics
Native\Mobile\Facades\Browser
Native\Mobile\Facades\Camera
```

Each of these is covered in their respective plugin's documentation.


## Run from anywhere

All native APIs are called through PHP. This means that your application is not reliant upon a web view to
function.

However, when using a web view you may also interact with the native functions easily from JavaScript using
our convenient `Native` library.

This is especially useful if you're building applications with a SPA framework, like Vue or React, as you can simply
import the functions you need and move a lot of work into the reactive part of your UI.

### Install the Node plugin

To use the `Native` JavaScript library, you must install the plugin in your `package.json` file. Add the following
section to the JSON: 

```js
{
    "dependencies": {
        ...
    },
    "imports": { // [tl! focus:start] 
        "#nativephp": "./vendor/nativephp/mobile/resources/dist/native.js"
    } // [tl! focus:end]
}
```

Run `npm install`, then in your JavaScript, simply import the relevant functions from the plugin:

```js
import { On, Off, Microphone, Events } from '#nativephp';
import { onMounted, onUnmounted } from 'vue';

const buttonClicked = () => {
    Microphone.record();
};

const handleRecordingFinished = () => {
    // Update the UI
};

onMounted(() => {
    On(Events.Microphone.MicrophoneRecorded, handleRecordingFinished);
});

onUnmounted(() => {
    Off(Events.Microphone.MicrophoneRecorded, handleRecordingFinished);
});
```

The library is fully typed, so your IDE should be able to pick up the available properties and methods to provide you
with inline hints and code completion support.

For the most part, the JavaScript APIs mirror the PHP APIs. Any key differences are noted in each Plugin's docs.

This approach uses the PHP interface under the hood, meaning the two implementations stay in lock-step with each other.
So you'll never need to worry about whether an API is available only in one place or the other; they're all always
available wherever you need them.

<aside>

#### Found a bug?

Community support is available via [Discord]({{ $discordLink }}) and
[GitHub Issues](https://github.com/nativephp/mobile-air/issues/new).

</aside>
