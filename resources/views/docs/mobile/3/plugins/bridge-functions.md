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
            "android": "com.myvendor.plugins.myplugin.MyPluginFunctions.DoSomething",
            "description": "Does something useful"
        }
    ]
}
```

The `name` is what PHP uses. The platform-specific values point to your native class and method.

### Naming Convention

- **`name`** — A unique identifier like `MyPlugin.DoSomething`. This is what PHP code uses.
- **`ios`** — Swift enum/class path: `EnumName.ClassName`
- **`android`** — Full Kotlin class path including your vendor package (e.g., `com.myvendor.plugins.myplugin.ClassName`)

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

Create your functions in `resources/android/src/`. Use your own vendor-namespaced package:

```kotlin
package com.myvendor.plugins.myplugin

import com.nativephp.mobile.bridge.BridgeFunction
import com.nativephp.mobile.bridge.BridgeResponse

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

The package declaration determines where your file is placed during compilation. Using `com.myvendor.plugins.myplugin` ensures
your code is isolated from other plugins and the core NativePHP code.

## Calling from PHP

Create a facade method that calls your bridge function:

```php
class MyPlugin
{
    public function doSomething(array $options = []): mixed
    {
        if (function_exists('nativephp_call')) {
            $result = nativephp_call('MyPlugin.DoSomething', json_encode($options));

            return json_decode($result)?->data;
        }

        return null;
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

## Official Plugins & Dev Kit

Need native functionality without writing Kotlin or Swift? Browse ready-made plugins or get the Dev Kit to build your own.
[Visit the NativePHP Plugin Marketplace →](https://nativephp.com/plugins)
