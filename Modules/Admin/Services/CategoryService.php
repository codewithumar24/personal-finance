<?php
// modules/Finance/Services/CategoryService.php

namespace Modules\Admin\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Admin\Contracts\Repositories\CategoryRepositoryContract;
use Modules\Admin\Contracts\Services\CategoryServiceContract;
use Modules\Admin\DataTransfer\Requests\CategoryDTO;
use Modules\Admin\Entities\Category;
use Modules\Admin\Enum\CategoryType;

class CategoryService implements CategoryServiceContract
{
    public function __construct(
        private readonly CategoryRepositoryContract $categoryRepository
    ) {}

    public function createCategory(CategoryDTO $dto, string $userId): Category
    {
        return $this->categoryRepository->create(
            $userId,
            $dto->getName(),
            $dto->getType(),
            $dto->getColor(),
            $dto->getIcon(),
            false, // is_default - user created categories are not default
            $dto->getIsActive() ?? true,
            $dto->getSortOrder(),
            $dto->getDescription()
        );
    }

    public function getUserCategories(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator
    {
        return $this->categoryRepository->getUserCategories($userId, $filters, $perPage);
    }

    public function getUserCategoryByUuid(string $userId, string $uuid): ?Category
    {
        return $this->categoryRepository->getUserCategoryByUuid($userId, $uuid);
    }

    public function updateCategory(Category $category, CategoryDTO $dto): Category
    {
        return $this->categoryRepository->update(
            $category,
            $dto->getName(),
            $dto->getType(),
            $dto->getColor(),
            $dto->getIcon(),
            $dto->getIsActive(),
            $dto->getSortOrder(),
            $dto->getDescription()
        );
    }

    public function deleteCategory(Category $category): bool
    {
        return $this->categoryRepository->delete($category);
    }

    public function getCategoriesByType(string $userId, CategoryType $type): Collection
    {
        return $this->categoryRepository->getUserCategoriesByType($userId, $type);
    }
}
