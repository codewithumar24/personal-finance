<?php
// modules/Finance/DataTransfer/Requests/CategoryDTO.php

namespace Modules\Admin\DataTransfer\Requests;

use Modules\Core\DataTransfer\DTO;
use Modules\Admin\Enum\CategoryType;

final class CategoryDTO implements DTO
{
    public function __construct(
        private readonly string $name,
        private readonly CategoryType $type,
        private readonly ?string $color,
        private readonly ?string $icon,
        private readonly ?bool $is_active,
        private readonly ?int $sort_order,
        private readonly ?string $description,
    ) { }

    public static function create(
        string $name,
        CategoryType $type,
        ?string $color = null,
        ?string $icon = null,
        ?bool $is_active = true,
        ?int $sort_order = 0,
        ?string $description = null,
    ): self {
        return new self($name, $type, $color, $icon, $is_active, $sort_order, $description);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): CategoryType
    {
        return $this->type;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function getSortOrder(): ?int
    {
        return $this->sort_order;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}