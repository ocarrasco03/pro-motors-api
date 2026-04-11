<?php

declare(strict_types=1);

namespace App\Application\DTOs\Product;

use InvalidArgumentException;

final readonly class CreateProductDTO
{
    public function __construct(
        public string $sku,
        public int $brandId,
        public string $description,
        public ?string $alternativeSku = null,
        public ?string $application = null,
        public ?string $ean = null,
        public ?string $satCode = null,
        public ?array $attributes = null,
    ) {
        if (empty($this->sku)) {
            throw new InvalidArgumentException('SKU is required');
        }

        if (empty($this->description)) {
            throw new InvalidArgumentException('Description is required');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            sku: $data['sku'],
            brandId: (int) $data['brandId'],
            description: $data['description'],
            alternativeSku: $data['alternativeSku'] ?? null,
            application: $data['application'] ?? null,
            ean: $data['ean'] ?? null,
            satCode: $data['satCode'] ?? null,
            attributes: $data['attributes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'sku' => $this->sku,
            'brand_id' => $this->brandId,
            'description' => $this->description,
            'alternative_sku' => $this->alternativeSku,
            'application' => $this->application,
            'ean' => $this->ean,
            'sat_code' => $this->satCode,
            'attributes' => $this->attributes,
        ];
    }
}
