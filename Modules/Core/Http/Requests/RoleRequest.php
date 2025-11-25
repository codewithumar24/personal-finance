<?php
// modules/Core/Http/Requests/RoleRequest.php

namespace Modules\Core\Http\Requests;

use Modules\Core\DataTransfer\Requests\RoleDTO;

class RoleRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_default' => ['boolean'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id']
        ];
    }

    public function getDTO(): RoleDTO
    {
        return RoleDTO::create(
            $this->input('name'),
            $this->input('description'),
            $this->input('is_default', false),
            $this->input('permissions', [])
        );
    }
}