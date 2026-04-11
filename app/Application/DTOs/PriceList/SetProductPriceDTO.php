<?php

declare(strict_types=1);

namespace App\Application\DTOs\PriceList;

use InvalidArgumentException;

final readonly class SetProductPriceDTO
{
    public function __construct(
        public int $priceListId,
        public int $productId,
        public ?int $supplierId = null,
        public string $currency = 'MXN',
        public float $cost = 0,
        public float $discount = 0,
        public bool $applyDiscount = true,
    ) {
        if ($this->cost < 0) {
            throw new InvalidArgumentException('Cost cannot be negative');
        }

        if ($this->discount < 0 || $this->discount > 100) {
            throw new InvalidArgumentException('Discount must be between 0 and 100');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            priceListId: (int) $data['priceListId'],
            productId: (int) $data['productId'],
            supplierId: isset($data['supplierId']) ? (int) $data['supplierId'] : null,
            currency: $data['currency'] ?? 'MXN',
            cost: (float) ($data['cost'] ?? 0),
            discount: (float) ($data['discount'] ?? 0),
            applyDiscount: $data['applyDiscount'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'price_list_id' => $this->priceListId,
            'product_id' => $this->productId,
            'supplier_id' => $this->supplierId,
            'currency' => $this->currency,
            'cost' => $this->cost,
            'discount' => $this->discount,
            'apply_discount' => $this->applyDiscount,
        ];
    }
}
