<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PluginReportCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StorePluginReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['required', Rule::enum(PluginReportCategory::class)],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'Please select a reason for this report.',
            'message.required' => 'Please describe the issue.',
            'message.max' => 'The message cannot be longer than 5000 characters.',
        ];
    }
}
