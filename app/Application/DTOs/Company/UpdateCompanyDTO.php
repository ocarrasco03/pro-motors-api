<?php

namespace App\Application\DTOs\Company;

use App\Data\Casts\EnumCaster;
use App\Domain\ValueObjects\Enums\BillingPeriodEnum;
use App\Domain\ValueObjects\Enums\LicenseEnum;
use App\Domain\ValueObjects\Enums\StatusEnum;

final readonly class UpdateCompanyDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $ownerName = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $country = null,
        public ?string $zipCode = null,
        public ?string $rfc = null,
        public ?int $companyGroupId = null,
        public ?string $companyGroupName = null,
        public ?StatusEnum $status = null,
        public ?BillingPeriodEnum $billingPeriod = null,
        public ?int $taxId = null,
        public ?string $tax = null,
        public ?LicenseEnum $license = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            ownerName: $data['ownerName'] ?? null,
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            country: $data['country'] ?? null,
            zipCode: $data['zipCode'] ?? null,
            rfc: $data['rfc'] ?? null,
            companyGroupId: $data['companyGroupId'] ?? null,
            companyGroupName: $data['companyGroupName'] ?? null,
            status: EnumCaster::cast(StatusEnum::class, $data['status'] ?? null, null),
            billingPeriod: EnumCaster::cast(BillingPeriodEnum::class, $data['billingPeriod'] ?? null, null),
            taxId: $data['taxId'] ?? null,
            tax: $data['tax'] ?? null,
            license: EnumCaster::cast(LicenseEnum::class, $data['license'] ?? null, null),
        );
    }

    public function toArray(): array
    {

        $values = [
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
            'company_group_name' => $this->companyGroupName,
            'status' => $this->status,
            'billing_period' => $this->billingPeriod,
            'tax_id' => $this->taxId,
            'tax_name' => $this->tax,
            'license' => $this->license,
        ];

        return $this->filterNullValues($values);
    }

    private function filterNullValues(array $data): array
    {
        return array_filter($data, fn ($value) => !is_null($value));
    }
}
