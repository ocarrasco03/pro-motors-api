<?php

declare(strict_types=1);

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class EquivalenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'productId' => ['required', 'integer', 'exists:products,id'],
            'relationType' => ['nullable', 'string', 'in:equivalent,substitute,complementary,accessory,upgrade,cross_reference'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
