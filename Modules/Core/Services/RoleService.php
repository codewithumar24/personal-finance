<?php
// modules/Core/Services/RoleService.php

namespace Modules\Core\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Core\Contracts\Repositories\RoleRepositoryContract;
use Modules\Core\Contracts\Services\RoleServiceContract;
use Modules\Core\DataTransfer\Requests\RoleDTO;
use Modules\Core\Entities\Role;

class RoleService implements RoleServiceContract
{
    public function __construct(
        private readonly RoleRepositoryContract $roleRepository
    ) {}

    public function create(RoleDTO $dto): Role
    {
        $role = $this->roleRepository->create([
            'name' => $dto->getName(),
            'description' => $dto->getDescription(),
            'is_default' => $dto->getIsDefault(),
        ]);

        if (!empty($dto->getPermissions())) {
            $this->roleRepository->syncPermissions($role, $dto->getPermissions());
        }

        return $role->load('permissions');
    }

    public function getAll(int|null $perPage): Collection|LengthAwarePaginator
    {
        return $this->roleRepository->getAll($perPage);
    }

    public function findByUuid(string $uuid): ?Role
    {
        return $this->roleRepository->findByUuid($uuid);
    }

    public function update(Role $role, RoleDTO $dto): Role
    {
        $role = $this->roleRepository->update($role, [
            'name' => $dto->getName(),
            'description' => $dto->getDescription(),
            'is_default' => $dto->getIsDefault(),
        ]);

        if (!empty($dto->getPermissions())) {
            $this->roleRepository->syncPermissions($role, $dto->getPermissions());
        }

        return $role->load('permissions');
    }

    public function delete(Role $role): bool
    {
        return $this->roleRepository->delete($role);
    }
}