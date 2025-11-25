<?php
// modules/Admin/Services/AdminUserService.php

namespace Modules\Admin\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Admin\Contracts\Services\AdminUserServiceContract;
use Modules\Admin\Notifications\WelcomeNotification;
use Modules\Core\Entities\Role;
use Modules\User\Contracts\Repositories\UserRepositoryContract;
use Modules\User\DataTransfer\Requests\UpdateProfileDTO;
use Modules\User\Entities\User;

class AdminUserService implements AdminUserServiceContract
{
    public function __construct(
        private readonly UserRepositoryContract $userRepository
    ) {}

    public function getAllUsers(array $filters = [], int|null $perPage = 15): Collection|LengthAwarePaginator
    {
        $query = User::with(['roles']);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortBy, $sortOrder);

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function getUserByUuid(string $uuid): ?User
    {
        return $this->userRepository->findByUuid($uuid);
    }

    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepository->create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'address' => $data['address'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Assign roles
            if (!empty($data['roles'])) {
                $roles = Role::whereIn('id', $data['roles'])->get();
                foreach ($roles as $role) {
                    $this->userRepository->assignRole($user, $role->name);
                }
            }

            // Send welcome email if requested
            if ($data['send_welcome_email'] ?? false) {
                // Implement welcome email logic
                $user->notify(new WelcomeNotification($data['password']));
            }

            return $user->load('roles');
        });
    }

    public function updateUser(User $user, UpdateProfileDTO $dto): User
    {
        $updateData = [];

        if ($dto->hasFirstName()) {
            $updateData['first_name'] = $dto->getFirstName();
        }

        if ($dto->hasLastName()) {
            $updateData['last_name'] = $dto->getLastName();
        }

        if ($dto->hasPhone()) {
            $updateData['phone'] = $dto->getPhone();
        }

        if ($dto->hasDateOfBirth()) {
            $updateData['date_of_birth'] = $dto->getDateOfBirth();
        }

        if ($dto->hasAddress()) {
            $updateData['address'] = $dto->getAddress();
        }

        if ($dto->hasProfileImage()) {
            $path = $dto->getProfileImage()->store('profiles', 'public');
            $updateData['profile_image'] = $path;
        }

        // Handle is_active from request if provided
        if (request()->has('is_active')) {
            $updateData['is_active'] = request()->boolean('is_active');
        }

        return $this->userRepository->update($user, $updateData);
    }

    public function deleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            // Delete user roles
            DB::table('user_roles')->where('user_id', $user->id)->delete();

            // Delete user tokens
            $user->tokens()->delete();

            return $this->userRepository->delete($user);
        });
    }

    public function assignRole(User $user, string $role): User
    {
        $this->userRepository->assignRole($user, $role);
        return $user->load('roles');
    }

    public function removeRole(User $user, string $role): User
    {
        $roleModel = Role::where('name', $role)->first();

        if ($roleModel) {
            DB::table('user_roles')
                ->where('user_id', $user->id)
                ->where('role_id', $roleModel->id)
                ->delete();
        }

        return $user->load('roles');
    }

    public function activateUser(User $user): User
    {
        return $this->userRepository->update($user, ['is_active' => true]);
    }

    public function deactivateUser(User $user): User
    {
        return $this->userRepository->update($user, ['is_active' => false]);
    }

    public function getDashboardStats(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $totalAdmins = DB::table('user_roles')
            ->join('roles', 'user_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'admin')
            ->count();

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'new_users_today' => $newUsersToday,
            'total_admins' => $totalAdmins,
            'inactive_users' => $totalUsers - $activeUsers,
        ];
    }
}
