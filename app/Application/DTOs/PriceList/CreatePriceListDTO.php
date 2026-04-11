<?php

declare(strict_types=1);

namespace App\Application\DTOs\PriceList;

use InvalidArgumentException;

final readonly class CreatePriceListDTO
{
    public function __construct(
        public int $companyId,
        public string $name,
        public ?string $description = null,
        public bool $isDefault = false,
    ) {
        if (empty($this->name)) {
            throw new InvalidArgumentException('Price list name is required');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            companyId: (int) $data['companyId'],
            name: $data['name'],
            description: $data['description'] ?? null,
            isDefault: $data['isDefault'] ?? false,
        );
    }

    public function toArray(): array
    {
        return [
            'company_id' => $this->companyId,
            'name' => $this->name,
            'description' => $this->description,
            'is_default' => $this->isDefault,
        ];
    }
}
