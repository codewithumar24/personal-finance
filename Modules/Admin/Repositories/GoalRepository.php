<?php
// modules/Finance/Repositories/GoalRepository.php

namespace Modules\Admin\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Admin\Contracts\Repositories\GoalRepositoryContract;
use Modules\Admin\Entities\Goal;

class GoalRepository implements GoalRepositoryContract
{
    public function __construct(private readonly Goal $model) {}

    public function create(
        string $userId,
        string $name,
        float $targetAmount,
        string $targetDate,
        GoalType $type,
        ?string $color,
        ?string $icon,
        ?string $description,
        bool $isActive
    ): Goal {
        $objQuery = $this->model->newQuery();

        return $objQuery->create([
            'goal_uuid' => Str::uuid(),
            'user_id' => $userId,
            'name' => $name,
            'target_amount' => $targetAmount,
            'current_amount' => 0, // Default current amount
            'target_date' => $targetDate,
            'type' => $type->value,
            'color' => $color ?? '#3B82F6',
            'icon' => $icon,
            'description' => $description,
            'is_active' => $isActive,
            'is_completed' => false, // Default not completed
        ]);
    }

    public function findById(string $id): ?Goal
    {
        return $this->model->find($id);
    }

    public function findByUuid(string $uuid): ?Goal
    {
        return $this->model->where('goal_uuid', $uuid)->first();
    }

    public function getUserGoals(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator
    {
        $query = $this->model->where('user_id', $userId);

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['is_completed'])) {
            $query->where('is_completed', $filters['is_completed']);
        }

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $query->orderBy('created_at', 'desc');

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getUserGoalByUuid(string $userId, string $uuid): ?Goal
    {
        return $this->model->where('user_id', $userId)
            ->where('goal_uuid', $uuid)
            ->first();
    }

    public function update(
        Goal $goal,
        ?string $name,
        ?float $targetAmount,
        ?string $targetDate,
        ?GoalType $type,
        ?string $color,
        ?string $icon,
        ?string $description,
        ?bool $isActive
    ): Goal {
        if (is_string($name) && $goal->name !== $name) {
            $goal->name = $name;
        }
        if (!is_null($targetAmount) && $goal->target_amount !== $targetAmount) {
            $goal->target_amount = $targetAmount;
        }
        if (is_string($targetDate) && $goal->target_date !== $targetDate) {
            $goal->target_date = $targetDate;
        }
        if ($type instanceof GoalType && $goal->type !== $type->value) {
            $goal->type = $type->value;
        }
        if (!is_null($color) && $goal->color !== $color) {
            $goal->color = $color;
        }
        if (!is_null($icon) && $goal->icon !== $icon) {
            $goal->icon = $icon;
        }
        if (!is_null($description) && $goal->description !== $description) {
            $goal->description = $description;
        }
        if (is_bool($isActive) && $goal->is_active !== $isActive) {
            $goal->is_active = $isActive;
        }

        $goal->save();
        return $goal->fresh();
    }
    public function delete(Goal $goal): bool
    {
        return $goal->delete();
    }

    public function getActiveGoals(string $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->where('is_active', true)
            ->where('is_completed', false)
            ->get();
    }

    public function getCompletedGoals(string $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->where('is_completed', true)
            ->get();
    }
}
