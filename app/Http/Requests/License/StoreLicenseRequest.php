<?php

namespace App\Http\Requests\License;

use Illuminate\Foundation\Http\FormRequest;

class StoreLicenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // You can add more complex authorization logic here if needed
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'applicant_id' => ['required', 'exists:applicants,id'],
            'license_type_id' => ['required', 'exists:license_types,id'],
            'species' => ['required', 'array', 'min:1'],
            'species.*.id' => ['required', 'exists:species,id'],
            'species.*.requested_quota' => ['required', 'numeric', 'min:0', 'max:999999'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'applicant_id.required' => 'An applicant must be selected.',
            'applicant_id.exists' => 'The selected applicant is invalid.',
            'license_type_id.required' => 'A license type must be selected.',
            'license_type_id.exists' => 'The selected license type is invalid.',
            'species.required' => 'At least one species must be selected.',
            'species.array' => 'The species must be provided in a list.',
            'species.min' => 'At least one species must be selected.',
            'species.*.id.required' => 'Each species must have an ID.',
            'species.*.id.exists' => 'One or more selected species are invalid.',
            'species.*.requested_quota.required' => 'Each species must have a requested quota.',
            'species.*.requested_quota.numeric' => 'The requested quota must be a number.',
            'species.*.requested_quota.min' => 'The requested quota cannot be negative.',
            'species.*.requested_quota.max' => 'The requested quota cannot exceed 999,999.',
        ];
    }
}