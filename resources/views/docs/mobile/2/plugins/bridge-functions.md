---
title: Bridge Functions
order: 400
---

## How Bridge Functions Work

Bridge functions are the connection between your PHP code and native platform code. When you call a method like
`MyPlugin::doSomething()`, NativePHP routes that to your Swift or Kotlin implementation running on the device.

The flow:
1. PHP calls `nativephp_call('MyPlugin.DoSomething', $params)`
2. The native bridge locates the registered function
3. Your native code executes and returns a response
4. PHP receives the result

## Declaring Bridge Functions

In your `nativephp.json`, declare each function with its platform implementations:

```json
{
    "bridge_functions": [
        {
            "name": "MyPlugin.DoSomething",
            "ios": "MyPluginFunctions.DoSomething",
            "android": "com.vendor.plugin.myplugin.MyPluginFunctions.DoSomething",
            "description": "Does something useful"
        }
    ]
}
```

The `name` is what PHP uses. The platform-specific values point to your native class and method.

## Swift Implementation (iOS)

Create your functions in `resources/ios/Sources/`:

```swift
import Foundation

enum MyPluginFunctions {

    class DoSomething: BridgeFunction {
        func execute(parameters: [String: Any]) throws -> [String: Any] {
            let option = parameters["option"] as? String ?? ""

            // Do your native work here

            return BridgeResponse.success(data: [
                "result": "completed",
                "option": option
            ])
        }
    }
}
```

Key points:
- Implement the `BridgeFunction` protocol
- Parameters come as a dictionary
- Return using `BridgeResponse.success()` or `BridgeResponse.error()`

## Kotlin Implementation (Android)

Create your functions in `resources/android/src/.../`:

```kotlin
package com.vendor.plugin.myplugin

import com.example.androidphp.bridge.BridgeFunction
import com.example.androidphp.bridge.BridgeResponse

object MyPluginFunctions {

    class DoSomething : BridgeFunction {
        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            val option = parameters["option"] as? String ?: ""

            // Do your native work here

            return BridgeResponse.success(mapOf(
                "result" to "completed",
                "option" to option
            ))
        }
    }
}
```

<aside>

#### Package Naming

Use your plugin's namespace in the Kotlin package name. The scaffolding command sets this up correctly.

</aside>

## Calling from PHP

Create a facade method that calls your bridge function:

```php
class MyPlugin
{
    public function doSomething(array $options = []): mixed
    {
        $result = nativephp_call('MyPlugin.DoSomething', json_encode($options));

        return json_decode($result)?->data;
    }
}
```

## Error Handling

Return errors from native code using `BridgeResponse.error()`:

```swift
// Swift
return BridgeResponse.error(message: "Something went wrong")
```

```kotlin
// Kotlin
return BridgeResponse.error("Something went wrong")
```

The error message is available in PHP through the response.
