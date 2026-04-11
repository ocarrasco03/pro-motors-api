<?php

declare(strict_types=1);

namespace App\Application\DTOs\Supplier;

use InvalidArgumentException;

final readonly class CreateSupplierDTO
{
    public function __construct(
        public string $name,
        public ?string $contactName = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $notes = null,
    ) {
        if (empty($this->name)) {
            throw new InvalidArgumentException('Supplier name is required');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            contactName: $data['contactName'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'contact_name' => $this->contactName,
            'email' => $this->email,
            'phone' => $this->phone,
            'notes' => $this->notes,
        ];
    }
}
