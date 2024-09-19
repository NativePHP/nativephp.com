@props(['height' => 'h-24', 'sameHeight' => true])
<a href="https://beyondco.de/?utm_source=nativephp&utm_medium=logo&utm_campaign=nativephp" title="BeyondCode sponsor">
    <img src="/img/sponsors/beyondcode.png" class="block dark:hidden {{ $height }} w-auto max-w-full">
    <img src="/img/sponsors/beyondcode-dark.png" class="hidden dark:block {{ $height }} w-auto max-w-full">
</a>

<a href="https://laradir.com/?ref=nativephp" title="Laradir sponsor">
    <x-sponsors.laradir class="block {{ $height }} {{ $sameHeight ? 'py-[5%]':''  }} max-w-full"/>
</a>
