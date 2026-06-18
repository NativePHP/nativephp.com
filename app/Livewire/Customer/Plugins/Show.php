<?php

namespace App\Livewire\Customer\Plugins;

use App\Enums\PluginTier;
use App\Enums\PluginType;
use App\Jobs\ReviewPluginRepository;
use App\Models\Plugin;
use App\Notifications\PluginSubmitted;
use App\Services\GitHubUserService;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.dashboard')]
#[Title('Plugin')]
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

    public ?string $displayName = null;

    public ?string $supportChannel = null;

    public string $notes = '';

    public string $activeTab = 'details';

    public string $pluginType = 'free';

    public ?string $tier = null;

    #[Computed]
    public function hasCompletedDeveloperOnboarding(): bool
    {
        return auth()->user()->developerAccount?->hasCompletedOnboarding() ?? false;
    }

    public function mount(string $vendor, string $package): void
    {
        $this->plugin = Plugin::findByVendorPackageOrFail($vendor, $package);

        if ($this->plugin->user_id !== auth()->id()) {
            abort(403);
        }

        $this->displayName = $this->plugin->display_name;
        $this->description = $this->plugin->description;
        $this->iconName = $this->plugin->icon_name ?? 'cube';
        $this->iconGradient = $this->plugin->icon_gradient;
        $this->iconMode = $this->plugin->hasLogo() ? 'upload' : 'gradient';
        $this->supportChannel = $this->plugin->support_channel;
        $this->notes = $this->plugin->notes ?? '';
        $this->pluginType = $this->plugin->type->value;
        $this->tier = $this->plugin->tier?->value;
    }

    public function runPreflightChecks(): void
    {
        if (! $this->plugin->isDraft()) {
            return;
        }

        $user = auth()->user();
        $repoInfo = $this->plugin->getRepositoryOwnerAndName();

        // Ensure a webhook secret exists so we can always show setup instructions
        if (! $this->plugin->webhook_secret) {
            $this->plugin->generateWebhookSecret();
        }

        // Verify or install webhook
        if ($repoInfo && $user->hasGitHubToken()) {
            $githubService = GitHubUserService::for($user);
            $webhookUrl = $this->plugin->getWebhookUrl();

            // Check if our webhook already exists on the repo
            if ($webhookUrl && $githubService->webhookExists($repoInfo['owner'], $repoInfo['repo'], $webhookUrl)) {
                if (! $this->plugin->webhook_installed) {
                    $this->plugin->update(['webhook_installed' => true]);
                }
            } else {
                // Webhook not found on GitHub — try to create it
                $webhookSecret = $this->plugin->webhook_secret ?? $this->plugin->generateWebhookSecret();
                $webhookResult = $githubService->createWebhook(
                    $repoInfo['owner'],
                    $repoInfo['repo'],
                    $this->plugin->getWebhookUrl(),
                    $webhookSecret
                );
                $this->plugin->update(['webhook_installed' => $webhookResult['success']]);
            }
        }

        // Run review checks
        (new ReviewPluginRepository($this->plugin))->handle();

        $this->plugin->refresh();
    }

    public function submitForReview(): void
    {
        if (! $this->plugin->isDraft()) {
            Flux::toast(variant: 'danger', text: 'Only draft plugins can be submitted for review.');

            return;
        }

        if (! $this->plugin->description) {
            Flux::toast(variant: 'danger', text: 'Please add a description before submitting for review.');

            return;
        }

        if (! $this->plugin->support_channel) {
            Flux::toast(variant: 'danger', text: 'Please set a support channel before submitting for review.');

            return;
        }

        if ($this->plugin->isPaid() && ! $this->plugin->tier) {
            Flux::toast(variant: 'danger', text: 'Please select a pricing tier for your paid plugin.');

            return;
        }

        // Run preflight checks
        $this->runPreflightChecks();

        // Only submit if required checks pass
        if (! $this->plugin->passesRequiredReviewChecks()) {
            Flux::toast(variant: 'danger', text: 'Your plugin doesn\'t pass all required checks yet. Please resolve the failing checks and try again.');

            return;
        }

        $user = auth()->user();

        // Submit
        $this->plugin->submit();
        $this->plugin->refresh();

        // Notify
        $user->notify(new PluginSubmitted($this->plugin));

        Flux::toast(variant: 'success', text: 'Your plugin has been submitted for review!');
    }

    public function certifyWebhook(): void
    {
        if ($this->plugin->webhook_installed) {
            return;
        }

        if (! $this->plugin->webhook_secret) {
            $this->plugin->generateWebhookSecret();
        }

        $this->plugin->update(['webhook_installed' => true]);
        $this->plugin->refresh();

        $this->modal('certify-webhook')->close();

        Flux::toast(variant: 'success', text: 'Webhook marked as installed.');
    }

    public function retryWebhook(): void
    {
        $user = auth()->user();
        $repoInfo = $this->plugin->getRepositoryOwnerAndName();

        if (! $repoInfo || ! $user->hasGitHubToken()) {
            Flux::toast(variant: 'danger', text: 'Unable to register webhook automatically. Please ensure your GitHub account is connected and the repository URL is valid.');

            return;
        }

        $webhookSecret = $this->plugin->webhook_secret ?? $this->plugin->generateWebhookSecret();
        $githubService = GitHubUserService::for($user);
        $webhookResult = $githubService->createWebhook(
            $repoInfo['owner'],
            $repoInfo['repo'],
            $this->plugin->getWebhookUrl(),
            $webhookSecret
        );

        $this->plugin->update(['webhook_installed' => $webhookResult['success']]);
        $this->plugin->refresh();

        if ($webhookResult['success']) {
            Flux::toast(variant: 'success', text: 'Webhook installed successfully.');
        } else {
            Flux::toast(variant: 'danger', text: 'Failed to install webhook: '.($webhookResult['error'] ?? 'Unknown error'));
        }
    }

    public function withdrawFromReview(): void
    {
        if (! $this->plugin->isPending()) {
            Flux::toast(variant: 'danger', text: 'Only pending plugins can be withdrawn.');

            return;
        }

        $this->plugin->withdraw();
        $this->plugin->refresh();

        Flux::toast(variant: 'success', text: 'Your plugin has been withdrawn from review and returned to draft.');
    }

    public function returnToDraft(): void
    {
        if (! $this->plugin->isRejected()) {
            Flux::toast(variant: 'danger', text: 'Only rejected plugins can be returned to draft.');

            return;
        }

        $this->plugin->returnToDraft();
        $this->plugin->refresh();

        Flux::toast(variant: 'success', text: 'Your plugin has been returned to draft. You can make changes and resubmit.');
    }

    public function save(): void
    {
        if (! $this->plugin->isDraft() && ! $this->plugin->isApproved()) {
            Flux::toast(variant: 'danger', text: 'You can only edit draft or approved plugins.');

            return;
        }

        $rules = [
            'displayName' => ['nullable', 'string', 'max:250'],
            'description' => ['required', 'string', 'max:1000'],
            'supportChannel' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($value && ! filter_var($value, FILTER_VALIDATE_EMAIL) && ! filter_var($value, FILTER_VALIDATE_URL)) {
                        $fail('The support channel must be a valid email address or URL.');
                    }
                },
            ],
        ];

        if ($this->plugin->isDraft()) {
            $rules['notes'] = ['nullable', 'string', 'max:5000'];
            $rules['pluginType'] = ['required', 'string', 'in:free,paid'];

            if ($this->pluginType === 'paid') {
                $rules['tier'] = ['required', 'string', 'in:bronze,silver,gold'];
            }
        }

        $this->validate($rules, [
            'tier.required' => 'Please select a pricing tier for your paid plugin.',
        ]);

        if ($this->plugin->isDraft() && $this->pluginType === 'paid' && ! $this->hasCompletedDeveloperOnboarding) {
            Flux::toast(variant: 'danger', text: 'You must complete developer onboarding before setting a plugin as paid.');

            return;
        }

        $data = [
            'display_name' => $this->displayName ?: null,
            'support_channel' => $this->supportChannel,
        ];

        if ($this->plugin->isDraft()) {
            $data['notes'] = $this->notes ?: null;

            $pluginType = PluginType::from($this->pluginType);
            $data['type'] = $pluginType;
            $data['tier'] = $pluginType === PluginType::Paid && $this->tier ? PluginTier::from($this->tier) : null;
        }

        $this->plugin->update($data);
        $this->plugin->updateDescription($this->description, auth()->id());
        $this->plugin->refresh();

        Flux::toast(variant: 'success', text: 'Plugin details saved successfully!');
    }

    public function updateIcon(): void
    {
        if (! $this->plugin->isDraft() && ! $this->plugin->isApproved()) {
            Flux::toast(variant: 'danger', text: 'You can only edit the icon for draft or approved plugins.');

            return;
        }

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

        Flux::toast(variant: 'success', text: 'Plugin icon updated successfully!');
    }

    public function uploadLogo(): void
    {
        if (! $this->plugin->isDraft() && ! $this->plugin->isApproved()) {
            Flux::toast(variant: 'danger', text: 'You can only upload a logo for draft or approved plugins.');

            return;
        }

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

        Flux::toast(variant: 'success', text: 'Plugin logo updated successfully!');
    }

    public function deleteIcon(): void
    {
        if (! $this->plugin->isDraft() && ! $this->plugin->isApproved()) {
            Flux::toast(variant: 'danger', text: 'You can only remove the icon for draft or approved plugins.');

            return;
        }

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

        Flux::toast(variant: 'success', text: 'Plugin icon removed successfully!');
    }

    public function toggleListing(): void
    {
        if (! $this->plugin->isApproved()) {
            Flux::toast(variant: 'danger', text: 'Only approved plugins can be listed or de-listed.');

            return;
        }

        $this->plugin->update([
            'is_active' => ! $this->plugin->is_active,
        ]);

        $this->plugin->refresh();

        $action = $this->plugin->is_active ? 'listed' : 'de-listed';
        Flux::toast(variant: 'success', text: "Your plugin has been {$action}.");
    }

    public function validate($rules = [], $messages = [], $attributes = []): array
    {
        try {
            return parent::validate($rules, $messages, $attributes);
        } catch (ValidationException $e) {
            $this->dispatch('scroll-to-first-error');

            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.customer.plugins.show');
    }
}
