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
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]([_.-]?[a-z0-9]+)*\/[a-z0-9]([_.-]?[a-z0-9]+)*$/i',
                'unique:plugins,name',
            ],
            'type' => ['required', 'string', Rule::enum(PluginType::class)],
            'anystack_id' => [
                'nullable',
                'required_if:type,paid',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your plugin\'s Composer package name.',
            'name.regex' => 'Please enter a valid Composer package name (e.g., vendor/package-name).',
            'name.unique' => 'This plugin has already been submitted.',
            'type.required' => 'Please select whether your plugin is free or paid.',
            'type.enum' => 'Please select a valid plugin type.',
            'anystack_id.required_if' => 'Please enter your Anystack Product ID for paid plugins.',
        ];
    }
}
