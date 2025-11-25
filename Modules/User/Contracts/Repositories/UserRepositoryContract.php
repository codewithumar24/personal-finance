<?php
// modules/User/Contracts/Repositories/UserRepositoryContract.php

namespace Modules\User\Contracts\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\User\Entities\User;

interface UserRepositoryContract
{
    public function create(array $data): User;
    public function findById(string $id): ?User;
    public function findByUuid(string $uuid): ?User;
    public function findByEmail(string $email): ?User;
    public function getAll(int|null $perPage): Collection|LengthAwarePaginator;
    public function update(User $user, array $data): User;
    public function delete(User $user): bool;
    public function updateLastLogin(User $user): void;
    public function assignRole(User $user, string $role): void;
}