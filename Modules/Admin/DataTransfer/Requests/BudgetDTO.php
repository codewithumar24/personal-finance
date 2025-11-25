<?php
// modules/Finance/DataTransfer/Requests/BudgetDTO.php

namespace Modules\Admin\DataTransfer\Requests;

use Modules\Core\DataTransfer\DTO;

final class BudgetDTO implements DTO
{
    public function __construct(
        private readonly string $category_id,
        private readonly float $amount,
        private readonly string $start_date,
        private readonly string $end_date,
        private readonly ?string $description = null,
        private readonly ?bool $is_active = true,
    ) { }

    public static function create(
        string $category_id,
        float $amount,
        string $start_date,
        string $end_date,
        ?string $description = null,
        ?bool $is_active = true,
    ): self {
        return new self($category_id, $amount, $start_date, $end_date, $description, $is_active);
    }

    public function getCategoryId(): string
    {
        return $this->category_id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getStartDate(): string
    {
        return $this->start_date;
    }

    public function getEndDate(): string
    {
        return $this->end_date;
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
