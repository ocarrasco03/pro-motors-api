<?php

declare(strict_types=1);

namespace App\Application\DTOs\Product;

use App\Data\Casts\EnumCaster;
use App\Domain\ValueObjects\Enums\RelationTypeEnum;
use InvalidArgumentException;

final readonly class AddEquivalenceDTO
{
    public function __construct(
        public int $productId,
        public int $equivalentProductId,
        public ?RelationTypeEnum $relationType = RelationTypeEnum::EQUIVALENT,
        public ?string $notes = null,
    ) {
        if ($this->productId === $this->equivalentProductId) {
            throw new InvalidArgumentException('A product cannot be equivalent to itself');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            productId: (int) $data['productId'],
            equivalentProductId: (int) $data['equivalentProductId'],
            relationType: EnumCaster::cast(RelationTypeEnum::class, $data['relationType'] ?? null, RelationTypeEnum::EQUIVALENT),
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'equivalent_product_id' => $this->equivalentProductId,
            'relation_type' => $this->relationType,
            'notes' => $this->notes,
        ];
    }
}
