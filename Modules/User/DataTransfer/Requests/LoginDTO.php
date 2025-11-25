<?php
// modules/User/DataTransfer/Requests/LoginDTO.php

namespace Modules\User\DataTransfer\Requests;

use Modules\Core\DataTransfer\DTO;

final class LoginDTO implements DTO
{
    public function __construct(
        private readonly string $email,
        private readonly string $password,
        private readonly bool $remember_me = false,
    ) { }

    public static function create(
        string $email,
        string $password,
        bool $remember_me = false,
    ): self {
        return new self($email, $password, $remember_me);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRememberMe(): bool
    {
        return $this->remember_me;
    }
}