<x-mail::message>
# Great news!

You've been granted access to **{{ $product->name }}**.

<x-mail::button :url="$url">
Check it out
</x-mail::button>

Thank you for being a NativePHP customer!
</x-mail::message>
