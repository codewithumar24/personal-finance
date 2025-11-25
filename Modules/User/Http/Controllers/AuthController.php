<?php
// modules/User/Http/Controllers/AuthController.php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Modules\User\Http\Requests\ForgotPasswordRequest;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Http\Requests\RegisterRequest;
use Modules\User\Http\Requests\ResetPasswordRequest;
use Modules\User\Contracts\Services\UserServiceContract;
use Modules\User\Transformers\UserTransformer;

class AuthController extends Controller
{
    public function __construct(
        private readonly UserServiceContract $userService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->userService->register($request->getDTO());
        
        return apiResponse()
            ->success(new UserTransformer($user), 'Registration successful. Please check your email for verification.')
            ->setStatusCode(201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->userService->login($request->getDTO());
        
        return apiResponse()
            ->success([
                'user' => new UserTransformer($result['user']),
                'token' => $result['token'],
            ], 'Login successful');
    }

    public function logout(): JsonResponse
    {
        $this->userService->logout();
        
        return apiResponse()
            ->success(null, 'Logout successful');
    }

    public function profile(): JsonResponse
    {
        $user = $this->userService->getProfile();
        
        return apiResponse()
            ->success(new UserTransformer($user));
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = $this->userService->forgotPassword($request->input('email'));
        
        if ($status === Password::RESET_LINK_SENT) {
            return apiResponse()
                ->success(null, 'Password reset link sent to your email');
        }

        return apiResponse()
            ->error('Unable to send reset link', 400);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->userService->resetPassword($request->only(
            'email', 'password', 'password_confirmation', 'token'
        ));

        if ($status === Password::PASSWORD_RESET) {
            return apiResponse()
                ->success(null, 'Password reset successfully');
        }

        return apiResponse()
            ->error('Unable to reset password', 400);
    }
}