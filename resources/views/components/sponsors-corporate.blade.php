@props(['height' => 'h-12'])

<a href="https://www.redgalaxy.co.uk/" title="RedGalaxy sponsor">
    <x-sponsors.redgalaxy class="block {{ $height }} max-w-full"/>
</a>

<a href="https://sevalla.com/?utm_source=nativephp&utm_medium=Referral&utm_campaign=homepage" title="Sevalla sponsor">
    <x-sponsors.sevalla class="{{ $height }} text-black dark:text-white max-w-full"/>
</a>

{{--
<a href="https://serverauth.com/" class="block {{ $height }}" title="ServerAuth sponsor">
    <x-sponsors.serverauth class="block {{ $height }} fill-[#042340] dark:fill-white max-w-full"/>
</a>
--}}

<a href="https://www.kaashosting.nl/?lang=en" class="block {{ $height }}" title="KaasHosting sponsor">
    <x-sponsors.kaashosting class="block {{ $height }} fill-[#042340] dark:fill-white max-w-full"/>
</a>
