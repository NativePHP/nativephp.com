<div class="flex items-center gap-1.5">
    @if($getRecord()->has_mobile)
        <x-heroicon-o-device-phone-mobile class="h-5 w-5 text-success-500" />
    @endif
    @if($getRecord()->has_desktop)
        <x-heroicon-o-computer-desktop class="h-5 w-5 text-success-500" />
    @endif
</div>
