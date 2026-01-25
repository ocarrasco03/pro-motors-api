<?php

namespace App\Core\DTOs;

readonly class UserDTO
{
    public function __construct(
        public ?int $id,
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $username,
        public ?string $password,
        public bool $active,
        public int $companyId,
        public ?array $roles = null,
        public ?\Carbon\Carbon $lastLoginAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            email: $data['email'],
            username: $data['username'],
            password: $data['password'] ?? null,
            active: $data['active'] ?? true,
            companyId: $data['company_id'],
            roles: $data['roles'] ?? null,
            lastLoginAt: isset($data['last_login_at'])
                ? \Carbon\Carbon::parse($data['last_login_at'])
                : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'username' => $this->username,
            'password' => $this->password,
            'active' => $this->active,
            'company_id' => $this->companyId,
            'roles' => $this->roles,
            'last_login_at' => $this->lastLoginAt?->toISOString(),
        ], fn ($value) => $value !== null);
    }

    public function getFullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }
}
