<?php
// modules/User/Services/UserService.php

namespace Modules\User\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Modules\Core\Entities\Role;
use Modules\User\Contracts\Repositories\UserRepositoryContract;
use Modules\User\Contracts\Services\UserServiceContract;
use Modules\User\DataTransfer\Requests\LoginDTO;
use Modules\User\DataTransfer\Requests\RegisterDTO;
use Modules\User\DataTransfer\Requests\UpdateProfileDTO;
use Modules\User\Entities\User;
use Modules\User\Notifications\CustomResetPassword;
use Modules\User\Notifications\RegistrationNotification;

class UserService implements UserServiceContract
{
    public function __construct(
        private readonly UserRepositoryContract $userRepository
    ) {}

    public function register(RegisterDTO $dto): User
    {
        $user = $this->userRepository->create([
            'first_name' => $dto->getFirstName(),
            'last_name' => $dto->getLastName(),
            'email' => $dto->getEmail(),
            'password' => Hash::make($dto->getPassword()),
            'phone' => $dto->getPhone(),
            'date_of_birth' => $dto->getDateOfBirth(),
            'address' => $dto->getAddress(),
            'email_verified_at' => now(), // Mark email as verified immediately
        ]);

        // Assign default user role
        $defaultRole = Role::where('is_default', true)->first();
        if ($defaultRole) {
            $this->userRepository->assignRole($user, $defaultRole->name);
        }
// Send custom registration email instead of verification
        $user->notify(new RegistrationNotification());
        // Send registration email
//        $user->sendEmailVerificationNotification();

        return $user->load('roles');
    }

    public function login(LoginDTO $dto): array
    {
        $user = $this->userRepository->findByEmail($dto->getEmail());

        if (!$user || !Hash::check($dto->getPassword(), $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        if (!$user->is_active) {
            throw new \Exception('Your account has been deactivated');
        }

        // Update last login
        $this->userRepository->updateLastLogin($user);

        // Create token
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(): void
    {
        Auth::user()->currentAccessToken()->delete();
    }

    public function getProfile(): User
    {
        return Auth::user()->load('roles');
    }

    public function updateProfile(UpdateProfileDTO $dto): User
    {
        $user = Auth::user();
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

        // Handle profile image upload
        if ($dto->hasProfileImage()) {
            $path = $dto->getProfileImage()->store('profiles', 'public');
            $updateData['profile_image'] = $path;
        }

        return $this->userRepository->update($user, $updateData);
    }

    public function getAllUsers(int|null $perPage): Collection|LengthAwarePaginator
    {
        return $this->userRepository->getAll($perPage);
    }

    public function findByUuid(string $uuid): ?User
    {
        return $this->userRepository->findByUuid($uuid);
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

        return $this->userRepository->update($user, $updateData);
    }

    public function deleteUser(User $user): bool
    {
        return $this->userRepository->delete($user);
    }

//    public function forgotPassword(string $email): string
//    {
//        return Password::sendResetLink(['email' => $email]);
//    }

    public function forgotPassword(string $email): string
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return Password::INVALID_USER;
        }

        // Generate reset token
        $token = Password::broker()->createToken($user);

        // Send custom notification directly
        $user->notify(new CustomResetPassword($token));

        return Password::RESET_LINK_SENT;
    }

    public function resetPassword(array $credentials): string
    {
        return Password::reset($credentials, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });
    }
}
