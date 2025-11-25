<?php
// modules/User/Http/Requests/UpdateProfileRequest.php

namespace Modules\User\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\User\DataTransfer\Requests\UpdateProfileDTO;

class UpdateProfileRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    public function getDTO(): UpdateProfileDTO
    {
        return UpdateProfileDTO::create(
            $this->input('first_name'),
            $this->input('last_name'),
            $this->input('phone'),
            $this->input('date_of_birth'),
            $this->input('address'),
            $this->file('profile_image')
        );
    }
}