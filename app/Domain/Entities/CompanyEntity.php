<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Enums\LicenseEnum;
use App\Domain\ValueObjects\Enums\StatusEnum;
use InvalidArgumentException;

final class CompanyEntity
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $ownerName = null,
        public readonly ?string $phone = null,
        public readonly ?string $address = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $country = null,
        public readonly ?string $zipCode = null,
        public readonly ?string $rfc = null,
        public readonly ?int $companyGroupId = null,
        public readonly ?int $taxId = null,
        public readonly ?LicenseEnum $license = null,
        public readonly StatusEnum $status = StatusEnum::ACTIVE,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (empty($this->name)) {
            throw new InvalidArgumentException('Company name is required');
        }

        if (! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }

        if ($this->rfc && strlen($this->rfc) < 10) {
            throw new InvalidArgumentException('RFC must be at least 10 characters');
        }
    }

    public function isActive(): bool
    {
        return $this->status === StatusEnum::ACTIVE;
    }

    public function canBeEdited(): bool
    {
        return $this->status === StatusEnum::ACTIVE;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'owner_name' => $this->ownerName,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'zip_code' => $this->zipCode,
            'rfc' => $this->rfc,
            'company_group_id' => $this->companyGroupId,
            'tax_id' => $this->taxId,
            'license' => $this->license?->value,
        ];
    }
}
