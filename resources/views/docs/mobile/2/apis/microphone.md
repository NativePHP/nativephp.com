---
title: Microphone
order: 900
---

## Overview

The Microphone API provides access to the device's microphone for recording audio. It offers a fluent interface for
starting and managing recordings, tracking them with unique identifiers, and responding to completion events.

```php
use Native\Mobile\Facades\Microphone;
```

## Methods

### `record()`

Start an audio recording. Returns a `PendingMicrophone` instance that controls the recording lifecycle.

```php
Microphone::record()->start();
```

### `stop()`

Stop the current audio recording. If this results in a saved file, this dispatches the `AudioRecorded` event with the
recording file path.

```php
Microphone::stop();
```

### `pause()`

Pause the current audio recording without ending it.

```php
Microphone::pause();
```

### `resume()`

Resume a paused audio recording.

```php
Microphone::resume();
```

### `getStatus()`

Get the current recording status.

**Returns:** `string` - One of: `"idle"`, `"recording"`, or `"paused"`

```php
$status = Microphone::getStatus();

if ($status === 'recording') {
    // A recording is in progress
}
```

### `getRecording()`

Get the file path to the last recorded audio file.

**Returns:** `string|null` - Path to the last recording, or `null` if none exists

```php
$path = Microphone::getRecording();

if ($path) {
    // Process the recording file
}
```

## PendingMicrophone

The `PendingMicrophone` provides a fluent interface for configuring and starting audio recordings. Most methods return
`$this` for method chaining.

### `id(string $id)`

Set a unique identifier for this recording. This ID will be included in the `AudioRecorded` event, allowing you to
correlate recordings with completion events.

```php
$recorderId = 'message-recording-' . $this->id;

Microphone::record()
    ->id($recorderId)
    ->start();
```

### `getId()`

Get the recorder's unique identifier. If no ID was set, one is automatically generated (UUID v4).

```php
$recorder = Microphone::record()
    ->id('my-recording');

$id = $recorder->getId(); // 'my-recording'
```

### `event(string $eventClass)`

Set a custom event class to dispatch when recording completes. By default, `AudioRecorded` is used.

**Throws:** `InvalidArgumentException` if the event class does not exist

```php
use App\Events\VoiceMessageRecorded;

Microphone::record()
    ->event(VoiceMessageRecorded::class)
    ->start();
```

### `remember()`

Store the recorder's ID in the session for later retrieval. This is useful when the recording completes on the next request.

```php
Microphone::record()
    ->id('voice-note')
    ->remember()
    ->start();
```

### `lastId()`

Retrieve the last remembered audio recorder ID from the session. Use this in event listeners to correlate recordings.

```php
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Microphone\MicrophoneRecorded;

#[OnNative(MicrophoneRecorded::class)]
public function handleAudioRecorded(string $path, string $mimeType, ?string $id)
{
    // For comparing with remembered IDs
    if ($id === Audio::record()->lastId()) {
        $this->saveRecording($path);
    }
}
```

### `start()`

Explicitly start the audio recording. This is optional - recordings auto-start if you don't call this method.

**Returns:** `bool` - `true` if recording started successfully, `false` if it failed or was already started

```php
$recorder = Microphone::record()->id('my-recording');

if ($recorder->start()) {
    // Recording started
} else {
    // Recording failed - likely due to permission denial
}
```

## Events

### `MicrophoneRecorded`

Dispatched when an audio recording completes. The event includes the file path and recording ID.

**Payload:**
- `string $path` - File path to the recorded audio
- `string $mimeType` - MIME type of the audio (default: `'audio/m4a'`)
- `?string $id` - The recorder's ID, if one was set

```php
#[OnNative(MicrophoneRecorded::class)]
public function handleAudioRecorded(string $path, string $mimeType, ?string $id)
{
    // Store or process the recording
    $this->recordings[] = [
        'path' => $path,
        'mimeType' => $mimeType,
        'id' => $id,
    ];
}
```

## Notes

- **Microphone Permission:** The first time your app requests microphone access, users will be prompted for permission. If denied, recording functions will fail silently.

- **Microphone Background Permission:** You can allow your app to record audio while the device is locked by toggling `microphone_background` to true in [the config](../getting-started/configuration)

- **File Format:** Recordings are stored as M4A/AAC audio files (`.m4a`). This format is optimized for small file sizes while maintaining quality.

- **Recording State:** Only one recording can be active at a time. Calling `start()` while a recording is in progress will return `false`.

- **Auto-Start Behavior:** If you don't explicitly call `start()`, the recording will automatically start when the `PendingMicrophone is destroyed. This maintains backward compatibility with earlier versions.
