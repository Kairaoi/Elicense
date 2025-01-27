<?php

namespace App\Http\Requests\License;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLicenseRequest  extends FormRequest
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
            'applicant_id' => ['sometimes', 'required', 'exists:applicants,id'],
            'license_type_id' => ['sometimes', 'required', 'exists:license_types,id'],
            'species' => ['sometimes', 'required', 'array', 'min:1'],
            'species.*.id' => ['required_with:species', 'exists:species,id'],
            'species.*.requested_quota' => [
                'required_with:species.*.id',
                'numeric',
                'min:0',
                'max:999999'
            ],
            'status' => ['sometimes', 'string', 'in:pending,approved,rejected'],
            'remarks' => ['sometimes', 'nullable', 'string', 'max:1000'],
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
            'species.*.id.required_with' => 'Each species must have an ID.',
            'species.*.id.exists' => 'One or more selected species are invalid.',
            'species.*.requested_quota.required_with' => 'Each species must have a requested quota.',
            'species.*.requested_quota.numeric' => 'The requested quota must be a number.',
            'species.*.requested_quota.min' => 'The requested quota cannot be negative.',
            'species.*.requested_quota.max' => 'The requested quota cannot exceed 999,999.',
            'status.in' => 'The status must be either pending, approved, or rejected.',
            'remarks.max' => 'The remarks cannot exceed 1000 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->has('species') && is_array($this->species)) {
            // Remove any empty species entries
            $this->merge([
                'species' => array_filter($this->species, function ($species) {
                    return !empty($species['id']) && isset($species['requested_quota']);
                })
            ]);
        }
    }
}