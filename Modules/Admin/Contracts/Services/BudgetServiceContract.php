<?php
// modules/Finance/Contracts/Services/BudgetServiceContract.php

namespace Modules\Admin\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Admin\DataTransfer\Requests\BudgetDTO;
use Modules\Admin\Entities\Budget;

interface BudgetServiceContract
{
    public function createBudget(BudgetDTO $dto, string $userId): Budget;
    public function getUserBudgets(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator;
    public function getUserBudgetByUuid(string $userId, string $uuid): ?Budget;
    public function updateBudget(Budget $budget, BudgetDTO $dto): Budget;
    public function deleteBudget(Budget $budget): bool;
    public function getCurrentBudgets(string $userId): Collection;
    public function checkBudgetAlerts(string $userId): array;
    public function getBudgetProgress(string $budgetId): array;
}
