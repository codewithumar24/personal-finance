<?php
// modules/Finance/Http/Requests/UpdateTransactionRequest.php

namespace Modules\Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Admin\DataTransfer\Requests\UpdateTransactionDTO;
use Modules\Admin\Enum\RecurringFrequency;
use Modules\Admin\Enum\TransactionType;

class UpdateTransactionRequest extends BaseRequest
{
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'wallet_id' => [
                'sometimes', 
                'uuid',
                Rule::exists('wallets', 'wallet_uuid')->where('user_id', $userId)
            ],
            'category_id' => [
                'sometimes', 
                'uuid',
                Rule::exists('categories', 'category_uuid')->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)->orWhere('is_default', true);
                })
            ],
            'type' => ['sometimes', Rule::enum(TransactionType::class)],
            'title' => ['sometimes', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'min:0.01', 'max:999999999.99'],
            'transaction_date' => ['sometimes', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:1000'],
            'from_wallet_id' => [
                'nullable', 
                'uuid',
                Rule::exists('wallets', 'wallet_uuid')->where('user_id', $userId)
            ],
            'to_wallet_id' => [
                'nullable', 
                'uuid',
                Rule::exists('wallets', 'wallet_uuid')->where('user_id', $userId),
                'different:from_wallet_id'
            ],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'is_recurring' => ['boolean'],
            'recurring_frequency' => ['nullable', Rule::enum(RecurringFrequency::class)],
            'recurring_end_date' => ['nullable', 'date'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
        ];
    }

    public function getDTO(): UpdateTransactionDTO
    {
        return UpdateTransactionDTO::create(
            $this->input('wallet_id'),
            $this->input('category_id'),
            $this->input('type') ? TransactionType::from($this->input('type')) : null,
            $this->input('title'),
            $this->input('amount') ? (float) $this->input('amount') : null,
            $this->input('transaction_date'),
            $this->input('description'),
            $this->input('from_wallet_id'),
            $this->input('to_wallet_id'),
            $this->input('tags'),
            $this->has('is_recurring') ? $this->boolean('is_recurring') : null,
            $this->input('recurring_frequency') ? RecurringFrequency::from($this->input('recurring_frequency')) : null,
            $this->input('recurring_end_date'),
            $this->input('reference_number')
        );
    }
}