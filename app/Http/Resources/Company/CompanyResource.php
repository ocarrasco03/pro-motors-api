<?php

namespace App\Http\Resources\Company;

use App\Http\Resources\Settings\CompanyGroupResource;
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
            'email' => $this->email,
            'ownerName' => $this->owner_name,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zipCode' => $this->zip_code,
            'country' => $this->country,
            'rfc' => $this->rfc,
            'companyGroup' => $this->whenLoaded(
                'companyGroup',
                fn () => new CompanyGroupResource($this->companyGroup)
            ),
            'tax' => $this->whenLoaded(
                'tax',
                fn () => new CompanyTaxResource($this->tax)
            ),
            'price_list' => null,
            'license' => $this->license,
            'status' => $this->status,
            'billingPeriod' => $this->billing_period,
            'usersAssigned' => $this->users->count(),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'updatedBy' => $this->updated_by,
        ];
    }
}
