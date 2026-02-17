<?php

namespace App\Application\DTOs\Company;

use InvalidArgumentException;

final readonly class CompanyFilterDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $ownerName = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $country = null,
        public ?string $zip_code = null,
        public ?string $rfc = null,
        public ?int $company_group_id = null,
        public ?string $company_group_name = null,
        public ?string $status = null,
        public ?string $billing_period = null,
        public int $tax_id,
        public ?string $license = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            ownerName: $data['owner_name'] ?? null,
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            country: $data['country'] ?? null,
            zip_code: $data['zip_code'] ?? null,
            rfc: $data['rfc'] ?? null,
            company_group_id: $data['company_group_id'] ?? null,
            company_group_name: $data['company_group_name'] ?? null,
            status: $data['status'] ?? null,
            billing_period: $data['billing_period'] ?? null,
            tax_id: $data['tax_id'],
            license: $data['license'] ?? null,
        );
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
            'zip_code' => $this->zip_code,
            'rfc' => $this->rfc,
            'company_group_id' => $this->company_group_id,
            'company_group_name' => $this->company_group_name,
            'status' => $this->status,
            'billing_period' => $this->billing_period,
            'tax_id' => $this->tax_id,
            'license' => $this->license,
        ];
    }
}
