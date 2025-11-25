<?php
// modules/User/Repositories/UserRepository.php

namespace Modules\User\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Core\Entities\Role;
use Modules\User\Contracts\Repositories\UserRepositoryContract;
use Modules\User\Entities\User;

class UserRepository implements UserRepositoryContract
{
    public function __construct(private readonly User $model) {}

    public function create(array $data): User
    {
        $data['user_uuid'] = Str::uuid();
        return $this->model->create($data);
    }

    public function findById(string $id): ?User
    {
        return $this->model->find($id);
    }

    public function findByUuid(string $uuid): ?User
    {
        return $this->model->where('user_uuid', $uuid)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function getAll(int|null $perPage): Collection|LengthAwarePaginator
    {
        if ($perPage) {
            return $this->model->with('roles')->paginate($perPage);
        }
        
        return $this->model->with('roles')->get();
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh(['roles']);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function updateLastLogin(User $user): void
    {
        $user->update(['last_login_at' => now()]);
    }

    public function assignRole(User $user, string $role): void
    {
        $roleModel = Role::where('name', $role)->first();
        
        if ($roleModel) {
            DB::table('user_roles')->updateOrInsert(
                ['user_id' => $user->id, 'role_id' => $roleModel->id],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}