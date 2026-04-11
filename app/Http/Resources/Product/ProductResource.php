<?php

declare(strict_types=1);

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'brand' => $this->whenLoaded('brand', fn () => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
                'slug' => $this->brand->slug,
            ]),
            'description' => $this->description,
            'alternativeSku' => $this->alternative_sku,
            'application' => $this->application,
            'ean' => $this->ean,
            'satCode' => $this->sat_code,
            'attributes' => $this->attributes,
            'isActive' => $this->is_active,
            'suppliers' => $this->whenLoaded('suppliers', fn () => SupplierResource::collection($this->suppliers)),
            'priceLists' => $this->whenLoaded('priceLists', fn () => PriceListResource::collection($this->priceLists)),
            'equivalences' => $this->whenLoaded('equivalences', fn () => ProductResource::collection($this->equivalences)),
            'relations' => $this->whenLoaded('relations', fn () => ProductResource::collection($this->relations)),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
