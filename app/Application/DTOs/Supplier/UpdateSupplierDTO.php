<?php

declare(strict_types=1);

namespace App\Application\DTOs\Supplier;

final readonly class UpdateSupplierDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $contactName = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $notes = null,
        public ?bool $isActive = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            contactName: $data['contactName'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            notes: $data['notes'] ?? null,
            isActive: $data['isActive'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'contact_name' => $this->contactName,
            'email' => $this->email,
            'phone' => $this->phone,
            'notes' => $this->notes,
            'is_active' => $this->isActive,
        ], fn ($value) => $value !== null);
    }
}
