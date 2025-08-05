<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchOrganizationsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'activity' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'activity.string' => 'Activity must be a string.',
            'activity.max' => 'Activity name cannot exceed 255 characters.',
            'name.string' => 'Organization name must be a string.',
            'name.max' => 'Organization name cannot exceed 255 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->has('activity') && !$this->has('name')) {
                $validator->errors()->add('search', 'At least one search parameter (activity or name) is required.');
            }
        });
    }
} 