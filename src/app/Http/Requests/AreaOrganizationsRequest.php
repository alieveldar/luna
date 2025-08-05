<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AreaOrganizationsRequest extends FormRequest
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
            'lat1' => 'required|numeric|between:-90,90',
            'lng1' => 'required|numeric|between:-180,180',
            'lat2' => 'required|numeric|between:-90,90',
            'lng2' => 'required|numeric|between:-180,180',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'lat1.required' => 'First latitude is required.',
            'lat1.numeric' => 'First latitude must be a number.',
            'lat1.between' => 'First latitude must be between -90 and 90.',
            'lng1.required' => 'First longitude is required.',
            'lng1.numeric' => 'First longitude must be a number.',
            'lng1.between' => 'First longitude must be between -180 and 180.',
            'lat2.required' => 'Second latitude is required.',
            'lat2.numeric' => 'Second latitude must be a number.',
            'lat2.between' => 'Second latitude must be between -90 and 90.',
            'lng2.required' => 'Second longitude is required.',
            'lng2.numeric' => 'Second longitude must be a number.',
            'lng2.between' => 'Second longitude must be between -180 and 180.',
        ];
    }
} 