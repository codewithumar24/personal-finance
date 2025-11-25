<?php
// modules/Finance/Repositories/BudgetRepository.php

namespace Modules\Admin\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Admin\Contracts\Repositories\BudgetRepositoryContract;
use Modules\Admin\Entities\Budget;

class BudgetRepository implements BudgetRepositoryContract
{
    public function __construct(private readonly Budget $model) {}

    public function create(
        string $userId,
        string $categoryId,
        float $amount,
        string $startDate,
        string $endDate,
        ?string $description,
        bool $isActive
    ): Budget {
        $objQuery = $this->model->newQuery();

        return $objQuery->create([
            'budget_uuid' => Str::uuid(),
            'user_id' => $userId,
            'category_id' => $categoryId,
            'amount' => $amount,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'description' => $description,
            'is_active' => $isActive,
            'notifications' => [], // Initialize empty notifications
        ]);
    }

    public function findById(string $id): ?Budget
    {
        return $this->model->find($id);
    }

    public function findByUuid(string $uuid): ?Budget
    {
        return $this->model->where('budget_uuid', $uuid)->first();
    }

    public function getUserBudgets(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator
    {
        $query = $this->model->with(['category'])
            ->where('user_id', $userId);

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        $query->orderBy('created_at', 'desc');

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getUserBudgetByUuid(string $userId, string $uuid): ?Budget
    {
        return $this->model->with(['category'])
            ->where('user_id', $userId)
            ->where('budget_uuid', $uuid)
            ->first();
    }

    public function update(
        Budget $budget,
        ?string $categoryId,
        ?float $amount,
        ?string $startDate,
        ?string $endDate,
        ?string $description,
        ?bool $isActive
    ): Budget {
        if (is_string($categoryId) && $budget->category_id !== $categoryId) {
            $budget->category_id = $categoryId;
        }
        if (!is_null($amount) && $budget->amount !== $amount) {
            $budget->amount = $amount;
        }
        if (is_string($startDate) && $budget->start_date !== $startDate) {
            $budget->start_date = $startDate;
        }
        if (is_string($endDate) && $budget->end_date !== $endDate) {
            $budget->end_date = $endDate;
        }
        if (!is_null($description) && $budget->description !== $description) {
            $budget->description = $description;
        }
        if (is_bool($isActive) && $budget->is_active !== $isActive) {
            $budget->is_active = $isActive;
        }

        $budget->save();
        return $budget->fresh(['category']);
    }

    public function delete(Budget $budget): bool
    {
        return $budget->delete();
    }

    public function getCurrentBudgets(string $userId): Collection
    {
        $today = now()->format('Y-m-d');

        return $this->model->with(['category'])
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->get();
    }

    public function getBudgetByCategoryAndPeriod(string $userId, string $categoryId, string $startDate, string $endDate): ?Budget
    {
        return $this->model->where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->first();
    }
}
