<?php
// modules/Finance/Contracts/Repositories/GoalRepositoryContract.php

namespace Modules\Admin\Contracts\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Admin\Entities\Goal;

interface GoalRepositoryContract
{
    public function create( string $userId,
                            string $name,
                            float $targetAmount,
                            string $targetDate,
                            GoalType $type,
                            ?string $color,
                            ?string $icon,
                            ?string $description,
                            bool $isActive): Goal;
    public function findById(string $id): ?Goal;
    public function findByUuid(string $uuid): ?Goal;
    public function getUserGoals(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator;
    public function getUserGoalByUuid(string $userId, string $uuid): ?Goal;
    public function update(Goal $goal,
                           ?string $name,
                           ?float $targetAmount,
                           ?string $targetDate,
                           ?GoalType $type,
                           ?string $color,
                           ?string $icon,
                           ?string $description,
                           ?bool $isActive): Goal;
    public function delete(Goal $goal): bool;
    public function getActiveGoals(string $userId): Collection;
    public function getCompletedGoals(string $userId): Collection;
}
