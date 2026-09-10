<?php

namespace App\Http\Requests;

use App\Services\PluginSearchService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class McpPluginSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'max:500'],
            'type' => ['nullable', 'string', Rule::in(['free', 'paid'])],
            'limit' => ['nullable', 'integer', 'min:1', 'max:'.PluginSearchService::MAX_LIMIT],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'plugin_license_key' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'Type must be either free or paid.',
            'limit.max' => 'Limit cannot exceed '.PluginSearchService::MAX_LIMIT.'.',
        ];
    }
}
