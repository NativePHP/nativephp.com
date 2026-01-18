<?php

namespace App\Http\Requests;

use App\Enums\PluginType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitPluginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'repository_url' => [
                'required',
                'url',
                'max:255',
                'regex:/^https:\/\/github\.com\/[a-zA-Z0-9_.-]+\/[a-zA-Z0-9_.-]+$/',
                'unique:plugins,repository_url',
            ],
            'type' => ['required', 'string', Rule::enum(PluginType::class)],
            'price' => [
                'nullable',
                'required_if:type,paid',
                'integer',
                'min:10',
                'max:99999',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'repository_url.required' => 'Please select or enter your plugin\'s GitHub repository.',
            'repository_url.url' => 'Please enter a valid URL.',
            'repository_url.regex' => 'Please enter a valid GitHub repository URL (e.g., https://github.com/vendor/repo).',
            'repository_url.unique' => 'This repository has already been submitted.',
            'type.required' => 'Please select whether your plugin is free or paid.',
            'type.enum' => 'Please select a valid plugin type.',
            'price.required_if' => 'Please enter a price for your paid plugin.',
            'price.integer' => 'The price must be a whole dollar amount (no cents).',
            'price.min' => 'The price must be at least $10.',
            'price.max' => 'The price cannot exceed $99,999.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->type === 'paid' && ! $this->user()->github_id) {
                $validator->errors()->add('type', 'You must connect your GitHub account to submit a paid plugin.');
            }
        });
    }
}
