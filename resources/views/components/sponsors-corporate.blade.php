@props(['height' => 'h-12'])

<a href="https://www.redgalaxy.co.uk/" title="RedGalaxy sponsor">
    <img src="/img/sponsors/redgalaxy.svg" class="block {{ $height }} max-w-full" alt="">
</a>

<a href="https://sevalla.com/?utm_source=nativephp&utm_medium=Referral&utm_campaign=homepage" title="Sevalla sponsor">
    <img src="/img/sponsors/sevalla.png" class="block {{ $height }} dark:hidden max-w-full" alt="">
    <img src="/img/sponsors/sevalla-dark.png" class="{{ $height }} hidden dark:block max-w-full" alt="">
</a>


<a href="https://serverauth.com/" class="block {{ $height }}" title="ServerAuth sponsor">
    <x-sponsors.serverauth class="block {{ $height }} fill-[#042340] dark:fill-white max-w-full"/>
</a>

<a href="https://www.kaashosting.nl/?lang=en" class="block {{ $height }}" title="KaasHosting sponsor">
    <x-sponsors.kaashosting class="block {{ $height }} fill-[#042340] dark:fill-white max-w-full"/>
</a>
