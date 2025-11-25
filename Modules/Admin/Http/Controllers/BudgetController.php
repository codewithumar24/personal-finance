<?php
// modules/Finance/Http/Controllers/BudgetController.php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Http\Requests\FilterRequest;
use Modules\Admin\Contracts\Services\BudgetServiceContract;
use Modules\Admin\Http\Requests\BudgetRequest;
use Modules\Admin\Transformers\BudgetTransformer;

class BudgetController extends Controller
{
    public function __construct(
        private readonly BudgetServiceContract $budgetService
    ) {}

    public function index(FilterRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $filters = $request->only(['is_active', 'category_id']);

        $budgets = $this->budgetService->getUserBudgets($userId, $filters, $request->getPerPage());

        return apiResponse()
            ->pagination($budgets)
            ->success(BudgetTransformer::collection($budgets));
    }

    public function store(BudgetRequest $request): JsonResponse
    {
        $userId = Auth::id();

        try {
            $budget = $this->budgetService->createBudget($request->getDTO(), $userId);

            return apiResponse()
                ->success(new BudgetTransformer($budget), 'Budget created successfully')
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function show(string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $budget = $this->budgetService->getUserBudgetByUuid($userId, $uuid);

        if (!$budget) {
            return apiResponse()->notFound('Budget not found');
        }

        return apiResponse()->success(new BudgetTransformer($budget));
    }

    public function update(BudgetRequest $request, string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $budget = $this->budgetService->getUserBudgetByUuid($userId, $uuid);

        if (!$budget) {
            return apiResponse()->notFound('Budget not found');
        }

        try {
            $updatedBudget = $this->budgetService->updateBudget($budget, $request->getDTO());

            return apiResponse()
                ->success(new BudgetTransformer($updatedBudget), 'Budget updated successfully');
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function destroy(string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $budget = $this->budgetService->getUserBudgetByUuid($userId, $uuid);

        if (!$budget) {
            return apiResponse()->notFound('Budget not found');
        }

        try {
            $this->budgetService->deleteBudget($budget);

            return apiResponse()->success(null, 'Budget deleted successfully');
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function current(): JsonResponse
    {
        $userId = Auth::id();
        $budgets = $this->budgetService->getCurrentBudgets($userId);

        return apiResponse()->success(BudgetTransformer::collection($budgets));
    }

    public function alerts(): JsonResponse
    {
        $userId = Auth::id();
        $alerts = $this->budgetService->checkBudgetAlerts($userId);

        return apiResponse()->success($alerts);
    }

    public function progress(string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $budget = $this->budgetService->getUserBudgetByUuid($userId, $uuid);

        if (!$budget) {
            return apiResponse()->notFound('Budget not found');
        }

        try {
            $progress = $this->budgetService->getBudgetProgress($budget->id);
            return apiResponse()->success($progress);
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }
}
