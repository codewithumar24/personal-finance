<?php
// modules/Core/Repositories/RoleRepository.php

namespace Modules\Core\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Core\Contracts\Repositories\RoleRepositoryContract;
use Modules\Core\Entities\Role;

class RoleRepository implements RoleRepositoryContract
{
    public function __construct(private readonly Role $model) {}

    public function create(array $data): Role
    {
        $data['role_uuid'] = Str::uuid();
        return $this->model->create($data);
    }

    public function findById(string $id): ?Role
    {
        return $this->model->find($id);
    }

    public function findByUuid(string $uuid): ?Role
    {
        return $this->model->where('role_uuid', $uuid)->first();
    }

    public function findByName(string $name): ?Role
    {
        return $this->model->where('name', $name)->first();
    }

    public function getAll(int|null $perPage): Collection|LengthAwarePaginator
    {
        if ($perPage) {
            return $this->model->with('permissions')->paginate($perPage);
        }
        
        return $this->model->with('permissions')->get();
    }

    public function update(Role $role, array $data): Role
    {
        $role->update($data);
        return $role->fresh();
    }

    public function delete(Role $role): bool
    {
        return $role->delete();
    }

    public function syncPermissions(Role $role, array $permissions): void
    {
        $role->permissions()->sync($permissions);
    }
}