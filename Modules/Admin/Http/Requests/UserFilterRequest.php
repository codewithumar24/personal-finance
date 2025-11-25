<?php
// modules/Admin/Http/Requests/UserFilterRequest.php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class UserFilterRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'exists:roles,name'],
            'is_active' => ['nullable', 'boolean'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'sort_by' => ['nullable', 'string', 'in:name,email,created_at,last_login_at'],
            'sort_order' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

    public function getFilters(): array
    {
        return $this->only([
            'search', 'role', 'is_active', 'date_from', 'date_to', 'sort_by', 'sort_order'
        ]);
    }
}