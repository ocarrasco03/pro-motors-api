<?php

namespace App\Application\DTOs\User;

final readonly class StoreUserDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $username,
        public string $password,
        public ?bool $active = true,
        public ?int $companyId = null,
        public ?string $companyName = null,
        public ?string $role = null,
    ) {
        if (empty($this->firstName) || empty($this->lastName) || empty($this->email) || empty($this->username) || empty($this->password)) {
            throw new \InvalidArgumentException('First name, last name, email, username, and password are required fields.');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['firstName'],
            lastName: $data['lastName'],
            email: $data['email'],
            username: $data['username'],
            password: $data['password'],
            active: $data['active'] ?? true,
            companyId: $data['companyId'] ?? null,
            companyName: $data['companyName'] ?? null,
            role: $data['role'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
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
    }
}
