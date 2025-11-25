<?php
// modules/Finance/Http/Requests/TransactionRequest.php

namespace Modules\Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Admin\DataTransfer\Requests\TransactionDTO;
use Modules\Admin\Enum\RecurringFrequency;
use Modules\Admin\Enum\TransactionType;

class TransactionRequest extends BaseRequest
{
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'wallet_id' => [
                'required', 
                'uuid',
                Rule::exists('wallets', 'wallet_uuid')->where('user_id', $userId)
            ],
            'category_id' => [
                'required', 
                'uuid',
                Rule::exists('categories', 'category_uuid')->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)->orWhere('is_default', true);
                })
            ],
            'type' => ['required', Rule::enum(TransactionType::class)],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:1000'],
            'from_wallet_id' => [
                'nullable', 
                'uuid',
                Rule::exists('wallets', 'wallet_uuid')->where('user_id', $userId),
                'required_if:type,transfer'
            ],
            'to_wallet_id' => [
                'nullable', 
                'uuid',
                Rule::exists('wallets', 'wallet_uuid')->where('user_id', $userId),
                'required_if:type,transfer',
                'different:from_wallet_id'
            ],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'is_recurring' => ['boolean'],
            'recurring_frequency' => ['nullable', 'required_if:is_recurring,true', Rule::enum(RecurringFrequency::class)],
            'recurring_end_date' => ['nullable', 'date', 'after:transaction_date'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'], // 5MB
        ];
    }

    public function messages(): array
    {
        return [
            'wallet_id.required' => 'Wallet is required',
            'wallet_id.exists' => 'Selected wallet does not exist',
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Selected category does not exist',
            'type.required' => 'Transaction type is required',
            'title.required' => 'Transaction title is required',
            'amount.required' => 'Amount is required',
            'amount.min' => 'Amount must be at least 0.01',
            'transaction_date.required' => 'Transaction date is required',
            'from_wallet_id.required_if' => 'From wallet is required for transfers',
            'to_wallet_id.required_if' => 'To wallet is required for transfers',
            'to_wallet_id.different' => 'From and to wallets must be different',
            'recurring_frequency.required_if' => 'Recurring frequency is required for recurring transactions',
        ];
    }

    public function getDTO(): TransactionDTO
    {
        return TransactionDTO::create(
            $this->input('wallet_id'),
            $this->input('category_id'),
            TransactionType::from($this->input('type')),
            $this->input('title'),
            (float) $this->input('amount'),
            $this->input('transaction_date'),
            $this->input('description'),
            $this->input('from_wallet_id'),
            $this->input('to_wallet_id'),
            $this->input('tags'),
            $this->boolean('is_recurring', false),
            $this->input('recurring_frequency') ? RecurringFrequency::from($this->input('recurring_frequency')) : null,
            $this->input('recurring_end_date'),
            $this->input('reference_number')
        );
    }
}