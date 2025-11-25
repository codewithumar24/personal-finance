<?php
// modules/User/Http/Requests/LoginRequest.php

namespace Modules\User\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\User\DataTransfer\Requests\LoginDTO;

class LoginRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember_me' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',
        ];
    }

    public function getDTO(): LoginDTO
    {
        return LoginDTO::create(
            $this->input('email'),
            $this->input('password'),
            $this->boolean('remember_me', false)
        );
    }
}