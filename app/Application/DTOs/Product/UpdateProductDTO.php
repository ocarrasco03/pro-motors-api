<?php

declare(strict_types=1);

namespace App\Application\DTOs\Product;

final readonly class UpdateProductDTO
{
    public function __construct(
        public ?string $sku = null,
        public ?int $brandId = null,
        public ?string $description = null,
        public ?string $alternativeSku = null,
        public ?string $application = null,
        public ?string $ean = null,
        public ?string $satCode = null,
        public ?array $attributes = null,
        public ?bool $isActive = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            sku: $data['sku'] ?? null,
            brandId: isset($data['brandId']) ? (int) $data['brandId'] : null,
            description: $data['description'] ?? null,
            alternativeSku: $data['alternativeSku'] ?? null,
            application: $data['application'] ?? null,
            ean: $data['ean'] ?? null,
            satCode: $data['satCode'] ?? null,
            attributes: $data['attributes'] ?? null,
            isActive: $data['isActive'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'sku' => $this->sku,
            'brand_id' => $this->brandId,
            'description' => $this->description,
            'alternative_sku' => $this->alternativeSku,
            'application' => $this->application,
            'ean' => $this->ean,
            'sat_code' => $this->satCode,
            'attributes' => $this->attributes,
            'is_active' => $this->isActive,
        ], fn ($value) => $value !== null);
    }
}
