<?php
// modules/Admin/Services/AdminRoleService.php

namespace Modules\Admin\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Contracts\Services\AdminRoleServiceContract;
use Modules\Core\Contracts\Services\RoleServiceContract;
use Modules\Core\DataTransfer\Requests\RoleDTO;
use Modules\Core\Entities\Permission;
use Modules\Core\Entities\Role;
use Modules\User\Entities\User;

class AdminRoleService implements AdminRoleServiceContract
{
    public function __construct(
        private readonly RoleServiceContract $roleService
    ) {}

    public function getAllRoles(int|null $perPage = 15): Collection|LengthAwarePaginator
    {
        $query = Role::with(['permissions']);

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getRoleByUuid(string $uuid): ?Role
    {
        return Role::where('role_uuid', $uuid)->with(['permissions'])->first();
    }

    public function createRole(RoleDTO $dto): Role
    {
        return $this->roleService->create($dto);
    }

    public function updateRole(Role $role, RoleDTO $dto): Role
    {
        return $this->roleService->update($role, $dto);
    }

    public function deleteRole(Role $role): bool
    {
        // Check if role has users
        $userCount = DB::table('user_roles')->where('role_id', $role->id)->count();
        
        if ($userCount > 0) {
            throw new \Exception("Cannot delete role that has users assigned. Please reassign users first.");
        }

        return $this->roleService->delete($role);
    }

    public function assignPermission(Role $role, string $permission): Role
    {
        $permissionModel = Permission::where('name', $permission)->first();
        
        if (!$permissionModel) {
            throw new \Exception("Permission not found: {$permission}");
        }

        $role->permissions()->syncWithoutDetaching([$permissionModel->id]);
        
        return $role->load('permissions');
    }

    public function removePermission(Role $role, string $permission): Role
    {
        $permissionModel = Permission::where('name', $permission)->first();
        
        if ($permissionModel) {
            $role->permissions()->detach($permissionModel->id);
        }

        return $role->load('permissions');
    }

    public function getRoleUsers(Role $role, int|null $perPage = 15): Collection|LengthAwarePaginator
    {
        $query = User::whereHas('roles', function ($q) use ($role) {
            $q->where('roles.id', $role->id);
        })->with(['roles']);

        return $perPage ? $query->paginate($perPage) : $query->get();
    }
}