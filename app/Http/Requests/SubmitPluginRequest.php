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
            'repository' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_.-]+\/[a-zA-Z0-9_.-]+$/',
                function ($attribute, $value, $fail): void {
                    $url = 'https://github.com/'.trim($value, '/');
                    if (\App\Models\Plugin::where('repository_url', $url)->exists()) {
                        $fail('This repository has already been submitted.');
                    }
                },
            ],
            'type' => ['required', 'string', Rule::enum(PluginType::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'repository.required' => 'Please enter your plugin\'s GitHub repository.',
            'repository.regex' => 'Please enter a valid repository in the format vendor/repo-name.',
            'type.required' => 'Please select whether your plugin is free or paid.',
            'type.enum' => 'Please select a valid plugin type.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (! $this->user()->github_id) {
                $validator->errors()->add('repository', 'You must connect your GitHub account to submit a plugin.');
            }
        });
    }
}
