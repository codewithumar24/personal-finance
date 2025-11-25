<?php
// modules/Finance/Transformers/TransactionTransformer.php

namespace Modules\Admin\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionTransformer extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->transaction_uuid,
            'title' => $this->title,
            'description' => $this->description,
            'amount' => (float) $this->amount,
            'formatted_amount' => $this->formatted_amount,
            'signed_amount' => $this->signed_amount,
            'type' => $this->type->value,
            'type_label' => $this->type_label,
            'transaction_date' => $this->transaction_date,
            'reference_number' => $this->reference_number,
            'tags' => $this->tags,
            'is_recurring' => $this->is_recurring,
            'recurring_frequency' => $this->recurring_frequency?->value,
            'recurring_frequency_label' => $this->recurring_frequency?->label(),
            'recurring_end_date' => $this->recurring_end_date,
            'attachment' => $this->attachment_url,

            // Relationships
            'wallet' => new WalletTransformer($this->whenLoaded('wallet')),
            'category' => new CategoryTransformer($this->whenLoaded('category')),
            'from_wallet' => new WalletTransformer($this->whenLoaded('fromWallet')),
            'to_wallet' => new WalletTransformer($this->whenLoaded('toWallet')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
