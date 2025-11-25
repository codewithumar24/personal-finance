<?php
// modules/User/Http/Controllers/ProfileController.php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\User\Http\Requests\UpdateProfileRequest;
use Modules\User\Contracts\Services\UserServiceContract;
use Modules\User\Transformers\UserTransformer;

class ProfileController extends Controller
{
    public function __construct(
        private readonly UserServiceContract $userService
    ) {}

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->userService->updateProfile($request->getDTO());
        
        return apiResponse()
            ->success(new UserTransformer($user), 'Profile updated successfully');
    }
}