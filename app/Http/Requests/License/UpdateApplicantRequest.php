<?php

namespace App\Http\Requests\License;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicantRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $applicantId = $this->route('applicant');

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('applicants')->ignore($applicantId)->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ],
            'local_registration_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('applicants')->ignore($applicantId)->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ],
            'types_of_company' => 'required|in:Corporation,Partnership,Single Private Company',
            'date_of_establishment' => 'required|date|before_or_equal:today',
            'citizenship' => 'required|string|max:255',
            'work_address' => 'required|string|max:255',
            'registered_address' => 'required|string|max:255',
            'foreign_investment_license' => 'nullable|string|max:255',
            'phone_number' => [
                'required',
                'string',
                'max:20',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                Rule::unique('applicants')->ignore($applicantId)->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('applicants')->ignore($applicantId)->where(function ($query) {
                    return $query->whereNull('deleted_at')
                        ->where(function ($q) {
                            $q->where('first_name', $this->first_name)
                              ->where('last_name', $this->last_name);
                        });
                })
            ],
        ];
    }

    public function messages()
    {
        return [
            'phone_number.regex' => 'Please enter a valid phone number',
            'phone_number.unique' => 'This phone number is already registered in our system',
            'types_of_company.in' => 'Please select a valid company type',
            'date_of_establishment.date' => 'Please enter a valid date',
            'date_of_establishment.before_or_equal' => 'Date of establishment cannot be in the future',
            'email.unique' => 'An application with this email and name combination already exists',
            'company_name.unique' => 'This company name is already registered in our system',
            'local_registration_number.unique' => 'This registration number is already registered in our system'
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('phone_number')) {
            // Clean phone number - remove any extra spaces
            $this->merge([
                'phone_number' => preg_replace('/\s+/', ' ', trim($this->phone_number))
            ]);
        }

        if ($this->has(['first_name', 'last_name'])) {
            // Clean and capitalize names
            $this->merge([
                'first_name' => ucwords(strtolower(trim($this->first_name))),
                'last_name' => ucwords(strtolower(trim($this->last_name)))
            ]);
        }
    }
}