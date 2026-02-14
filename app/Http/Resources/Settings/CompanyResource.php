<?php

namespace App\Http\Resources\Settings;

use App\Http\Resources\Settings\CompanyGroupResource;
use App\Http\Resources\Settings\TaxResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'slug' => $this->slug,
            'companyGroup' => $this->whenLoaded(
                'companyGroup',
                fn () => new CompanyGroupResource($this->companyGroup)
            ),
            'tax' => $this->whenLoaded(
                'tax',
                fn () => new TaxResource($this->tax)
            ),
            'price_list' => null,
            'rfc' => $this->rfc,
            'license' => $this->license,
            'status' => $this->status,
            'billingPeriod' => $this->billing_period,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'updatedBy' => $this->updated_by,
        ];
    }
}
