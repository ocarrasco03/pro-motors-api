<?php

declare(strict_types=1);

namespace App\Application\DTOs\Brand;

use InvalidArgumentException;

final readonly class CreateBrandDTO
{
    public function __construct(
        public string $name,
        public ?string $logoUrl = null,
    ) {
        if (empty($this->name)) {
            throw new InvalidArgumentException('Brand name is required');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            logoUrl: $data['logoUrl'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'logo_url' => $this->logoUrl,
        ];
    }
}
