<?php

declare(strict_types=1);

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class SetProductPriceRequest extends FormRequest
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
            'supplierId' => ['nullable', 'integer', 'exists:suppliers,id'],
            'currency' => ['sometimes', 'string', 'max:3', 'in:MXN,USD,EUR'],
            'cost' => ['required', 'numeric', 'min:0'],
            'discount' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'applyDiscount' => ['sometimes', 'boolean'],
        ];
    }
}
