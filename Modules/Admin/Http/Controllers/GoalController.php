<?php
// modules/Finance/Http/Controllers/GoalController.php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Http\Requests\FilterRequest;
use Modules\Admin\Contracts\Services\GoalServiceContract;
use Modules\Admin\Http\Requests\GoalRequest;
use Modules\Admin\Transformers\GoalTransformer;

class GoalController extends Controller
{
    public function __construct(
        private readonly GoalServiceContract $goalService
    ) {}

    public function index(FilterRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $filters = $request->only(['type', 'is_completed', 'is_active']);

        $goals = $this->goalService->getUserGoals($userId, $filters, $request->getPerPage());

        return apiResponse()
            ->pagination($goals)
            ->success(GoalTransformer::collection($goals));
    }

    public function store(GoalRequest $request): JsonResponse
    {
        $userId = Auth::id();

        try {
            $goal = $this->goalService->createGoal($request->getDTO(), $userId);

            return apiResponse()
                ->success(new GoalTransformer($goal), 'Goal created successfully')
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function show(string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $goal = $this->goalService->getUserGoalByUuid($userId, $uuid);

        if (!$goal) {
            return apiResponse()->notFound('Goal not found');
        }

        return apiResponse()->success(new GoalTransformer($goal));
    }

    public function update(GoalRequest $request, string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $goal = $this->goalService->getUserGoalByUuid($userId, $uuid);

        if (!$goal) {
            return apiResponse()->notFound('Goal not found');
        }

        try {
            $updatedGoal = $this->goalService->updateGoal($goal, $request->getDTO());

            return apiResponse()
                ->success(new GoalTransformer($updatedGoal), 'Goal updated successfully');
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function destroy(string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $goal = $this->goalService->getUserGoalByUuid($userId, $uuid);

        if (!$goal) {
            return apiResponse()->notFound('Goal not found');
        }

        try {
            $this->goalService->deleteGoal($goal);

            return apiResponse()->success(null, 'Goal deleted successfully');
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function addAmount(string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $goal = $this->goalService->getUserGoalByUuid($userId, $uuid);

        if (!$goal) {
            return apiResponse()->notFound('Goal not found');
        }

        $request = request()->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        try {
            $updatedGoal = $this->goalService->addToGoal($goal, $request['amount']);

            return apiResponse()
                ->success(new GoalTransformer($updatedGoal), 'Amount added to goal successfully');
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function progress(string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $goal = $this->goalService->getUserGoalByUuid($userId, $uuid);

        if (!$goal) {
            return apiResponse()->notFound('Goal not found');
        }

        try {
            $progress = $this->goalService->getGoalProgress($goal->id);
            return apiResponse()->success($progress);
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function alerts(): JsonResponse
    {
        $userId = Auth::id();
        $alerts = $this->goalService->checkGoalAlerts($userId);

        return apiResponse()->success($alerts);
    }
}
