<?php
// modules/Finance/Http/Requests/TransactionFilterRequest.php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Admin\Enum\TransactionType;

class TransactionFilterRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'in:income,expense,transfer'],
            'wallet_id' => ['nullable', 'uuid'],
            'category_id' => ['nullable', 'uuid'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'amount_min' => ['nullable', 'numeric', 'min:0'],
            'amount_max' => ['nullable', 'numeric', 'min:0'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'is_recurring' => ['nullable', 'boolean'],
            'sort_by' => ['nullable', 'string', 'in:amount,transaction_date,created_at,title'],
            'sort_order' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

    public function getFilters(): array
    {
        return $this->only([
            'search', 'type', 'wallet_id', 'category_id', 'date_from', 'date_to',
            'amount_min', 'amount_max', 'tags', 'is_recurring', 'sort_by', 'sort_order'
        ]);
    }
}
