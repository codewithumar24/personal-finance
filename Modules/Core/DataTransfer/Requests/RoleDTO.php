<?php
// modules/Core/DataTransfer/Requests/RoleDTO.php

namespace Modules\Core\DataTransfer\Requests;

use Modules\Core\DataTransfer\DTO;

final class RoleDTO implements DTO
{
    public function __construct(
        private readonly string $name,
        private readonly ?string $description = null,
        private readonly bool $is_default = false,
        private readonly array $permissions = []
    ) { }

    public static function create(
        string $name,
        ?string $description = null,
        bool $is_default = false,
        array $permissions = []
    ): self {
        return new self($name, $description, $is_default, $permissions);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getIsDefault(): bool
    {
        return $this->is_default;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }
}