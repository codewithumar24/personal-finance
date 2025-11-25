<?php
// modules/Finance/Http/Requests/WalletRequest.php

namespace Modules\Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\Admin\DataTransfer\Requests\WalletDTO;
use Modules\Admin\Enum\WalletType;

class WalletRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(WalletType::class)],
            'currency' => ['required', 'string', 'size:3'],
            'balance' => 'required|numeric|min:0', // Make sure this rule exists
            'account_number' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'is_default' => ['boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Wallet name is required',
            'type.required' => 'Wallet type is required',
            'currency.required' => 'Currency is required',
        ];
    }

    public function getDTO(): WalletDTO
    {
        return WalletDTO::create(
            $this->input('name'),
            WalletType::from($this->input('type')),
            $this->input('currency', 'USD'),
            $this->input('balance'), // Make sure this is passed
            $this->input('account_number'),
            $this->input('bank_name'),
            $this->boolean('is_default', false),
            $this->input('description')
        );
    }
}
