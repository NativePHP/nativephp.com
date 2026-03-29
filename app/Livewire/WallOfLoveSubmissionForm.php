<?php

namespace App\Livewire;

use App\Models\WallOfLoveSubmission;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class WallOfLoveSubmissionForm extends Component
{
    use WithFileUploads;

    public ?WallOfLoveSubmission $submission = null;

    public bool $isEditing = false;

    public $name = '';

    public $company = '';

    public $photo;

    public ?string $existingPhoto = null;

    public $url = '';

    public $testimonial = '';

    public function mount(?WallOfLoveSubmission $submission = null): void
    {
        if ($submission && $submission->exists && $submission->user_id === auth()->id()) {
            $this->submission = $submission;
            $this->isEditing = true;
            $this->name = $submission->name;
            $this->company = $submission->company ?? '';
            $this->existingPhoto = $submission->photo_path;
            $this->url = $submission->url ?? '';
            $this->testimonial = $submission->testimonial ?? '';
        } else {
            // Pre-fill name if user has a name
            $this->name = auth()->user()->name ?? '';
        }
    }

    public function rules(): array
    {
        if ($this->isEditing) {
            return [
                'company' => 'nullable|string|max:255',
                'photo' => 'nullable|image|max:2048',
            ];
        }

        return [
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'url' => 'nullable|url|max:255',
            'testimonial' => 'nullable|string|max:1000',
        ];
    }

    public function submit(): mixed
    {
        $this->validate();

        if ($this->isEditing && $this->submission) {
            $data = [
                'company' => $this->company ?: null,
            ];

            if ($this->photo) {
                $data['photo_path'] = $this->photo->store('wall-of-love-photos', 'public');
            } elseif ($this->existingPhoto === null) {
                $data['photo_path'] = null;
            }

            $this->submission->update($data);

            if ($this->photo) {
                $this->existingPhoto = $data['photo_path'];
                $this->reset('photo');
            }

            session()->flash('success', 'Your listing has been updated.');

            return null;
        }

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

    public function removeExistingPhoto(): void
    {
        $this->existingPhoto = null;
    }

    public function render()
    {
        return view('livewire.wall-of-love-submission-form');
    }
}
