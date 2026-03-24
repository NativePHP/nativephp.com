<?php

namespace App\Livewire\Customer\Support;

use App\Models\Plugin;
use App\Models\SupportTicket;
use App\Notifications\SupportTicketSubmitted;
use App\SupportTicket\Status;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Submit a Request')]
class Create extends Component
{
    #[Locked]
    public int $currentStep = 1;

    /** Step 1: Product selection */
    public string $selectedProduct = '';

    /** Step 2: Context questions */
    public string $mobileAreaType = '';

    public string $mobileArea = '';

    public string $issueType = '';

    public string $tryingToDo = '';

    public string $whatHappened = '';

    public string $reproductionSteps = '';

    public string $environment = '';

    /** Step 3: Subject + Message */
    public string $subject = '';

    public string $message = '';

    public function updatedSelectedProduct(): void
    {
        $this->mobileAreaType = '';
        $this->mobileArea = '';
        $this->issueType = '';
        $this->tryingToDo = '';
        $this->whatHappened = '';
        $this->reproductionSteps = '';
        $this->environment = '';
        $this->subject = '';
        $this->message = '';
        $this->resetValidation();
    }

    public function updatedMobileAreaType(): void
    {
        $this->mobileArea = '';
    }

    public function getShowMobileAreaProperty(): bool
    {
        return $this->selectedProduct === 'mobile';
    }

    public function getShowBugReportFieldsProperty(): bool
    {
        return in_array($this->selectedProduct, ['mobile', 'desktop']);
    }

    public function getShowIssueTypeProperty(): bool
    {
        return in_array($this->selectedProduct, ['bifrost', 'nativephp.com']);
    }

    public function nextStep(): void
    {
        if ($this->currentStep === 1) {
            $this->validateStep1();
        }

        if ($this->currentStep === 2) {
            $this->validateStep2();
        }

        $this->currentStep++;
    }

    public function previousStep(): void
    {
        $this->currentStep--;
    }

    public function submit(): void
    {
        $this->validateStep1();
        $this->validateStep2();

        $metadata = null;

        if ($this->showMobileArea || $this->showBugReportFields) {
            $metadata = array_filter([
                'mobile_area_type' => $this->showMobileArea ? $this->mobileAreaType : null,
                'mobile_area' => $this->showMobileArea && $this->mobileAreaType === 'plugin' ? $this->mobileArea : null,
                'trying_to_do' => $this->showBugReportFields ? $this->tryingToDo : null,
                'what_happened' => $this->showBugReportFields ? $this->whatHappened : null,
                'reproduction_steps' => $this->showBugReportFields ? $this->reproductionSteps : null,
                'environment' => $this->showBugReportFields ? $this->environment : null,
            ]);
        }

        $subject = $this->subject;
        $message = $this->message;

        if ($this->showBugReportFields) {
            $subject = Str::limit($this->tryingToDo, 252);
            $message = "**What I was trying to do:**\n{$this->tryingToDo}\n\n"
                ."**What happened instead:**\n{$this->whatHappened}\n\n"
                ."**Steps to reproduce:**\n{$this->reproductionSteps}\n\n"
                ."**Environment:**\n{$this->environment}";
        }

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $subject,
            'message' => $message,
            'status' => Status::OPEN,
            'product' => $this->selectedProduct,
            'issue_type' => $this->showIssueType ? $this->issueType : null,
            'metadata' => $metadata ?: null,
        ]);

        Notification::route('mail', 'support@nativephp.com')
            ->notify(new SupportTicketSubmitted($ticket));

        $this->redirect(route('customer.support.tickets.show', $ticket), navigate: false);
    }

    protected function validateStep1(): void
    {
        $this->validate([
            'selectedProduct' => ['required', 'string', 'in:mobile,desktop,bifrost,nativephp.com'],
        ], [
            'selectedProduct.required' => 'Please select a product.',
            'selectedProduct.in' => 'Please select a valid product.',
        ]);
    }

    protected function validateStep2(): void
    {
        $rules = [];
        $messages = [];

        if (! $this->showBugReportFields) {
            $rules['subject'] = ['required', 'string', 'max:255'];
            $rules['message'] = ['required', 'string', 'max:5000'];

            $messages['subject.required'] = 'Please enter a subject for your ticket.';
            $messages['subject.max'] = 'The subject must not exceed 255 characters.';
            $messages['message.required'] = 'Please enter a message for your ticket.';
            $messages['message.max'] = 'The message must not exceed 5000 characters.';
        }

        if ($this->showMobileArea) {
            $rules['mobileAreaType'] = ['required', 'string', 'in:core,plugin'];

            if ($this->mobileAreaType === 'plugin') {
                $rules['mobileArea'] = ['required', 'string', 'max:255'];
            }

            $messages['mobileAreaType.required'] = 'Please select what the issue is related to.';
            $messages['mobileArea.required'] = 'Please select a plugin or tool.';
        }

        if ($this->showBugReportFields) {
            $rules['tryingToDo'] = ['required', 'string', 'max:5000'];
            $rules['whatHappened'] = ['required', 'string', 'max:5000'];
            $rules['reproductionSteps'] = ['required', 'string', 'max:5000'];
            $rules['environment'] = ['required', 'string', 'max:1000'];

            $messages['tryingToDo.required'] = 'Please describe what you were trying to do.';
            $messages['whatHappened.required'] = 'Please describe what happened instead.';
            $messages['reproductionSteps.required'] = 'Please provide steps to reproduce the issue.';
            $messages['environment.required'] = 'Please describe your environment.';
        }

        if ($this->showIssueType) {
            $rules['issueType'] = ['required', 'string', 'in:account_query,bug,feature_request,other'];

            $messages['issueType.required'] = 'Please select an issue type.';
        }

        $this->validate($rules, $messages);
    }

    public function render()
    {
        $officialPlugins = collect();

        if ($this->selectedProduct === 'mobile') {
            $officialPlugins = Plugin::where('is_official', true)
                ->orderBy('name')
                ->pluck('name', 'id');
        }

        return view('livewire.customer.support.create', [
            'officialPlugins' => $officialPlugins,
        ]);
    }
}
