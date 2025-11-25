<?php
// modules/Core/Http/Requests/BaseRequest.php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    abstract public function rules(): array;

    public function getPerPage(): ?int
    {
        return $this->input('per_page', 15);
    }
}
