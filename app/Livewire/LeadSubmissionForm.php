<?php

namespace App\Livewire;

use App\Models\Lead;
use App\Notifications\LeadReceived;
use App\Notifications\NewLeadSubmitted;
use App\Rules\Turnstile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class LeadSubmissionForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $company = '';

    public string $description = '';

    public string $budget = '';

    public string $turnstileToken = '';

    public bool $submitted = false;

    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'budget' => ['required', 'string', 'in:'.implode(',', array_keys(Lead::BUDGETS))],
        ];

        if (config('services.turnstile.secret_key')) {
            $rules['turnstileToken'] = ['required', new Turnstile];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'budget.in' => 'Please select a budget range.',
            'turnstileToken.required' => 'Please complete the security check.',
        ];
    }

    public function submit(): void
    {
        $key = 'leads:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('form', "Too many submissions. Please try again in {$seconds} seconds.");

            return;
        }

        $this->validate();

        RateLimiter::hit($key, 60);

        $lead = Lead::create([
            'name' => $this->name,
            'email' => $this->email,
            'company' => $this->company,
            'description' => $this->description,
            'budget' => $this->budget,
            'ip_address' => request()->ip(),
        ]);

        $lead->notify(new LeadReceived);

        Notification::route('mail', 'support@nativephp.com')
            ->notify(new NewLeadSubmitted($lead));

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.lead-submission-form', [
            'budgets' => Lead::BUDGETS,
        ]);
    }
}
