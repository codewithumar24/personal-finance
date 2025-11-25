<?php
// modules/Finance/Contracts/Services/GoalServiceContract.php

namespace Modules\Admin\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Admin\DataTransfer\Requests\GoalDTO;
use Modules\Admin\Entities\Goal;

interface GoalServiceContract
{
    public function createGoal(GoalDTO $dto, string $userId): Goal;
    public function getUserGoals(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator;
    public function getUserGoalByUuid(string $userId, string $uuid): ?Goal;
    public function updateGoal(Goal $goal, GoalDTO $dto): Goal;
    public function deleteGoal(Goal $goal): bool;
    public function addToGoal(Goal $goal, float $amount): Goal;
    public function getGoalProgress(string $goalId): array;
    public function checkGoalAlerts(string $userId): array;
}
