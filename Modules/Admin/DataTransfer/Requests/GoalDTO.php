<?php
// modules/Finance/DataTransfer/Requests/GoalDTO.php

namespace Modules\Admin\DataTransfer\Requests;

use Modules\Core\DataTransfer\DTO;
use Modules\Admin\Enum\GoalType;

final class GoalDTO implements DTO
{
    public function __construct(
        private readonly string $name,
        private readonly float $target_amount,
        private readonly string $target_date,
        private readonly GoalType $type,
        private readonly ?string $color = null,
        private readonly ?string $icon = null,
        private readonly ?string $description = null,
        private readonly ?bool $is_active = true,
    ) { }

    public static function create(
        string $name,
        float $target_amount,
        string $target_date,
        GoalType $type,
        ?string $color = null,
        ?string $icon = null,
        ?string $description = null,
        ?bool $is_active = true,
    ): self {
        return new self($name, $target_amount, $target_date, $type, $color, $icon, $description, $is_active);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTargetAmount(): float
    {
        return $this->target_amount;
    }

    public function getTargetDate(): string
    {
        return $this->target_date;
    }

    public function getType(): GoalType
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }
}
