<?php
// modules/Admin/DataTransfer/Requests/AssignRoleDTO.php

namespace Modules\Admin\DataTransfer\Requests;

use Modules\Core\DataTransfer\DTO;

final class AssignRoleDTO implements DTO
{
    public function __construct(
        private readonly array $roles,
    ) { }

    public static function create(array $roles): self
    {
        return new self($roles);
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}