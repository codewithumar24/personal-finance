<?php
// modules/Finance/Repositories/CategoryRepository.php

namespace Modules\Admin\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Admin\Contracts\Repositories\CategoryRepositoryContract;
use Modules\Admin\Entities\Category;
use Modules\Admin\Enum\CategoryType;

class CategoryRepository implements CategoryRepositoryContract
{
    public function __construct(private readonly Category $model) {}

    public function create( ?string $userId,
                            string $name,
                            CategoryType $type,
                            ?string $color,
                            ?string $icon,
                            bool $isDefault,
                            bool $isActive,
                            ?int $sortOrder,
                            ?string $description): Category
    {
        $objQuery = $this->model->newQuery();

        return $objQuery->create([
            'category_uuid' => Str::uuid(),
            'user_id' => $userId,
            'name' => $name,
            'type' => $type->value,
            'color' => $color ?? '#6B7280',
            'icon' => $icon,
            'is_default' => $isDefault,
            'is_active' => $isActive,
            'sort_order' => $sortOrder ?? 0,
            'description' => $description,
        ]);
    }

    public function findById(string $id): ?Category
    {
        return $this->model->find($id);
    }

    public function findByUuid(string $uuid): ?Category
    {
        return $this->model->where('category_uuid', $uuid)->first();
    }

    public function getUserCategories(string $userId, array $filters = [], int|null $perPage = null): Collection|LengthAwarePaginator
    {
        $query = $this->model->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('is_default', true);
        });

        // Apply filters
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        $query->orderBy('sort_order', 'asc')
              ->orderBy('name', 'asc');

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getUserCategoryByUuid(string $userId, string $uuid): ?Category
    {
        return $this->model->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('is_default', true);
        })->where('category_uuid', $uuid)->first();
    }
    public function update(
        Category $category,
        ?string $name,
        ?CategoryType $type,
        ?string $color,
        ?string $icon,
        ?bool $isActive,
        ?int $sortOrder,
        ?string $description
    ): Category {
        if (is_string($name) && $category->name !== $name) {
            $category->name = $name;
        }
        if ($type instanceof CategoryType && $category->type !== $type->value) {
            $category->type = $type->value;
        }
        if (!is_null($color) && $category->color !== $color) {
            $category->color = $color;
        }
        if (!is_null($icon) && $category->icon !== $icon) {
            $category->icon = $icon;
        }
        if (is_bool($isActive) && $category->is_active !== $isActive) {
            $category->is_active = $isActive;
        }
        if (!is_null($sortOrder) && $category->sort_order !== $sortOrder) {
            $category->sort_order = $sortOrder;
        }
        if (!is_null($description) && $category->description !== $description) {
            $category->description = $description;
        }

        $category->save();
        return $category->fresh();
    }

    public function delete(Category $category): bool
    {
        // Check if category has transactions
        if ($category->transactions()->exists()) {
            throw new \Exception('Cannot delete category that has transactions.');
        }

        // Cannot delete default categories
        if ($category->is_default) {
            throw new \Exception('Cannot delete default category.');
        }

        return $category->delete();
    }

    public function getDefaultCategories(CategoryType $type): Collection
    {
        return $this->model->where('is_default', true)
            ->where('type', $type)
            ->whereNull('user_id')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getUserCategoriesByType(string $userId, CategoryType $type): Collection
    {
        return $this->model->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('is_default', true);
        })->where('type', $type)
          ->where('is_active', true)
          ->orderBy('sort_order', 'asc')
          ->orderBy('name', 'asc')
          ->get();
    }
}
