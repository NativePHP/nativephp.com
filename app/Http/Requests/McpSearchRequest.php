<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class McpSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'max:500'],
            'platform' => ['nullable', 'string', 'in:desktop,mobile'],
            'version' => ['nullable', 'string', 'regex:/^[0-9]+$/'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'platform.in' => 'Platform must be either desktop or mobile.',
            'version.regex' => 'Version must be a numeric value.',
            'limit.max' => 'Limit cannot exceed 100.',
        ];
    }
}
