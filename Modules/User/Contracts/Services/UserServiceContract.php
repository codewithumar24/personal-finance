<?php
// modules/User/Contracts/Services/UserServiceContract.php

namespace Modules\User\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\User\DataTransfer\Requests\LoginDTO;
use Modules\User\DataTransfer\Requests\RegisterDTO;
use Modules\User\DataTransfer\Requests\UpdateProfileDTO;
use Modules\User\Entities\User;

interface UserServiceContract
{
    public function register(RegisterDTO $dto): User;
    public function login(LoginDTO $dto): array;
    public function logout(): void;
    public function getProfile(): User;
    public function updateProfile(UpdateProfileDTO $dto): User;
    public function getAllUsers(int|null $perPage): Collection|LengthAwarePaginator;
    public function findByUuid(string $uuid): ?User;
    public function updateUser(User $user, UpdateProfileDTO $dto): User;
    public function deleteUser(User $user): bool;
}