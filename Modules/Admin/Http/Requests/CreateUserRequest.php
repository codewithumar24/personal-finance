<?php
// modules/Admin/Http/Requests/CreateUserRequest.php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Admin\DataTransfer\Requests\CreateUserDTO;

class CreateUserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
            'is_active' => ['boolean'],
            'send_welcome_email' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'roles.required' => 'At least one role is required',
            'roles.*.exists' => 'Selected role does not exist',
        ];
    }

    public function getDTO(): CreateUserDTO
    {
        return CreateUserDTO::create(
            $this->input('first_name'),
            $this->input('last_name'),
            $this->input('email'),
            $this->input('password'),
            $this->input('phone'),
            $this->input('date_of_birth'),
            $this->input('address'),
            $this->input('roles', []),
            $this->boolean('is_active', true),
            $this->boolean('send_welcome_email', false)
        );
    }
}