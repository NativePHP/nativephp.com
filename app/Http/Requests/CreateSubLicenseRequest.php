<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'assigned_email' => ['nullable', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'The name cannot be longer than 255 characters.',
            'assigned_email.email' => 'Please enter a valid email address.',
            'assigned_email.max' => 'The email address cannot be longer than 255 characters.',
        ];
    }
}
