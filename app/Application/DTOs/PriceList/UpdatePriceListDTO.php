<?php

declare(strict_types=1);

namespace App\Application\DTOs\PriceList;

final readonly class UpdatePriceListDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
        public ?bool $isDefault = null,
        public ?bool $isActive = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            isDefault: $data['isDefault'] ?? null,
            isActive: $data['isActive'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'is_default' => $this->isDefault,
            'is_active' => $this->isActive,
        ], fn ($value) => $value !== null);
    }
}
