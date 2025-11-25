<?php
// modules/Admin/Http/Requests/AssignRoleRequest.php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Admin\DataTransfer\Requests\AssignRoleDTO;

class AssignRoleRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'roles.required' => 'At least one role is required',
            'roles.*.exists' => 'Selected role does not exist',
        ];
    }

    public function getDTO(): AssignRoleDTO
    {
        return AssignRoleDTO::create($this->input('roles'));
    }
}