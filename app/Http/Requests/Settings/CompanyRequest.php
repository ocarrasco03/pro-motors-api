<?php

namespace App\Http\Requests\Settings;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()->can('create', $company);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'email' => ['required', 'email', 'string', 'max:255'],
            'ownerName' => ['nullable', 'sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'sometimes', 'string', 'max:20'],
            'address' => ['nullable', 'sometimes', 'string', 'max:255'],
            'city' => ['nullable', 'sometimes', 'string', 'max:255'],
            'state' => ['nullable', 'sometimes', 'string', 'max:255'],
            'zipCode' => ['nullable', 'sometimes', 'string', 'max:10'],
            'country' => ['nullable', 'sometimes', 'string', 'max:255'],
            'rfc' => ['nullable', 'sometimes', 'string', 'max:13'],
            'license' => ['required', 'string', 'in:corporate,individual'],
            'billingPeriod' => ['required', 'string', 'in:monthly,bimonthly,quarterly,annual,biannual'],
            'group' => ['nullable', 'sometimes', 'string', 'max:255', 'exists:groups,name'],
            'groupId' => ['required', 'sometimes', 'numeric:strict','exists:company_groups,id'],
            'tax' => ['nullable', 'sometimes', 'string', 'max:255', 'exists:taxes,name'],
            'taxId' => ['required', 'sometimes', 'numeric:strict', 'exists:taxes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Company name is required.',
            'name.string' => 'Company name must be a string.',
            'name.max' => 'Company name must not exceed 255 characters.',
            'name.unique' => 'Company name must be unique.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.string' => 'Email must be a string.',
            'email.max' => 'Email must not exceed 255 characters.',
            // Add more custom messages as needed
        ];
    }
}
