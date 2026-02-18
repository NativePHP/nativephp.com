<?php

namespace App\Livewire;

use App\Models\Showcase;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ShowcaseSubmissionForm extends Component
{
    use WithFileUploads;

    public ?Showcase $showcase = null;

    public bool $isEditing = false;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string|max:2000')]
    public string $description = '';

    #[Validate('nullable|image|max:2048')]
    public $image;

    public ?string $existingImage = null;

    #[Validate('nullable|array|max:5')]
    public array $screenshots = [];

    public array $existingScreenshots = [];

    #[Validate('boolean')]
    public bool $hasMobile = false;

    #[Validate('boolean')]
    public bool $hasDesktop = false;

    #[Validate('nullable|url|max:255')]
    public ?string $playStoreUrl = '';

    #[Validate('nullable|url|max:255')]
    public ?string $appStoreUrl = '';

    #[Validate('nullable|url|max:255')]
    public ?string $windowsDownloadUrl = '';

    #[Validate('nullable|url|max:255')]
    public ?string $macosDownloadUrl = '';

    #[Validate('nullable|url|max:255')]
    public ?string $linuxDownloadUrl = '';

    #[Validate('accepted')]
    public bool $certifiedNativephp = false;

    public function mount(?Showcase $showcase = null): void
    {
        if ($showcase && $showcase->exists && $showcase->user_id === auth()->id()) {
            $this->showcase = $showcase;
            $this->isEditing = true;
            $this->title = $showcase->title;
            $this->description = $showcase->description;
            $this->existingImage = $showcase->image;
            $this->existingScreenshots = $showcase->screenshots ?? [];
            $this->hasMobile = $showcase->has_mobile;
            $this->hasDesktop = $showcase->has_desktop;
            $this->playStoreUrl = $showcase->play_store_url ?? '';
            $this->appStoreUrl = $showcase->app_store_url ?? '';
            $this->windowsDownloadUrl = $showcase->windows_download_url ?? '';
            $this->macosDownloadUrl = $showcase->macos_download_url ?? '';
            $this->linuxDownloadUrl = $showcase->linux_download_url ?? '';
            $this->certifiedNativephp = $showcase->certified_nativephp;
        }
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'image' => 'nullable|image|max:2048',
            'screenshots.*' => 'nullable|image|max:2048',
            'hasMobile' => 'boolean',
            'hasDesktop' => 'boolean',
            'playStoreUrl' => 'nullable|url|max:255',
            'appStoreUrl' => 'nullable|url|max:255',
            'windowsDownloadUrl' => 'nullable|url|max:255',
            'macosDownloadUrl' => 'nullable|url|max:255',
            'linuxDownloadUrl' => 'nullable|url|max:255',
            'certifiedNativephp' => 'accepted',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'certifiedNativephp.accepted' => 'You must certify that your app is built with NativePHP.',
            'hasMobile.required_without' => 'Please select at least one platform (Mobile or Desktop).',
            'hasDesktop.required_without' => 'Please select at least one platform (Mobile or Desktop).',
        ];
    }

    public function removeExistingScreenshot(int $index): void
    {
        if (isset($this->existingScreenshots[$index])) {
            unset($this->existingScreenshots[$index]);
            $this->existingScreenshots = array_values($this->existingScreenshots);
        }
    }

    public function removeExistingImage(): void
    {
        $this->existingImage = null;
    }

    public function submit(): mixed
    {
        $this->validate();

        if (! $this->hasMobile && ! $this->hasDesktop) {
            $this->addError('hasMobile', 'Please select at least one platform (Mobile or Desktop).');

            return null;
        }

        $imagePath = $this->existingImage;
        if ($this->image) {
            $imagePath = $this->image->store('showcase-images', 'public');
        }

        $screenshotPaths = $this->existingScreenshots;
        foreach ($this->screenshots as $screenshot) {
            if (count($screenshotPaths) >= 5) {
                break;
            }
            $screenshotPaths[] = $screenshot->store('showcase-screenshots', 'public');
        }

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'image' => $imagePath,
            'screenshots' => $screenshotPaths ?: null,
            'has_mobile' => $this->hasMobile,
            'has_desktop' => $this->hasDesktop,
            'play_store_url' => $this->hasMobile ? ($this->playStoreUrl ?: null) : null,
            'app_store_url' => $this->hasMobile ? ($this->appStoreUrl ?: null) : null,
            'windows_download_url' => $this->hasDesktop ? ($this->windowsDownloadUrl ?: null) : null,
            'macos_download_url' => $this->hasDesktop ? ($this->macosDownloadUrl ?: null) : null,
            'linux_download_url' => $this->hasDesktop ? ($this->linuxDownloadUrl ?: null) : null,
            'certified_nativephp' => true,
        ];

        if ($this->isEditing && $this->showcase) {
            $wasApproved = $this->showcase->isApproved();

            $this->showcase->update($data);

            if ($wasApproved) {
                $this->showcase->update([
                    'approved_at' => null,
                    'approved_by' => null,
                ]);

                return to_route('customer.showcase.index')
                    ->with('warning', 'Your submission has been updated and sent back for review.');
            }

            return to_route('customer.showcase.index')
                ->with('success', 'Your submission has been updated.');
        }

        Showcase::create([
            'user_id' => auth()->id(),
            ...$data,
        ]);

        return to_route('customer.showcase.index')
            ->with('success', 'Thank you! Your app has been submitted for review.');
    }

    public function delete(): mixed
    {
        if ($this->showcase && $this->showcase->user_id === auth()->id()) {
            if ($this->showcase->image) {
                Storage::disk('public')->delete($this->showcase->image);
            }

            if ($this->showcase->screenshots) {
                foreach ($this->showcase->screenshots as $screenshot) {
                    Storage::disk('public')->delete($screenshot);
                }
            }

            $this->showcase->delete();

            return to_route('customer.showcase.index')
                ->with('success', 'Your submission has been deleted.');
        }

        return null;
    }

    public function render()
    {
        return view('livewire.showcase-submission-form');
    }
}
