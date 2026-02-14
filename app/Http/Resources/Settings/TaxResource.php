<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'rate' => $this->rate,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'updatedBy' => $this->updated_by,
        ];
    }
}
