<?php

declare(strict_types=1);

namespace App\Application\DTOs\Brand;

final readonly class UpdateBrandDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $logoUrl = null,
        public ?bool $isActive = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            logoUrl: $data['logoUrl'] ?? null,
            isActive: $data['isActive'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'logo_url' => $this->logoUrl,
            'is_active' => $this->isActive,
        ], fn ($value) => $value !== null);
    }
}
