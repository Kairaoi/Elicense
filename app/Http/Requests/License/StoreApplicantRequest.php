<?php

namespace App\Http\Requests\License;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApplicantRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('applicants')
                    ->where(function ($query) {
                        return $query->where('last_name', $this->last_name)
                                    ->whereNull('deleted_at');
                    })
            ],
            'company_name' => [
                'required',
                'string',
                'max:255'],
            'last_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('applicants')
                    ->where(function ($query) {
                        return $query->where('first_name', $this->first_name)
                                    ->whereNull('deleted_at');
                    })
            ],
            'local_registration_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('applicants')->where(function ($query) {
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
                Rule::unique('applicants')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('applicants')->where(function ($query) {
                    return $query->whereNull('deleted_at')
                        ->where(function ($q) {
                            $q->where('first_name', $this->first_name)
                              ->where('last_name', $this->last_name);
                        });
                })
            ],
            'captcha_answer' => 'required'
        ];
    }

    public function messages()
    {
        return [
            // Name related errors
            'first_name.required' => 'First name is required',
            'first_name.max' => 'First name cannot exceed 255 characters',
            'last_name.required' => 'Last name is required',
            'last_name.max' => 'Last name cannot exceed 255 characters',
    
            // Company details errors
            'company_name.required' => 'Company name is required',
            'company_name.unique' => 'This company name is already registered. Please use a different name',
            'local_registration_number.required' => 'Registration number is required',
            'local_registration_number.unique' => 'This registration number is already registered. Please check and try again',
            'types_of_company.required' => 'Please select a company type',
            'types_of_company.in' => 'Please select one of: Corporation, Partnership or Single Private Company',
            
            // Date related errors
            'date_of_establishment.required' => 'Date of establishment is required',
            'date_of_establishment.before_or_equal' => 'Date of establishment cannot be in the future',
            
            // Contact information errors
            'phone_number.required' => 'Phone number is required',
            'phone_number.regex' => 'Please enter a valid phone number format',
            'phone_number.unique' => 'This phone number is already registered',
            'phone_number.max' => 'Phone number cannot exceed 20 characters',
            
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'An application with this email and name combination already exists',
            
            // Address errors
            'work_address.required' => 'Work address is required',
            'registered_address.required' => 'Registered address is required',
            
            // Citizenship error
            'citizenship.required' => 'Citizenship information is required',
            
            // Foreign investment license (optional)
            'foreign_investment_license.max' => 'Foreign investment license cannot exceed 255 characters'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate captcha
            if ($this->captcha_answer != session('captcha_result')) {
                $validator->errors()->add('captcha_answer', 'Incorrect security answer. Please try again');
            }
    
            // Check for duplicate application
            if ($this->hasDuplicateApplication()) {
                $validator->errors()->add('email', 'An application with this email was submitted in the last 30 days. Please wait before submitting another application');
            }
        });
    }

    protected function hasDuplicateApplication()
    {
        return \App\Models\License\Applicant::query()
            ->where('email', $this->email)
            ->where('created_at', '>=', now()->subDays(30))
            ->exists();
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