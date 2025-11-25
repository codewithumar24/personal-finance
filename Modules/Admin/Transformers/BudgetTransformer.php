<?php
// modules/Finance/Transformers/BudgetTransformer.php

namespace Modules\Admin\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetTransformer extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->budget_uuid,
            'amount' => (float) $this->amount,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
            'description' => $this->description,

            // Calculated fields
            'spent_amount' => $this->spent_amount,
            'remaining_amount' => $this->remaining_amount,
            'progress_percentage' => $this->progress_percentage,
            'is_exceeded' => $this->is_exceeded,
            'days_remaining' => $this->days_remaining,

            // Relationships
            'category' => new CategoryTransformer($this->whenLoaded('category')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
