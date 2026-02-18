<?php

namespace App\Application\DTOs\User;

final readonly class UpdateUserDTO
{
    public function __construct(
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $email = null,
        public ?string $username = null,
        public ?string $password = null,
        public ?bool $active = null,
        public ?int $companyId = null,
        public ?string $companyName = null,
        public ?string $role = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['firstName'] ?? null,
            lastName: $data['lastName'] ?? null,
            email: $data['email'] ?? null,
            username: $data['username'] ?? null,
            password: $data['password'] ?? null,
            active: $data['active'] ?? null,
            companyId: $data['companyId'] ?? null,
            companyName: $data['companyName'] ?? null,
            role: $data['role'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'username' => $this->username,
            'password' => $this->password,
            'active' => $this->active,
            'company_id' => $this->companyId,
            'company_name' => $this->companyName,
            'role' => $this->role,
        ];

        return $this->filterNullValues($data);
    }

    private function filterNullValues(array $data): array
    {
        return array_filter($data, fn ($value) => !is_null($value));
    }
}
