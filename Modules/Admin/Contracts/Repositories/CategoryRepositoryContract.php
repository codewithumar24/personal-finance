<?php
namespace Modules\Admin\Contracts\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Admin\Entities\Category;
use Modules\Admin\Enum\CategoryType;

interface CategoryRepositoryContract
{
    public function create( ?string $userId,
                            string $name,
                            CategoryType $type,
                            ?string $color,
                            ?string $icon,
                            bool $isDefault,
                            bool $isActive,
                            ?int $sortOrder,
                            ?string $description): Category;
    public function findById(string $id): ?Category;
    public function findByUuid(string $uuid): ?Category;
    public function getUserCategories(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator;
    public function getUserCategoryByUuid(string $userId, string $uuid): ?Category;
    public function update(Category $category,
                           ?string $name,
                           ?CategoryType $type,
                           ?string $color,
                           ?string $icon,
                           ?bool $isActive,
                           ?int $sortOrder,
                           ?string $description): Category;
    public function delete(Category $category): bool;
    public function getDefaultCategories(CategoryType $type): Collection;
    public function getUserCategoriesByType(string $userId, CategoryType $type): Collection;
}
