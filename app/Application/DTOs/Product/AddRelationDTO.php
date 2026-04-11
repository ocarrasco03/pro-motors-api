<?php

declare(strict_types=1);

namespace App\Application\DTOs\Product;

use App\Data\Casts\EnumCaster;
use App\Domain\ValueObjects\Enums\RelationTypeEnum;
use InvalidArgumentException;

final readonly class AddRelationDTO
{
    public function __construct(
        public int $productId,
        public int $relatedProductId,
        public ?RelationTypeEnum $relationType = RelationTypeEnum::ACCESSORY,
        public ?string $notes = null,
    ) {
        if ($this->productId === $this->relatedProductId) {
            throw new InvalidArgumentException('A product cannot be related to itself');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            productId: (int) $data['productId'],
            relatedProductId: (int) $data['relatedProductId'],
            relationType: EnumCaster::cast(RelationTypeEnum::class, $data['relationType'] ?? null, RelationTypeEnum::ACCESSORY),
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'related_product_id' => $this->relatedProductId,
            'relation_type' => $this->relationType,
            'notes' => $this->notes,
        ];
    }
}
