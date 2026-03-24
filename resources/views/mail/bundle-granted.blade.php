<x-mail::message>
# Great news!

You've been granted access to the **{{ $bundle->name }}** bundle, which includes the following plugins:

@foreach ($grantedPlugins as $plugin)
- [{{ $plugin->name }}]({{ $pluginUrls[$plugin->id] }})
@endforeach

Thank you for being a NativePHP customer!
</x-mail::message>
