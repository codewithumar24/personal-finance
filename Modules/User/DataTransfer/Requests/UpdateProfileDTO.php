<?php
// modules/User/DataTransfer/Requests/UpdateProfileDTO.php

namespace Modules\User\DataTransfer\Requests;

use Modules\Core\DataTransfer\DTO;

final class UpdateProfileDTO implements DTO
{
    public function __construct(
        private readonly ?string $first_name = null,
        private readonly ?string $last_name = null,
        private readonly ?string $phone = null,
        private readonly ?string $date_of_birth = null,
        private readonly ?string $address = null,
        private readonly ?string $profile_image = null,
    ) { }

    public static function create(
        ?string $first_name = null,
        ?string $last_name = null,
        ?string $phone = null,
        ?string $date_of_birth = null,
        ?string $address = null,
        ?string $profile_image = null,
    ): self {
        return new self($first_name, $last_name, $phone, $date_of_birth, $address, $profile_image);
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
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

    public function getProfileImage(): ?string
    {
        return $this->profile_image;
    }

    public function hasFirstName(): bool
    {
        return !is_null($this->first_name);
    }

    public function hasLastName(): bool
    {
        return !is_null($this->last_name);
    }

    public function hasPhone(): bool
    {
        return !is_null($this->phone);
    }

    public function hasDateOfBirth(): bool
    {
        return !is_null($this->date_of_birth);
    }

    public function hasAddress(): bool
    {
        return !is_null($this->address);
    }

    public function hasProfileImage(): bool
    {
        return !is_null($this->profile_image);
    }
}