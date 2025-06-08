---
title: Native Functions
order: 100
---

Nearly any basic Laravel app will work as a mobile app with NativePHP for Mobile. However, what makes NativePHP
unique is that it allows you to call native functions from your PHP code.

These functions are called from your PHP code using one of an ever-growing list of facades.

Currently, there are two facades available:

- `Native\Mobile\Facades\System`
- `Native\Mobile\Facades\Dialog`

## System

The `System` facade is used to call native functions that access system resources.

For example, you may use the `System::camera()` method to request access to the device's camera.


## Synchronous vs. Asynchronous Methods

It is important to understand the difference between synchronous and asynchronous methods. Some methods
like `flashlight` and `vibrate` are synchronous, meaning that they will block the current thread until the
operation is complete. 

Other methods like `camera` and `biometric` are asynchronous, meaning that they
will return immediately and the operation will be performed in the background. When the operation is
complete, the method will `broadcast an event` to your frontend via an injected javascript event as well 
as a traditional [Laravel Event](https://laravel.com/docs/12.x/events#main-content) that you can listen for within your app.

In order to receive these events, you must register a listener for the event. For example,
take a look at how easy it is to listen for a `PhotoTaken` event in Livewire:

```php
use Livewire\Attributes\On;
use Livewire\Component;
use Native\Mobile\Facades\System;
use Native\Mobile\Events\Camera\PhotoTaken;

class Camera extends Component
{
    public string $photoDataUrl = '';

    public function camera()
    {
       System::camera();
    }

    #[On('native:' . PhotoTaken::class)]
    public function handleCamera($path)
    {
        $data   = base64_encode(file_get_contents($path));
        $mime   = mime_content_type($path);

        $this->photoDataUrl = "data:$mime;base64,$data";
    }

    public function render()
    {
        return view('livewire.system.camera');
    }
}
```

All Livewire/front end events are prefixed with `native:` to avoid collisions with other events. 
In the following pages we will highlight the available native functions, whether they are asynchronous or not
and which events they fire.
