@props(['height' => 'h-24', 'sameHeight' => true])
<a href="https://beyondco.de/?utm_source=nativephp&utm_medium=logo&utm_campaign=nativephp" title="BeyondCode sponsor">
    <img src="/img/sponsors/beyondcode.png" class="block dark:hidden {{ $height }} w-auto max-w-full">
    <img src="/img/sponsors/beyondcode-dark.png" class="hidden dark:block {{ $height }} w-auto max-w-full">
</a>

<a href="https://laradevs.com/?ref=nativephp" title="Laradevs sponsor">
    <x-sponsors.laradir class="text-black dark:text-white block {{ $height }} {{ $sameHeight ? 'py-[5%] m-auto':''  }} max-w-full"/>
</a>
