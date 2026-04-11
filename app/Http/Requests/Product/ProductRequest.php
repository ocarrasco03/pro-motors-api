<?php

declare(strict_types=1);

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:100'],
            'brandId' => ['required', 'integer', 'exists:brands,id'],
            'description' => ['required', 'string', 'max:1000'],
            'alternativeSku' => ['nullable', 'string', 'max:100'],
            'application' => ['nullable', 'string', 'max:500'],
            'ean' => ['nullable', 'string', 'max:50'],
            'satCode' => ['nullable', 'string', 'max:50'],
            'attributes' => ['nullable', 'array'],
        ];
    }
}
