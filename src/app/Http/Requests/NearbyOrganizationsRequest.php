<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NearbyOrganizationsRequest extends FormRequest
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
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'lat.required' => 'Latitude is required.',
            'lat.numeric' => 'Latitude must be a number.',
            'lat.between' => 'Latitude must be between -90 and 90.',
            'lng.required' => 'Longitude is required.',
            'lng.numeric' => 'Longitude must be a number.',
            'lng.between' => 'Longitude must be between -180 and 180.',
            'radius.numeric' => 'Radius must be a number.',
            'radius.min' => 'Radius must be at least 0.1 kilometers.',
            'radius.max' => 'Radius cannot exceed 1000 kilometers.',
        ];
    }
} 