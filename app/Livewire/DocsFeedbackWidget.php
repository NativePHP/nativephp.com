<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\DocsFeedback;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Component;

final class DocsFeedbackWidget extends Component
{
    #[Locked]
    public string $platform;

    #[Locked]
    public string $version;

    #[Locked]
    public string $page;

    public ?bool $helpful = null;

    public string $comment = '';

    public bool $submitted = false;

    public function mount(string $platform, string $version, string $page): void
    {
        $this->platform = $platform;
        $this->version = $version;
        $this->page = $page;
    }

    public function vote(bool $helpful): void
    {
        $this->helpful = $helpful;
    }

    public function submit(): void
    {
        $key = 'docs-feedback:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('form', "You've submitted a lot of feedback recently. Please try again in {$seconds} seconds.");

            return;
        }

        $this->validate([
            'helpful' => 'required|boolean',
            'comment' => 'nullable|string|max:1000',
        ]);

        RateLimiter::hit($key, 60);

        DocsFeedback::create([
            'platform' => $this->platform,
            'version' => $this->version,
            'page' => $this->page,
            'helpful' => $this->helpful,
            'comment' => $this->comment !== '' ? $this->comment : null,
            'ip_hash' => hash('sha256', (string) request()->ip()),
        ]);

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.docs-feedback-widget');
    }
}
