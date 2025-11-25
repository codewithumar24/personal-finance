<?php
// modules/Finance/Transformers/WalletTransformer.php

namespace Modules\Admin\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransformer extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->wallet_uuid,
            'name' => $this->name,
            'type' => $this->type->value,
            'type_label' => $this->type_label,
            'balance' => (float) $this->balance,
            'formatted_balance' => $this->formatted_balance,
            'currency' => $this->currency,
            'account_number' => $this->account_number,
            'bank_name' => $this->bank_name,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
