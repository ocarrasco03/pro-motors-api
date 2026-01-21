<?php

namespace App\Http\Requests\Settings;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()->can('update', $company);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'sometimes', 'string', 'max:255', Rule::unique('companies')->ignore($this->route('company'))],
            'email' => ['required', 'sometimes', 'email', 'string', 'max:255'],
            'ownerName' => ['nullable', 'sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'sometimes', 'string', 'max:20'],
            'address' => ['nullable', 'sometimes', 'string', 'max:255'],
            'city' => ['nullable', 'sometimes', 'string', 'max:255'],
            'state' => ['nullable', 'sometimes', 'string', 'max:255'],
            'zipCode' => ['nullable', 'sometimes', 'string', 'max:10'],
            'country' => ['nullable', 'sometimes', 'string', 'max:255'],
            'rfc' => ['nullable', 'sometimes', 'string', 'max:13'],
            'license' => ['required', 'sometimes', 'string', 'in:corporate,individual'],
            'billingPeriod' => ['required', 'sometimes', 'string', 'in:monthly,bimonthly,quarterly,annual,biannual'],
            'group' => ['nullable', 'sometimes', 'string', 'max:255', 'exists:groups,name'],
            'groupId' => ['nullable', 'sometimes', 'numeric:strict','exists:company_groups,id'],
            'tax' => ['required', 'sometimes', 'string', 'max:255', 'exists:taxes,name'],
            'taxId' => ['required', 'sometimes', 'numeric:strict', 'exists:taxes,id'],
        ];
    }
}
