<?php
// modules/Core/Contracts/Repositories/RoleRepositoryContract.php

namespace Modules\Core\Contracts\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Core\Entities\Role;

interface RoleRepositoryContract
{
    public function create(array $data): Role;
    public function findById(string $id): ?Role;
    public function findByUuid(string $uuid): ?Role;
    public function findByName(string $name): ?Role;
    public function getAll(int|null $perPage): Collection|LengthAwarePaginator;
    public function update(Role $role, array $data): Role;
    public function delete(Role $role): bool;
    public function syncPermissions(Role $role, array $permissions): void;
}