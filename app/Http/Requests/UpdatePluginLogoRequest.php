<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePluginLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logo' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg,svg,webp',
                'max:1024',
                'dimensions:min_width=100,min_height=100,max_width=1024,max_height=1024',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'Please select a logo image to upload.',
            'logo.image' => 'The file must be an image.',
            'logo.mimes' => 'The logo must be a PNG, JPG, JPEG, SVG, or WebP file.',
            'logo.max' => 'The logo must be less than 1MB.',
            'logo.dimensions' => 'The logo must be between 100x100 and 1024x1024 pixels.',
        ];
    }
}
