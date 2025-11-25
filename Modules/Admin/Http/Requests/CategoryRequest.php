<?php
// modules/Finance/Http/Requests/CategoryRequest.php

namespace Modules\Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Admin\DataTransfer\Requests\CategoryDTO;
use Modules\Admin\Enum\CategoryType;

class CategoryRequest extends BaseRequest
{
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('categories')->where(function ($query) use ($userId) {
                    return $query->where('user_id', $userId)
                                 ->where('type', $this->input('type'));
                })->ignore($this->route('category'))
            ],
            'type' => ['required', Rule::enum(CategoryType::class)],
            'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'icon' => ['nullable', 'string', 'max:10'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required',
            'name.unique' => 'You already have a category with this name and type',
            'type.required' => 'Category type is required',
            'color.regex' => 'Color must be a valid hex color code',
        ];
    }

    public function getDTO(): CategoryDTO
    {
        return CategoryDTO::create(
            $this->input('name'),
            CategoryType::from($this->input('type')),
            $this->input('color', '#6B7280'),
            $this->input('icon'),
            $this->boolean('is_active', true),
            $this->input('sort_order', 0),
            $this->input('description')
        );
    }
}