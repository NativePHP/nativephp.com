<?php

namespace App\Livewire;

use Livewire\Component;

class WallOfLoveBanner extends Component
{
    public function dismissBanner(): void
    {
        cache()->put('wall_of_love_dismissed_'.auth()->id(), true, now()->addWeek());
        $this->dispatch('banner-dismissed');
    }

    public function shouldShowBanner(): bool
    {
        // Check if user has early adopter licenses (before June 1st, 2025)
        $hasEarlyAdopterLicenses = auth()->user()->licenses()->where('created_at', '<', '2025-06-01')->exists();

        // Check if user already submitted
        $hasExistingSubmission = auth()->user()->wallOfLoveSubmissions()->exists();

        // Check if banner was dismissed
        $hasDismissedBanner = cache()->has('wall_of_love_dismissed_'.auth()->id());

        return $hasEarlyAdopterLicenses && ! $hasExistingSubmission && ! $hasDismissedBanner;
    }

    public function render()
    {
        return view('livewire.wall-of-love-banner');
    }
}
