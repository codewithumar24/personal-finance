<?php
// modules/Admin/Contracts/Services/AdminRoleServiceContract.php

namespace Modules\Admin\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Core\DataTransfer\Requests\RoleDTO;
use Modules\Core\Entities\Role;

interface AdminRoleServiceContract
{
    public function getAllRoles(int|null $perPage = 15): Collection|LengthAwarePaginator;
    public function getRoleByUuid(string $uuid): ?Role;
    public function createRole(RoleDTO $dto): Role;
    public function updateRole(Role $role, RoleDTO $dto): Role;
    public function deleteRole(Role $role): bool;
    public function assignPermission(Role $role, string $permission): Role;
    public function removePermission(Role $role, string $permission): Role;
    public function getRoleUsers(Role $role, int|null $perPage = 15): Collection|LengthAwarePaginator;
}