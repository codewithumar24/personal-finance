<?php
// modules/Finance/Http/Controllers/CategoryController.php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Http\Requests\FilterRequest;
use Modules\Admin\Contracts\Services\CategoryServiceContract;
use Modules\Admin\Enum\CategoryType;
use Modules\Admin\Http\Requests\CategoryRequest;
use Modules\Admin\Transformers\CategoryTransformer;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryServiceContract $categoryService
    ) {}

    public function index(FilterRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $filters = $request->only(['type', 'search', 'is_active']);
        
        $categories = $this->categoryService->getUserCategories($userId, $filters, $request->getPerPage());

        return apiResponse()
            ->pagination($categories)
            ->success(CategoryTransformer::collection($categories));
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $category = $this->categoryService->createCategory($request->getDTO(), $userId);

        return apiResponse()
            ->success(new CategoryTransformer($category), 'Category created successfully')
            ->setStatusCode(201);
    }

    public function show(string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $category = $this->categoryService->getUserCategoryByUuid($userId, $uuid);

        if (!$category) {
            return apiResponse()->notFound('Category not found');
        }

        return apiResponse()->success(new CategoryTransformer($category));
    }

    public function update(CategoryRequest $request, string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $category = $this->categoryService->getUserCategoryByUuid($userId, $uuid);

        if (!$category) {
            return apiResponse()->notFound('Category not found');
        }

        // Cannot update default categories
        if ($category->is_default && $category->user_id !== $userId) {
            return apiResponse()->error('Cannot update default category', 403);
        }

        $updatedCategory = $this->categoryService->updateCategory($category, $request->getDTO());

        return apiResponse()
            ->success(new CategoryTransformer($updatedCategory), 'Category updated successfully');
    }

    public function destroy(string $uuid): JsonResponse
    {
        $userId = Auth::id();
        $category = $this->categoryService->getUserCategoryByUuid($userId, $uuid);

        if (!$category) {
            return apiResponse()->notFound('Category not found');
        }

        try {
            $this->categoryService->deleteCategory($category);
            
            return apiResponse()->success(null, 'Category deleted successfully');
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function byType(string $type): JsonResponse
    {
        $userId = Auth::id();
        
        try {
            $categoryType = CategoryType::from($type);
            $categories = $this->categoryService->getCategoriesByType($userId, $categoryType);

            return apiResponse()->success(CategoryTransformer::collection($categories));
        } catch (\ValueError $e) {
            return apiResponse()->error('Invalid category type', 400);
        }
    }

    public function types(): JsonResponse
    {
        $types = collect(CategoryType::cases())->map(function ($type) {
            return [
                'value' => $type->value,
                'label' => $type->label(),
            ];
        });

        return apiResponse()->success($types);
    }
}