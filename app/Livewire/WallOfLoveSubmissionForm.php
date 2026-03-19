<?php

namespace App\Livewire;

use App\Models\WallOfLoveSubmission;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class WallOfLoveSubmissionForm extends Component
{
    use WithFileUploads;

    public $name = '';

    public $company = '';

    public $photo;

    public $url = '';

    public $testimonial = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'company' => 'nullable|string|max:255',
        'photo' => 'nullable|image|max:2048', // 2MB max
        'url' => 'nullable|url|max:255',
        'testimonial' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        // Pre-fill name if user has a name
        $this->name = auth()->user()->name ?? '';
    }

    public function submit()
    {
        $this->validate();

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('wall-of-love-photos', 'public');
        }

        WallOfLoveSubmission::create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'company' => $this->company ?: null,
            'photo_path' => $photoPath,
            'url' => $this->url ?: null,
            'testimonial' => $this->testimonial ?: null,
        ]);

        return to_route('dashboard')->with('success', 'Thank you! Your submission has been received and is awaiting review.');
    }

    public function render()
    {
        return view('livewire.wall-of-love-submission-form');
    }
}
