<?php
// modules/Admin/DataTransfer/Requests/CreateUserDTO.php

namespace Modules\Admin\DataTransfer\Requests;

use Modules\Core\DataTransfer\DTO;

final class CreateUserDTO implements DTO
{
    public function __construct(
        private readonly string $first_name,
        private readonly string $last_name,
        private readonly string $email,
        private readonly string $password,
        private readonly ?string $phone = null,
        private readonly ?string $date_of_birth = null,
        private readonly ?string $address = null,
        private readonly array $roles = [],
        private readonly bool $is_active = true,
        private readonly bool $send_welcome_email = false,
    ) { }

    public static function create(
        string $first_name,
        string $last_name,
        string $email,
        string $password,
        ?string $phone = null,
        ?string $date_of_birth = null,
        ?string $address = null,
        array $roles = [],
        bool $is_active = true,
        bool $send_welcome_email = false,
    ): self {
        return new self(
            $first_name, 
            $last_name, 
            $email, 
            $password, 
            $phone, 
            $date_of_birth, 
            $address, 
            $roles, 
            $is_active, 
            $send_welcome_email
        );
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getDateOfBirth(): ?string
    {
        return $this->date_of_birth;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getIsActive(): bool
    {
        return $this->is_active;
    }

    public function getSendWelcomeEmail(): bool
    {
        return $this->send_welcome_email;
    }
}