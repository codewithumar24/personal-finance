<?php
// modules/Finance/Services/BudgetService.php

namespace Modules\Admin\Services;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Contracts\Repositories\BudgetRepositoryContract;
use Modules\Admin\Contracts\Services\BudgetServiceContract;
use Modules\Admin\DataTransfer\Requests\BudgetDTO;
use Modules\Admin\Entities\Budget;
use Modules\Admin\Entities\Category;

class BudgetService implements BudgetServiceContract
{
    public function __construct(
        private readonly BudgetRepositoryContract $budgetRepository
    ) {}

    public function createBudget(BudgetDTO $dto, string $userId): Budget
    {
        $category = Category::where('category_uuid', $dto->getCategoryId())->first();

        if (!$category) {
            throw new \Exception('Invalid category');
        }

        // Check for overlapping budgets
        $existingBudget = $this->budgetRepository->getBudgetByCategoryAndPeriod(
            $userId,
            $category->id,
            $dto->getStartDate(),
            $dto->getEndDate()
        );

        if ($existingBudget) {
            throw new \Exception('A budget already exists for this category and time period');
        }

        return $this->budgetRepository->create(
            $userId,
            $category->id,
            $dto->getAmount(),
            $dto->getStartDate(),
            $dto->getEndDate(),
            $dto->getDescription(),
            $dto->getIsActive() ?? true
        );
    }

    public function getUserBudgets(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator
    {
        return $this->budgetRepository->getUserBudgets($userId, $filters, $perPage);
    }

    public function getUserBudgetByUuid(string $userId, string $uuid): ?Budget
    {
        return $this->budgetRepository->getUserBudgetByUuid($userId, $uuid);
    }

    public function updateBudget(Budget $budget, BudgetDTO $dto): Budget
    {
        $categoryId = null;
        if ($dto->getCategoryId()) {
            $category = Category::where('category_uuid', $dto->getCategoryId())->first();
            if ($category) {
                $categoryId = $category->id;
            }
        }

        return $this->budgetRepository->update(
            $budget,
            $categoryId,
            $dto->getAmount(),
            $dto->getStartDate(),
            $dto->getEndDate(),
            $dto->getDescription(),
            $dto->getIsActive()
        );
    }

    public function deleteBudget(Budget $budget): bool
    {
        return $this->budgetRepository->delete($budget);
    }

    public function getCurrentBudgets(string $userId): Collection
    {
        $today = Carbon::today();

        return Budget::with(['category'])
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
//            ->orderBy('category.name')
            ->get()
            ->sortBy(function ($budget) {
                return $budget->category->name ?? '';
            })
        ->
        values();
    }

    public function checkBudgetAlerts(string $userId): array
    {
        $alerts = [];
        $budgets = $this->getCurrentBudgets($userId);

        foreach ($budgets as $budget) {
            $progress = $budget->progress_percentage;
            $daysRemaining = $budget->days_remaining;

            // Alert for exceeded budget
            if ($budget->is_exceeded) {
                $alerts[] = [
                    'type' => 'budget_exceeded',
                    'message' => "Budget for {$budget->category->name} has been exceeded by " . number_format($budget->spent_amount - $budget->amount, 2),
                    'budget_id' => $budget->id,
                    'severity' => 'high',
                ];
            }
            // Alert for nearing budget limit (80%)
            elseif ($progress >= 80 && $progress < 100) {
                $alerts[] = [
                    'type' => 'budget_warning',
                    'message' => "Budget for {$budget->category->name} is at " . round($progress) . "% with {$daysRemaining} days remaining",
                    'budget_id' => $budget->id,
                    'severity' => 'medium',
                ];
            }
            // Alert for low time remaining with high spending
            elseif ($daysRemaining <= 7 && $progress >= 50) {
                $alerts[] = [
                    'type' => 'time_warning',
                    'message' => "Budget for {$budget->category->name} has {$daysRemaining} days remaining and is at " . round($progress) . "%",
                    'budget_id' => $budget->id,
                    'severity' => 'low',
                ];
            }
        }

        return $alerts;
    }

    public function getBudgetProgress(string $budgetId): array
    {
        $budget = Budget::find($budgetId);

        if (!$budget) {
            throw new \Exception('Budget not found');
        }

        $dailySpending = DB::table('transactions')
            ->where('user_id', $budget->user_id)
            ->where('category_id', $budget->category_id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$budget->start_date, $budget->end_date])
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(amount) as daily_spent')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dailyBudget = $budget->amount / $budget->start_date->diffInDays($budget->end_date);

        $cumulativeData = [];
        $runningSpent = 0;
        $runningBudget = 0;

        $currentDate = $budget->start_date->copy();
        while ($currentDate <= $budget->end_date) {
            $dateStr = $currentDate->format('Y-m-d');
            $daySpent = $dailySpending->where('date', $dateStr)->sum('daily_spent');

            $runningSpent += $daySpent;
            $runningBudget += $dailyBudget;

            $cumulativeData[] = [
                'date' => $dateStr,
                'daily_spent' => (float) $daySpent,
                'daily_budget' => (float) $dailyBudget,
                'cumulative_spent' => (float) $runningSpent,
                'cumulative_budget' => (float) $runningBudget,
                'variance' => (float) ($runningBudget - $runningSpent),
            ];

            $currentDate->addDay();
        }

        return [
            'budget' => $budget,
            'progress' => [
                'spent_amount' => $budget->spent_amount,
                'remaining_amount' => $budget->remaining_amount,
                'progress_percentage' => $budget->progress_percentage,
                'is_exceeded' => $budget->is_exceeded,
                'days_remaining' => $budget->days_remaining,
            ],
            'daily_breakdown' => $cumulativeData,
        ];
    }
}
