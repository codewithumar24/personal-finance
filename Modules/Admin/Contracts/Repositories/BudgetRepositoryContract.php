<?php
// modules/Finance/Contracts/Repositories/BudgetRepositoryContract.php

namespace Modules\Admin\Contracts\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Admin\Entities\Budget;

interface BudgetRepositoryContract
{
    public function create(string $userId,
                           string $categoryId,
                           float $amount,
                           string $startDate,
                           string $endDate,
                           ?string $description,
                           bool $isActive): Budget;
    public function findById(string $id): ?Budget;
    public function findByUuid(string $uuid): ?Budget;
    public function getUserBudgets(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator;
    public function getUserBudgetByUuid(string $userId, string $uuid): ?Budget;
    public function update( Budget $budget,
                            ?string $categoryId,
                            ?float $amount,
                            ?string $startDate,
                            ?string $endDate,
                            ?string $description,
                            ?bool $isActive): Budget;
    public function delete(Budget $budget): bool;
    public function getCurrentBudgets(string $userId): Collection;
    public function getBudgetByCategoryAndPeriod(string $userId, string $categoryId, string $startDate, string $endDate): ?Budget;
}
