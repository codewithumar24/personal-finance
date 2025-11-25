<?php
// modules/Admin/Http/Controllers/AdminRoleController.php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Admin\Contracts\Services\AdminRoleServiceContract;
use Modules\Core\Http\Requests\RoleRequest;
use Modules\Core\Http\Requests\RoleUpdateRequest;
use Modules\Core\Transformers\RoleTransformer;
use Modules\User\Transformers\UserTransformer;

class AdminRoleController extends Controller
{
    public function __construct(
        private readonly AdminRoleServiceContract $adminRoleService
    ) {}

    public function index(): JsonResponse
    {
        $roles = $this->adminRoleService->getAllRoles(request()->get('per_page', 15));

        return apiResponse()
            ->pagination($roles)
            ->success(RoleTransformer::collection($roles));
    }

    public function store(RoleRequest $request): JsonResponse
    {
        $role = $this->adminRoleService->createRole($request->getDTO());

        return apiResponse()
            ->success(new RoleTransformer($role), 'Role created successfully')
            ->setStatusCode(201);
    }

    public function show(string $uuid): JsonResponse
    {
        $role = $this->adminRoleService->getRoleByUuid($uuid);

        if (!$role) {
            return apiResponse()->notFound('Role not found');
        }

        return apiResponse()->success(new RoleTransformer($role));
    }

    public function update(RoleUpdateRequest $request, string $uuid): JsonResponse
    {
        $role = $this->adminRoleService->getRoleByUuid($uuid);

        if (!$role) {
            return apiResponse()->notFound('Role not found');
        }

        $updatedRole = $this->adminRoleService->updateRole($role, $request->getDTO());

        return apiResponse()
            ->success(new RoleTransformer($updatedRole), 'Role updated successfully');
    }

    public function destroy(string $uuid): JsonResponse
    {
        $role = $this->adminRoleService->getRoleByUuid($uuid);

        if (!$role) {
            return apiResponse()->notFound('Role not found');
        }

        try {
            $this->adminRoleService->deleteRole($role);
            
            return apiResponse()->success(null, 'Role deleted successfully');
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function roleUsers(string $uuid): JsonResponse
    {
        $role = $this->adminRoleService->getRoleByUuid($uuid);

        if (!$role) {
            return apiResponse()->notFound('Role not found');
        }

        $users = $this->adminRoleService->getRoleUsers($role, request()->get('per_page', 15));

        return apiResponse()
            ->pagination($users)
            ->success(UserTransformer::collection($users));
    }

    public function assignPermission(string $uuid, string $permission): JsonResponse
    {
        $role = $this->adminRoleService->getRoleByUuid($uuid);

        if (!$role) {
            return apiResponse()->notFound('Role not found');
        }

        try {
            $role = $this->adminRoleService->assignPermission($role, $permission);

            return apiResponse()
                ->success(new RoleTransformer($role), 'Permission assigned successfully');
        } catch (\Exception $e) {
            return apiResponse()->error($e->getMessage(), 400);
        }
    }

    public function removePermission(string $uuid, string $permission): JsonResponse
    {
        $role = $this->adminRoleService->getRoleByUuid($uuid);

        if (!$role) {
            return apiResponse()->notFound('Role not found');
        }

        $role = $this->adminRoleService->removePermission($role, $permission);

        return apiResponse()
            ->success(new RoleTransformer($role), 'Permission removed successfully');
    }
}