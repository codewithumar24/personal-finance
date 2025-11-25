<?php
// modules/User/Http/Requests/ForgotPasswordRequest.php

namespace Modules\User\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class ForgotPasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required',
            'email.exists' => 'No account found with this email address',
        ];
    }
}