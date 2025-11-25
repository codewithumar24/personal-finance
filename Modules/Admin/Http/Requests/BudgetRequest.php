<?php
// modules/Finance/Http/Requests/BudgetRequest.php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Admin\DataTransfer\Requests\BudgetDTO;

class BudgetRequest extends BaseRequest
{
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'category_id' => [
                'required',
                'uuid',
                'exists:categories,category_uuid'
            ],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Selected category does not exist',
            'amount.required' => 'Budget amount is required',
            'start_date.required' => 'Start date is required',
            'end_date.required' => 'End date is required',
            'end_date.after' => 'End date must be after start date',
        ];
    }

    public function getDTO(): BudgetDTO
    {
        return BudgetDTO::create(
            $this->input('category_id'),
            (float) $this->input('amount'),
            $this->input('start_date'),
            $this->input('end_date'),
            $this->input('description'),
            $this->boolean('is_active', true)
        );
    }
}
