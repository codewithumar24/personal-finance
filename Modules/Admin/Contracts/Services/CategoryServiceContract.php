<?php
// modules/Finance/Contracts/Services/CategoryServiceContract.php

namespace Modules\Admin\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Admin\DataTransfer\Requests\CategoryDTO;
use Modules\Admin\Entities\Category;
use Modules\Admin\Enum\CategoryType;

interface CategoryServiceContract
{
    public function createCategory(CategoryDTO $dto, string $userId): Category;
    public function getUserCategories(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator;
    public function getUserCategoryByUuid(string $userId, string $uuid): ?Category;
    public function updateCategory(Category $category, CategoryDTO $dto): Category;
    public function deleteCategory(Category $category): bool;
    public function getCategoriesByType(string $userId, CategoryType $type): Collection;
}