<?php
// modules/User/DataTransfer/Requests/RegisterDTO.php

namespace Modules\User\DataTransfer\Requests;

use Modules\Core\DataTransfer\DTO;

final class RegisterDTO implements DTO
{
    public function __construct(
        private readonly string $first_name,
        private readonly string $last_name,
        private readonly string $email,
        private readonly string $password,
        private readonly ?string $phone = null,
        private readonly ?string $date_of_birth = null,
        private readonly ?string $address = null,
    ) { }

    public static function create(
        string $first_name,
        string $last_name,
        string $email,
        string $password,
        ?string $phone = null,
        ?string $date_of_birth = null,
        ?string $address = null,
    ): self {
        return new self($first_name, $last_name, $email, $password, $phone, $date_of_birth, $address);
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
}