<?php
// modules/Admin/Http/Controllers/AdminUserController.php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Admin\Contracts\Services\AdminUserServiceContract;
use Modules\Admin\Http\Requests\AssignRoleRequest;
use Modules\Admin\Http\Requests\CreateUserRequest;
use Modules\Admin\Http\Requests\UpdateUserRequest;
use Modules\Admin\Http\Requests\UserFilterRequest;
use Modules\User\Transformers\UserTransformer;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly AdminUserServiceContract $adminUserService
    ) {}

    public function index(UserFilterRequest $request): JsonResponse
    {
        $users = $this->adminUserService->getAllUsers(
            $request->getFilters(),
            $request->getPerPage()
        );

        return apiResponse()
            ->pagination($users)
            ->success(UserTransformer::collection($users));
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        $user = $this->adminUserService->createUser($request->validated());

        return apiResponse()
            ->success(new UserTransformer($user), 'User created successfully')
            ->setStatusCode(201);
    }

    public function show(string $uuid): JsonResponse
    {
        $user = $this->adminUserService->getUserByUuid($uuid);

        if (!$user) {
            return apiResponse()->notFound('User not found');
        }

        return apiResponse()->success(new UserTransformer($user));
    }

    public function update(UpdateUserRequest $request, string $uuid): JsonResponse
    {
        $user = $this->adminUserService->getUserByUuid($uuid);

        if (!$user) {
            return apiResponse()->notFound('User not found');
        }

        $updatedUser = $this->adminUserService->updateUser($user, $request->getDTO());

        return apiResponse()
            ->success(new UserTransformer($updatedUser), 'User updated successfully');
    }

    public function destroy(string $uuid): JsonResponse
    {
        $user = $this->adminUserService->getUserByUuid($uuid);

        if (!$user) {
            return apiResponse()->notFound('User not found');
        }

        $this->adminUserService->deleteUser($user);

        return apiResponse()->success(null, 'User deleted successfully');
    }

    public function assignRoles(AssignRoleRequest $request, string $uuid): JsonResponse
    {
        $user = $this->adminUserService->getUserByUuid($uuid);

        if (!$user) {
            return apiResponse()->notFound('User not found');
        }

        // Remove existing roles
        $user->roles()->detach();

        // Assign new roles
        foreach ($request->getDTO()->getRoles() as $roleId) {
            $role = \Modules\Core\Entities\Role::find($roleId);
            if ($role) {
                $this->adminUserService->assignRole($user, $role->name);
            }
        }

        $user->load('roles');

        return apiResponse()
            ->success(new UserTransformer($user), 'Roles assigned successfully');
    }

    public function activate(string $uuid): JsonResponse
    {
        $user = $this->adminUserService->getUserByUuid($uuid);

        if (!$user) {
            return apiResponse()->notFound('User not found');
        }

        $user = $this->adminUserService->activateUser($user);

        return apiResponse()
            ->success(new UserTransformer($user), 'User activated successfully');
    }

    public function deactivate(string $uuid): JsonResponse
    {
        $user = $this->adminUserService->getUserByUuid($uuid);

        if (!$user) {
            return apiResponse()->notFound('User not found');
        }

        $user = $this->adminUserService->deactivateUser($user);

        return apiResponse()
            ->success(new UserTransformer($user), 'User deactivated successfully');
    }
}
