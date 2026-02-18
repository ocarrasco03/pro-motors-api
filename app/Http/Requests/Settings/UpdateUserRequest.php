<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = $this->route('user');

        return $this->user()->can('update', $user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstName' => ['sometimes', 'string', 'max:255'],
            'lastName' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255'],
            'username' => ['sometimes', 'required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'sometimes', 'string', 'min:8', 'confirmed'],
            'companyName' => ['sometimes', 'nullable', 'string', 'max:255', 'exists:companies,name'],
            'companyId' => ['sometimes', 'nullable', 'integer', 'exists:companies,id'],
            'role' => ['sometimes', 'string'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
