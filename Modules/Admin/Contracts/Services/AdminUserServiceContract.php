<?php
// modules/Admin/Contracts/Services/AdminUserServiceContract.php

namespace Modules\Admin\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\User\DataTransfer\Requests\UpdateProfileDTO;
use Modules\User\Entities\User;

interface AdminUserServiceContract
{
    public function getAllUsers(array $filters = [], int|null $perPage = 15): Collection|LengthAwarePaginator;
    public function getUserByUuid(string $uuid): ?User;
    public function createUser(array $data): User;
    public function updateUser(User $user, UpdateProfileDTO $dto): User;
    public function deleteUser(User $user): bool;
    public function assignRole(User $user, string $role): User;
    public function removeRole(User $user, string $role): User;
    public function activateUser(User $user): User;
    public function deactivateUser(User $user): User;
    public function getDashboardStats(): array;
}