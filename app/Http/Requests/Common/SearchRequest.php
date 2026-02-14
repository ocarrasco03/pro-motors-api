<?php

namespace App\Http\Requests\Common;

use App\Application\DTOs\Common\SearchDTO;
use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'perPage' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
            'search' => 'sometimes|string|nullable|min:3|max:100',
            'orderBy' => 'sometimes|string|nullable|in:asc,desc',
            'sortBy' => 'sometimes|string|nullable',
            'filterBy' => 'sometimes|array|nullable',
        ];
    }

    public function toDTO(): SearchDTO
    {
        return new SearchDTO(
            search: $this->input('search'),
            sortBy: $this->input('sortBy', 'id'),
            orderBy: $this->input('orderBy', 'asc'),
            perPage: $this->input('perPage', 15),
            authUser: $this->user(),
            page: $this->input('page', 1),
            filterBy: $this->input('filterBy', []),
        );
    }
}
