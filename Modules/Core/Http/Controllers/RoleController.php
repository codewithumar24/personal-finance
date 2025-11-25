<?php
// modules/Core/Http/Controllers/RoleController.php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Requests\RoleRequest;
use Modules\Core\Http\Requests\RoleUpdateRequest;
use Modules\Core\Contracts\Services\RoleServiceContract;
use Modules\Core\Transformers\RoleTransformer;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleServiceContract $roleService
    ) {}

    public function index(): JsonResponse
    {
        $roles = $this->roleService->getAll(request()->get('per_page'));
        return apiResponse()->success(RoleTransformer::collection($roles));
    }

    public function store(RoleRequest $request): JsonResponse
    {
        $role = $this->roleService->create($request->getDTO());
        return apiResponse()->success(new RoleTransformer($role), 'Role created successfully');
    }

    public function show(string $uuid): JsonResponse
    {
        $role = $this->roleService->findByUuid($uuid);
        
        if (!$role) {
            return apiResponse()->notFound('Role not found');
        }

        return apiResponse()->success(new RoleTransformer($role));
    }

    public function update(RoleUpdateRequest $request, string $uuid): JsonResponse
    {
        $role = $this->roleService->findByUuid($uuid);
        
        if (!$role) {
            return apiResponse()->notFound('Role not found');
        }

        $updatedRole = $this->roleService->update($role, $request->getDTO());
        return apiResponse()->success(new RoleTransformer($updatedRole), 'Role updated successfully');
    }

    public function destroy(string $uuid): JsonResponse
    {
        $role = $this->roleService->findByUuid($uuid);
        
        if (!$role) {
            return apiResponse()->notFound('Role not found');
        }

        $this->roleService->delete($role);
        return apiResponse()->success(null, 'Role deleted successfully');
    }
}