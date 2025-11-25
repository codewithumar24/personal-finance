<?php

namespace Modules\Core\Http\Requests;

class FilterRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'search' => ['sometimes', 'string', 'max:255'],
            'sort_by' => ['sometimes', 'string'],
            'sort_order' => ['sometimes', 'in:asc,desc'],
            'filters' => ['sometimes', 'array'],
        ];
    }

    public function getPerPage(): ?int
    {
        return $this->input('per_page', 15);
    }

    public function getPage(): int
    {
        return $this->input('page', 1);
    }

    public function getSearch(): ?string
    {
        return $this->input('search');
    }

    public function getSortBy(): ?string
    {
        return $this->input('sort_by', 'created_at');
    }

    public function getSortOrder(): string
    {
        return $this->input('sort_order', 'desc');
    }

    public function getFilters(): array
    {
        return $this->input('filters', []);
    }
}
