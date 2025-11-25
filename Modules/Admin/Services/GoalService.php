<?php
// modules/Finance/Services/GoalService.php

namespace Modules\Admin\Services;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Admin\Contracts\Repositories\GoalRepositoryContract;
use Modules\Admin\Contracts\Services\GoalServiceContract;
use Modules\Admin\DataTransfer\Requests\GoalDTO;
use Modules\Admin\Entities\Goal;

class GoalService implements GoalServiceContract
{
    public function __construct(
        private readonly GoalRepositoryContract $goalRepository
    ) {}

    public function createGoal(GoalDTO $dto, string $userId): Goal
    {
        return $this->goalRepository->create(
            $userId,
            $dto->getName(),
            $dto->getTargetAmount(),
            $dto->getTargetDate(),
            $dto->getType(),
            $dto->getColor(),
            $dto->getIcon(),
            $dto->getDescription(),
            $dto->getIsActive() ?? true
        );
    }

    public function getUserGoals(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator
    {
        return $this->goalRepository->getUserGoals($userId, $filters, $perPage);
    }

    public function getUserGoalByUuid(string $userId, string $uuid): ?Goal
    {
        return $this->goalRepository->getUserGoalByUuid($userId, $uuid);
    }

    public function updateGoal(Goal $goal, GoalDTO $dto): Goal
    {
        return $this->goalRepository->update(
            $goal,
            $dto->getName(),
            $dto->getTargetAmount(),
            $dto->getTargetDate(),
            $dto->getType(),
            $dto->getColor(),
            $dto->getIcon(),
            $dto->getDescription(),
            $dto->getIsActive()
        );

    }

    public function deleteGoal(Goal $goal): bool
    {
        return $this->goalRepository->delete($goal);
    }

    public function addToGoal(Goal $goal, float $amount): Goal
    {
        if ($amount <= 0) {
            throw new \Exception('Amount must be positive');
        }

        $goal->addAmount($amount);
        return $goal->fresh();
    }

    public function getGoalProgress(string $goalId): array
    {
        $goal = Goal::find($goalId);

        if (!$goal) {
            throw new \Exception('Goal not found');
        }

        $daysPassed = $goal->created_at->diffInDays(now());
        $totalDays = $goal->created_at->diffInDays($goal->target_date);
        $daysRemaining = $goal->days_remaining;

        $expectedProgress = $totalDays > 0 ? min(100, ($daysPassed / $totalDays) * 100) : 100;
        $actualProgress = $goal->progress_percentage;

        $status = match(true) {
            $goal->is_completed => 'completed',
            $actualProgress >= $expectedProgress => 'on_track',
            $actualProgress >= $expectedProgress * 0.7 => 'slightly_behind',
            default => 'behind',
        };

        $monthlyTarget = $totalDays > 0 ? $goal->target_amount / ($totalDays / 30) : $goal->target_amount;
        $requiredMonthly = $daysRemaining > 0 ?
            ($goal->target_amount - $goal->current_amount) / ($daysRemaining / 30) : 0;

        return [
            'goal' => $goal,
            'progress' => [
                'current_amount' => $goal->current_amount,
                'target_amount' => $goal->target_amount,
                'progress_percentage' => $goal->progress_percentage,
                'days_remaining' => $daysRemaining,
                'is_completed' => $goal->is_completed,
                'status' => $status,
            ],
            'targets' => [
                'monthly_target' => $monthlyTarget,
                'required_monthly' => $requiredMonthly,
                'expected_progress' => $expectedProgress,
                'actual_progress' => $actualProgress,
            ],
        ];
    }

    public function checkGoalAlerts(string $userId): array
    {
        $alerts = [];
        $goals = Goal::where('user_id', $userId)
            ->where('is_active', true)
            ->where('is_completed', false)
            ->get();

        foreach ($goals as $goal) {
            $progress = $this->getGoalProgress($goal->id);
            $daysRemaining = $goal->days_remaining;

            // Alert for nearing target date with low progress
            if ($daysRemaining <= 30 && $goal->progress_percentage < 50) {
                $alerts[] = [
                    'type' => 'goal_deadline',
                    'message' => "Goal '{$goal->name}' has {$daysRemaining} days remaining but is only at " . round($goal->progress_percentage) . "%",
                    'goal_id' => $goal->id,
                    'severity' => 'high',
                ];
            }
            // Alert for milestone achievement (25%, 50%, 75%)
            elseif (in_array(round($goal->progress_percentage), [25, 50, 75])) {
                $alerts[] = [
                    'type' => 'goal_milestone',
                    'message' => "Congratulations! Goal '{$goal->name}' has reached " . round($goal->progress_percentage) . "%",
                    'goal_id' => $goal->id,
                    'severity' => 'low',
                ];
            }
            // Alert for behind schedule
            elseif ($progress['progress']['status'] === 'behind') {
                $alerts[] = [
                    'type' => 'goal_behind',
                    'message' => "Goal '{$goal->name}' is behind schedule. Current progress: " . round($goal->progress_percentage) . "%",
                    'goal_id' => $goal->id,
                    'severity' => 'medium',
                ];
            }
        }

        return $alerts;
    }
    // Add this method to the existing GoalService
public function checkAndSendGoalAlerts(string $userId): array
{
    $alerts = $this->checkGoalAlerts($userId);

    foreach ($alerts as $alert) {
        $this->notificationService->sendGoalNotification(
            $userId,
            $alert['type'],
            [
                'goal_name' => $alert['message'],
                'progress_percentage' => $alert['goal']->progress_percentage ?? 0,
                'current_amount' => $alert['goal']->current_amount ?? 0,
                'target_amount' => $alert['goal']->target_amount ?? 0,
                'goal_id' => $alert['goal_id'],
            ]
        );
    }

    return $alerts;
}
}
