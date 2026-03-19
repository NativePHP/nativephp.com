<?php

namespace App\Livewire\Customer\Plugins;

use App\Models\Plugin;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.dashboard')]
#[Title('Edit Plugin')]
class Show extends Component
{
    use WithFileUploads;

    public Plugin $plugin;

    public string $iconMode = 'gradient';

    #[Validate('nullable|string|max:1000')]
    public ?string $description = null;

    #[Validate('nullable|string|max:100|regex:/^[a-z0-9-]+$/')]
    public ?string $iconName = null;

    public ?string $iconGradient = null;

    #[Validate('nullable|image|max:1024')]
    public $logo = null;

    public function mount(string $vendor, string $package): void
    {
        $this->plugin = Plugin::findByVendorPackageOrFail($vendor, $package);

        if ($this->plugin->user_id !== auth()->id()) {
            abort(403);
        }

        $this->description = $this->plugin->description;
        $this->iconName = $this->plugin->icon_name ?? 'cube';
        $this->iconGradient = $this->plugin->icon_gradient;
        $this->iconMode = $this->plugin->hasLogo() ? 'upload' : 'gradient';
    }

    public function updateDescription(): void
    {
        $this->validate([
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->plugin->updateDescription($this->description, auth()->id());
        $this->plugin->refresh();

        session()->flash('success', 'Plugin description updated successfully!');
    }

    public function updateIcon(): void
    {
        $this->validate([
            'iconGradient' => ['required', 'string', 'in:'.implode(',', array_keys(Plugin::gradientPresets()))],
            'iconName' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/'],
        ]);

        if ($this->plugin->logo_path) {
            Storage::disk('public')->delete($this->plugin->logo_path);
        }

        $this->plugin->update([
            'logo_path' => null,
            'icon_gradient' => $this->iconGradient,
            'icon_name' => $this->iconName,
        ]);

        $this->plugin->refresh();

        session()->flash('success', 'Plugin icon updated successfully!');
    }

    public function uploadLogo(): void
    {
        $this->validate([
            'logo' => ['required', 'image', 'max:1024', 'mimes:png,jpeg,jpg,svg,webp'],
        ]);

        if ($this->plugin->logo_path) {
            Storage::disk('public')->delete($this->plugin->logo_path);
        }

        $path = $this->logo->store('plugin-logos', 'public');

        $this->plugin->update([
            'logo_path' => $path,
            'icon_gradient' => null,
            'icon_name' => null,
        ]);

        $this->plugin->refresh();
        $this->logo = null;
        $this->iconMode = 'upload';

        session()->flash('success', 'Plugin logo updated successfully!');
    }

    public function deleteIcon(): void
    {
        if ($this->plugin->logo_path) {
            Storage::disk('public')->delete($this->plugin->logo_path);
        }

        $this->plugin->update([
            'logo_path' => null,
            'icon_gradient' => null,
            'icon_name' => null,
        ]);

        $this->plugin->refresh();
        $this->iconMode = 'gradient';

        session()->flash('success', 'Plugin icon removed successfully!');
    }

    public function resubmit(): void
    {
        if (! $this->plugin->isRejected()) {
            session()->flash('error', 'Only rejected plugins can be resubmitted.');

            return;
        }

        $this->plugin->resubmit();
        $this->plugin->refresh();

        session()->flash('success', 'Your plugin has been resubmitted for review!');
    }

    public function render()
    {
        return view('livewire.customer.plugins.show');
    }
}
